import { Component, inject, signal, OnInit } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';

import {
  ResultadoOperacionAviso,
  TareasService,
} from '../../core/services/avisos.service';
import { ClientesService } from '../../core/services/clientes.service';
import { AuthService } from '../../core/services/auth.service';
import { AlertService } from '../../core/services/alert.service';
import { Tarea } from '../../core/interfaces/avisos.interfaces';

@Component({
  selector: 'app-avisos',
  imports: [DatePipe, ReactiveFormsModule],
  templateUrl: './avisos.html', // Aquí enlazamos con el archivo HTML separado
})
export default class Avisos implements OnInit {
  public tareasService = inject(TareasService);
  public clientesService = inject(ClientesService);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private fb = inject(FormBuilder);

  public mostrarFormulario = signal(false);
  public idAvisoEditando = signal<number | null>(null);
  public avisoAsignando = signal<Tarea | null>(null);
  public authService = inject(AuthService);
  public alertService = inject(AlertService);

  public textoBusqueda = signal<string>('');

  actualizarBusqueda(event: Event) {
    const input = event.target as HTMLInputElement;
    this.textoBusqueda.set(input.value);
  }

  public avisoForm = this.fb.group({
    descripcion: this.fb.nonNullable.control('', [Validators.required, Validators.minLength(5)]),
    importancia: this.fb.nonNullable.control('Normal', [Validators.required]),
    estado: this.fb.nonNullable.control('Pendiente'),
    id_cliente: [null as number | null, [Validators.required]],
    persona_contacto: this.fb.nonNullable.control(''),
    telefono_contacto: this.fb.nonNullable.control(''),
  });

  public asignacionForm = this.fb.group({
    id_empleado: [null as number | null, [Validators.required]],
  });

  esTecnico(): boolean {
    const rol = this.authService.usuarioActual()?.rol_nombre;
    return rol === 'Tecnico' || rol === 'Técnico' || rol === 'TÃ©cnico';
  }

  esAtencionCliente(): boolean {
    const rol = this.authService.usuarioActual()?.rol_nombre;
    return rol === 'Atencion al Cliente' || rol === 'Atención al Cliente' || rol === 'AtenciÃ³n al Cliente';
  }

  esAdministrador(): boolean {
    return this.authService.usuarioActual()?.rol_nombre === 'Administrador';
  }

  puedeEditarAviso(): boolean {
    return this.esAdministrador() || this.esAtencionCliente();
  }

  puedeAsignarOReasignar(tarea: Tarea): boolean {
    if (this.esAdministrador() || this.esAtencionCliente()) {
      return true;
    }

    const usuario = this.authService.usuarioActual();
    return (
      this.esTecnico()
      && !!usuario?.id_empleado
      && tarea.id_empleado !== null
      && tarea.id_empleado !== undefined
      && Number(tarea.id_empleado) === Number(usuario.id_empleado)
      && !this.avisoCerrado(tarea)
    );
  }

  puedeCogerAviso(tarea: Tarea): boolean {
    return (
      this.esTecnico()
      && (tarea.id_empleado === null || tarea.id_empleado === undefined)
      && !this.avisoCerrado(tarea)
    );
  }

  puedeCancelarAviso(tarea: Tarea): boolean {
    if (tarea.estado === 'Cancelada' || tarea.estado === 'Finalizada') return false;

    const usuario = this.authService.usuarioActual();
    if (!usuario) return false;

    if (usuario.rol_nombre === 'Administrador' || this.esAtencionCliente()) {
      return true;
    }

    return this.esTecnico() && Number(tarea.id_empleado) === Number(usuario.id_empleado);
  }

  ngOnInit() {
    //Cargar los avisos desde PHP
    this.tareasService.cargarTareas();

    //Cargar los clientes
    this.clientesService.cargarClientes();

    // Cargar solo los empleados que el backend permite usar en asignaciones.
    if (this.esAdministrador() || this.esAtencionCliente() || this.esTecnico()) {
      void this.cargarEmpleadosAsignables();
    }

    this.route.queryParams.subscribe(params => {
      if (params['cliente_id']) {
        const idCliente = Number(params['cliente_id']);
        this.avisoForm.patchValue({ id_cliente: idCliente });
        this.mostrarFormulario.set(true);
      }
    });
  }

