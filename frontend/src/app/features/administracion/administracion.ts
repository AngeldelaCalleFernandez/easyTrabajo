import { Component, inject, signal, OnInit } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AdminService } from '../../core/services/admin.service';
import { AlertService } from '../../core/services/alert.service';

@Component({
  selector: 'app-administracion',
  imports: [ReactiveFormsModule],
  templateUrl: './administracion.html',
})
export default class Administracion implements OnInit {
  public adminService = inject(AdminService);
  public alertService = inject(AlertService);
  private fb = inject(FormBuilder);

  // Control de interfaz
  public pestanaActiva = signal<'empleados' | 'usuarios'>('empleados');
  public mostrarFormulario = signal(false);
  public textoBusquedaEmpleados = signal<string>('');
  public textoBusquedaUsuarios = signal<string>('');

  // Variables para saber si estamos editando
  public idEmpleadoEditando = signal<number | null>(null);
  public idUsuarioEditando = signal<number | null>(null);

  // Formulario de Empleados
  public empleadoForm = this.fb.group({
    nombre: ['', [Validators.required]],
    apellido: ['', [Validators.required]],
    apellido_2: [''],
    nif: [''],
    movil: ['']
  });

  // Formulario de Usuarios
  public usuarioForm = this.fb.group({
    nombre: ['', [Validators.required]],
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(4)]],
    id_rol: [null as number | null, [Validators.required]],
    id_empleado: [null as number | null]
  });

  ngOnInit() {
    this.adminService.cargarRoles();
    this.adminService.cargarEmpleados();
    this.adminService.cargarUsuarios();
  }

  actualizarBusquedaEmpleados(event: Event) {
    const input = event.target as HTMLInputElement;
    this.textoBusquedaEmpleados.set(input.value);
  }

  actualizarBusquedaUsuarios(event: Event) {
    const input = event.target as HTMLInputElement;
    this.textoBusquedaUsuarios.set(input.value);
  }

  get empleadosFiltrados() {
    const busqueda = this.textoBusquedaEmpleados().toLowerCase().trim();
    const empleados = this.adminService.empleados();

    if (!busqueda) return empleados;

    return empleados.filter(empleado =>
      empleado.nombre?.toLowerCase().includes(busqueda) ||
      empleado.apellido?.toLowerCase().includes(busqueda) ||
      empleado.apellido_2?.toLowerCase().includes(busqueda) ||
      empleado.nif?.toLowerCase().includes(busqueda) ||
      empleado.movil?.toLowerCase().includes(busqueda) ||
      empleado.id_empleado?.toString().includes(busqueda)
    );
  }

  get usuariosFiltrados() {
    const busqueda = this.textoBusquedaUsuarios().toLowerCase().trim();
    const usuarios = this.adminService.usuarios();

    if (!busqueda) return usuarios;

    return usuarios.filter(usuario =>
      usuario.nombre?.toLowerCase().includes(busqueda) ||
      usuario.email?.toLowerCase().includes(busqueda) ||
      usuario.rol_nombre?.toLowerCase().includes(busqueda) ||
      usuario.empleado_nombre?.toLowerCase().includes(busqueda) ||
      usuario.id_usuario?.toString().includes(busqueda)
    );
  }

  cambiarPestana(pestana: 'empleados' | 'usuarios') {
    this.pestanaActiva.set(pestana);
    this.mostrarFormulario.set(false);
    this.idEmpleadoEditando.set(null);
    this.idUsuarioEditando.set(null);
  }

  toggleFormulario() {
    this.mostrarFormulario.update(v => !v);
    if (!this.mostrarFormulario()) {
      this.empleadoForm.reset();
      this.usuarioForm.reset({ id_rol: null, id_empleado: null });
      this.idEmpleadoEditando.set(null);
      this.idUsuarioEditando.set(null);

      // Restauramos la validación de contraseña por si acaso
      this.usuarioForm.get('password')?.setValidators([Validators.required, Validators.minLength(4)]);
      this.usuarioForm.get('password')?.updateValueAndValidity();
    }
  }

  // --- FUNCIONES DE EMPLEADO ---

  editarEmpleado(empleado: any) {
    this.idEmpleadoEditando.set(empleado.id_empleado);
    this.empleadoForm.patchValue({
      nombre: empleado.nombre,
      apellido: empleado.apellido,
      apellido_2: empleado.apellido_2,
      nif: empleado.nif,
      movil: empleado.movil
    });
    this.mostrarFormulario.set(true);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  borrarEmpleado(id: number) {
    this.alertService.confirmar(
      '¿Dar de baja al empleado?',
      'El empleado ya no podrá acceder al sistema ni se le podrán asignar avisos. Sus historiales se mantendrán.',
      async () => {
        const exito = await this.adminService.eliminarEmpleado(id);
        if (exito) {
          this.alertService.mostrar('Eliminado', 'Empleado dado de baja con éxito.', 'info');
        } else {
          this.alertService.mostrar('Error', 'No se pudo dar de baja al empleado.', 'error');
        }
      }
    );
  }

  async guardarEmpleado() {
    if (this.empleadoForm.invalid) return;

    let exito = false;
    if (this.idEmpleadoEditando()) {
      exito = await this.adminService.actualizarEmpleado(this.idEmpleadoEditando()!, this.empleadoForm.value);
    } else {
      exito = await this.adminService.agregarEmpleado(this.empleadoForm.value);
    }

    if (exito) {
      this.toggleFormulario();
      this.alertService.mostrar('¡Guardado!', 'El empleado se ha guardado correctamente.', 'success');
    } else {
      this.alertService.mostrar('Error', 'Error al guardar el empleado.', 'error');
    }
  }

  // --- FUNCIONES DE USUARIO ---

editarUsuario(usuario: any) {
    this.idUsuarioEditando.set(usuario.id_usuario);

    this.usuarioForm.patchValue({
      nombre: usuario.nombre,
      email: usuario.email,
      id_rol: usuario.id_rol ? Number(usuario.id_rol) : null,
      id_empleado: usuario.id_empleado ? Number(usuario.id_empleado) : null
    });

    this.usuarioForm.get('password')?.clearValidators();
    this.usuarioForm.get('password')?.updateValueAndValidity();

    this.mostrarFormulario.set(true);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  borrarUsuario(id: number) {
    this.alertService.confirmar(
      '¿Revocar acceso?',
      '¿Estás seguro de que deseas eliminar este usuario? Perderá su acceso al sistema.',
      async () => {
        const exito = await this.adminService.eliminarUsuario(id);
        if (exito) {
          this.alertService.mostrar('Acceso Revocado', 'El usuario ha sido eliminado.', 'info');
        } else {
          this.alertService.mostrar('Error', 'No se pudo eliminar el usuario.', 'error');
        }
      }
    );
  }

  async guardarUsuario() {
    if (this.usuarioForm.invalid) return;

    let exito = false;
    if (this.idUsuarioEditando()) {
      exito = await this.adminService.actualizarUsuario(this.idUsuarioEditando()!, this.usuarioForm.value);
    } else {
      exito = await this.adminService.agregarUsuario(this.usuarioForm.value);
    }

    if (exito) {
      this.toggleFormulario();
      this.alertService.mostrar('¡Guardado!', 'El usuario se ha guardado correctamente.', 'success');
    } else {
      this.alertService.mostrar('Error', 'Error al guardar el usuario. Es posible que el correo ya exista.', 'error');
    }
  }
}
