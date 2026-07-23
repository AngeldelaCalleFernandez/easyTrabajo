import { inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { Empleado, Rol, UsuarioAdmin } from '../interfaces/admin.interfaces';
import { API_BASE_URL } from '../config/api.config';

@Injectable({
  providedIn: 'root'
})
export class AdminService {
  private http = inject(HttpClient);
  private apiUrl = API_BASE_URL;

  public roles = signal<Rol[]>([]);
  public empleados = signal<Empleado[]>([]);
  public usuarios = signal<UsuarioAdmin[]>([]);

  // CARGAR DATOS
  cargarRoles() {
    this.http.get<Rol[]>(`${this.apiUrl}/roles`).subscribe(res => this.roles.set(res));
  }

  cargarEmpleados() {
    this.http.get<Empleado[]>(`${this.apiUrl}/empleados`).subscribe(res => this.empleados.set(res));
  }

  cargarUsuarios() {
    this.http.get<UsuarioAdmin[]>(`${this.apiUrl}/usuarios`).subscribe(res => this.usuarios.set(res));
  }

  // GUARDAR DATOS
  async agregarEmpleado(datos: any): Promise<boolean> {
    try {
      const res: any = await firstValueFrom(this.http.post(`${this.apiUrl}/empleados`, datos));
      this.empleados.update(actuales => [res.empleado, ...actuales]);
      return true;
    } catch (error) {
      console.error("Error al guardar empleado:", error);
      return false;
    }
  }

  async agregarUsuario(datos: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.post(`${this.apiUrl}/usuarios`, datos));
      this.cargarUsuarios();
      return true;
    } catch (error) {
      console.error("Error al guardar usuario:", error);
      return false;
    }
  }

  // ACTUALIZAR DATOS
  async actualizarEmpleado(id: number, datos: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/empleados/${id}`, datos));
      this.cargarEmpleados();
      return true;
    } catch (error) {
      console.error("Error al actualizar empleado:", error);
      return false;
    }
  }

  async actualizarUsuario(id: number, datos: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/usuarios/${id}`, datos));
      this.cargarUsuarios();
      return true;
    } catch (error) {
      console.error("Error al actualizar usuario:", error);
      return false;
    }
  }

  // ELIMINAR DATOS (Soft Delete)
  async eliminarEmpleado(id: number): Promise<boolean> {
    try {
      await firstValueFrom(this.http.delete(`${this.apiUrl}/empleados/${id}`));
      this.cargarEmpleados();
      return true;
    } catch (error) {
      console.error("Error al borrar empleado:", error);
      return false;
    }
  }

  async eliminarUsuario(id: number): Promise<boolean> {
    try {
      await firstValueFrom(this.http.delete(`${this.apiUrl}/usuarios/${id}`));
      this.cargarUsuarios();
      return true;
    } catch (error) {
      console.error("Error al borrar usuario:", error);
      return false;
    }
  }
}