  //FORMULARIO DE EDICION
  abrirEditar(tarea: Tarea) {
    if (!tarea.id_tarea) return;

    this.cerrarAsignacion();
    this.idAvisoEditando.set(tarea.id_tarea);
    this.avisoForm.patchValue({
      descripcion: tarea.descripcion,
      importancia: tarea.importancia,
      estado: tarea.estado,
      id_cliente: tarea.id_cliente,
      persona_contacto: tarea.persona_contacto,
      telefono_contacto: tarea.telefono_contacto,
    });
    this.mostrarFormulario.set(true);
  }

  toggleFormulario() {
    this.mostrarFormulario.update(valor => !valor);
    if (!this.mostrarFormulario()) {
      this.reiniciarFormularioAviso();
    } else {
      this.cerrarAsignacion();
    }
  }

  get avisosFiltrados() {
    const usuario = this.authService.usuarioActual();

    // Si no hay usuario o no tiene rol, no ve nada
    if (!usuario || !usuario.rol_nombre) return [];

    let avisos = this.tareasService.tareas();

    // Administrador y Atención al Cliente ven todos
    if (usuario.rol_nombre === 'Administrador' || this.esAtencionCliente()) {
      // Mantienen la lista completa.
    }

    // Técnico solo ve los suyos y los no asignados
    else if (this.esTecnico()) {
      avisos = avisos.filter(aviso =>
        aviso.id_empleado === usuario.id_empleado || aviso.id_empleado === null
      );
    } else {
      return [];
    }

    const busqueda = this.textoBusqueda().toLowerCase().trim();
    if (!busqueda) return avisos;

    return avisos.filter(aviso =>
      aviso.descripcion?.toLowerCase().includes(busqueda) ||
      aviso.cliente_nombre?.toLowerCase().includes(busqueda) ||
      aviso.estado?.toLowerCase().includes(busqueda) ||
      aviso.tecnico_nombre?.toLowerCase().includes(busqueda)
    );
  }

  async guardarAviso() {
    if (this.avisoForm.invalid) return;

    const datosFormulario = this.avisoForm.getRawValue();
    if (datosFormulario.id_cliente === null) return;

    const datosGenerales = {
      descripcion: datosFormulario.descripcion,
      importancia: datosFormulario.importancia,
      estado: datosFormulario.estado,
      id_cliente: datosFormulario.id_cliente,
      persona_contacto: datosFormulario.persona_contacto,
      telefono_contacto: datosFormulario.telefono_contacto,
    };

    let exito = false;

    if (this.idAvisoEditando()) {

      exito = await this.tareasService.actualizarTarea(this.idAvisoEditando()!, datosGenerales);
    } else {

      exito = await this.tareasService.agregarTarea({
        ...datosGenerales,
        estado: 'Pendiente',
      });
    }

    if (exito) {
      this.toggleFormulario();
      this.alertService.mostrar('¡Guardado!', 'El aviso se ha guardado correctamente.', 'success');
    } else {
      // alert("Hubo un error al guardar el aviso.");
      this.alertService.mostrar('Error', 'Hubo un error al guardar el aviso en el servidor.', 'error');
    }
  }

  abrirAsignacion(tarea: Tarea) {
    if (!this.puedeAsignarOReasignar(tarea)) {
      this.alertService.mostrar(
        'Sin permisos',
        'No tienes permisos para cambiar la asignación de este aviso.',
        'error',
      );
      return;
    }

    if (this.mostrarFormulario()) {
      this.mostrarFormulario.set(false);
      this.reiniciarFormularioAviso();
    }

    this.avisoAsignando.set(tarea);
    this.asignacionForm.reset({ id_empleado: null });
  }

  cerrarAsignacion() {
    this.avisoAsignando.set(null);
    this.asignacionForm.reset({ id_empleado: null });
  }

  empleadosDestino(tarea: Tarea) {
    return this.tareasService
      .empleadosAsignables()
      .filter(empleado => Number(empleado.id_empleado) !== Number(tarea.id_empleado));
  }

