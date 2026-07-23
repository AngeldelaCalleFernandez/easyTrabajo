import { Component, inject, signal, OnInit } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';

import { TareasService } from '../../core/services/avisos.service';
import { ClientesService } from '../../core/services/clientes.service';
import { AdminService } from '../../core/services/admin.service';
import { AuthService } from '../../core/services/auth.service';
import { AlertService } from '../../core/services/alert.service';

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
  public adminService = inject(AdminService);
  public authService = inject(AuthService);
  public alertService = inject(AlertService);

  public textoBusqueda = signal<string>('');

  actualizarBusqueda(event: Event) {
    const input = event.target as HTMLInputElement;
    this.textoBusqueda.set(input.value);
  }

  public avisoForm = this.fb.group({
    descripcion: ['', [Validators.required, Validators.minLength(5)]],
    importancia: ['Normal', [Validators.required]],
    estado: ['Pendiente'],
    id_cliente: [null as number | null, [Validators.required]],
    persona_contacto: [''],
    telefono_contacto: [''],
    id_empleado: [null]
  });

  esTecnico(): boolean {
    const rol = this.authService.usuarioActual()?.rol_nombre;
    return rol === 'Tecnico' || rol === 'Técnico' || rol === 'TÃ©cnico';
  }

  esAtencionCliente(): boolean {
    const rol = this.authService.usuarioActual()?.rol_nombre;
    return rol === 'Atencion al Cliente' || rol === 'Atención al Cliente' || rol === 'AtenciÃ³n al Cliente';
  }

  puedeCancelarAviso(tarea: any): boolean {
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

    //Carga a los empleados
    if (!this.esTecnico()) {
      this.adminService.cargarEmpleados();
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
  abrirEditar(tarea: any) {
    this.idAvisoEditando.set(tarea.id_tarea);
    this.avisoForm.patchValue({
      descripcion: tarea.descripcion,
      importancia: tarea.importancia,
      id_cliente: tarea.id_cliente,
      persona_contacto: tarea.persona_contacto,
      telefono_contacto: tarea.telefono_contacto,
      id_empleado: tarea.id_empleado
    });
    this.mostrarFormulario.set(true);
  }

  toggleFormulario() {
    this.mostrarFormulario.update(valor => !valor);
    if (!this.mostrarFormulario()) {
      this.avisoForm.reset({ importancia: 'Normal', id_cliente: null, id_empleado: null });
      this.idAvisoEditando.set(null);
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

    const datosFormulario: any = { ...this.avisoForm.value };
    let exito = false;

    if (this.idAvisoEditando()) {

      exito = await this.tareasService.actualizarTarea(this.idAvisoEditando()!, datosFormulario);
    } else {

      const estadoInicial = datosFormulario.id_empleado ? 'En proceso' : 'Pendiente';
      exito = await this.tareasService.agregarTarea({ ...datosFormulario, estado: estadoInicial });
    }

    if (exito) {
      this.toggleFormulario();
      this.alertService.mostrar('¡Guardado!', 'El aviso se ha guardado correctamente.', 'success');
    } else {
      // alert("Hubo un error al guardar el aviso.");
      this.alertService.mostrar('Error', 'Hubo un error al guardar el aviso en el servidor.', 'error');
    }
  }

  cogerAviso(idTarea: number) {
    const usuario = this.authService.usuarioActual();
    if (usuario && usuario.id_empleado) {
      this.tareasService.asignarTarea(idTarea, usuario.id_empleado);
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

        const exito = await this.tareasService.cancelarTarea(idTarea);
        if (exito) {
          this.alertService.mostrar('Cancelado', 'El aviso ha sido cancelado.', 'info');
        } else {
          this.alertService.mostrar('Error', 'No se ha podido cancelar el aviso.', 'error');
        }
      }
    );
  }
}