  guardarAsignacion() {
    const tarea = this.avisoAsignando();
    const idEmpleado = this.asignacionForm.controls.id_empleado.value;
    if (!tarea?.id_tarea || !idEmpleado || this.asignacionForm.invalid) return;

    const ejecutarAsignacion = async () => {
      const resultado = await this.tareasService.asignarAviso(tarea.id_tarea!, idEmpleado);
      if (resultado.exito) {
        const esReasignacion = tarea.id_empleado !== null && tarea.id_empleado !== undefined;
        this.cerrarAsignacion();
        this.alertService.mostrar(
          esReasignacion ? 'Reasignado' : 'Asignado',
          esReasignacion
            ? 'El aviso se ha reasignado correctamente.'
            : 'El aviso se ha asignado correctamente.',
          'success',
        );
      } else {
        this.mostrarErrorOperacion(
          resultado,
          'No se ha podido actualizar la asignación del aviso.',
        );
      }
    };

    if (tarea.id_empleado !== null && tarea.id_empleado !== undefined) {
      this.alertService.confirmar(
        '¿Reasignar aviso?',
        '¿Confirmas que deseas asignar este aviso a otro técnico?',
        ejecutarAsignacion,
      );
      return;
    }

    void ejecutarAsignacion();
  }

  async cogerAviso(tarea: Tarea) {
    if (!tarea.id_tarea || !this.puedeCogerAviso(tarea)) {
      this.alertService.mostrar('Sin permisos', 'No puedes coger este aviso.', 'error');
      return;
    }

    const resultado = await this.tareasService.cogerAviso(tarea.id_tarea);
    if (resultado.exito) {
      this.alertService.mostrar('Aviso asignado', 'Has cogido el aviso correctamente.', 'success');
    } else {
      this.mostrarErrorOperacion(resultado, 'No se ha podido coger el aviso.');
    }
  }

  irACrearAlbaran(idTarea: number) {
    this.router.navigate(['/albaranes'], { queryParams: { aviso_id: idTarea } });
  }

  cancelarAviso(idTarea: number) {
    const tarea = this.tareasService.tareas().find(item => item.id_tarea === idTarea);
    if (!tarea || !this.puedeCancelarAviso(tarea)) {
      this.alertService.mostrar('Sin permisos', 'No puedes cancelar este aviso.', 'error');
      return;
    }

    this.alertService.confirmar(
      '¿Cancelar Aviso?',
      '¿Estás seguro de que deseas cancelar este aviso?.',
      async () => {

        const resultado = await this.tareasService.cancelarTarea(idTarea);
        if (resultado.exito) {
          this.alertService.mostrar('Cancelado', 'El aviso ha sido cancelado.', 'info');
        } else {
          this.mostrarErrorOperacion(resultado, 'No se ha podido cancelar el aviso.');
        }
      }
    );
  }

  private avisoCerrado(tarea: Tarea): boolean {
    return tarea.estado === 'Cancelada' || tarea.estado === 'Finalizada';
  }

  private reiniciarFormularioAviso() {
    this.avisoForm.reset({
      descripcion: '',
      importancia: 'Normal',
      estado: 'Pendiente',
      id_cliente: null,
      persona_contacto: '',
      telefono_contacto: '',
    });
    this.idAvisoEditando.set(null);
  }

  private async cargarEmpleadosAsignables() {
    const resultado = await this.tareasService.cargarEmpleadosAsignables();
    if (!resultado.exito) {
      this.mostrarErrorOperacion(
        resultado,
        'No se ha podido cargar la lista de técnicos asignables.',
      );
    }
  }

  private mostrarErrorOperacion(
    resultado: ResultadoOperacionAviso,
    mensajePredeterminado: string,
  ) {
    if (resultado.estadoHttp === 403) {
      this.alertService.mostrar(
        'Sin permisos',
        'No tienes permisos para realizar esta acción.',
        'error',
      );
      return;
    }

    if (resultado.estadoHttp === 500 || resultado.estadoHttp === 0) {
      this.alertService.mostrar(
        'Error',
        'No se ha podido completar la operación. Inténtalo de nuevo más tarde.',
        'error',
      );
      return;
    }

    this.alertService.mostrar('Error', mensajePredeterminado, 'error');
  }
}
