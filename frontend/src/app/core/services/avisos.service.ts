import { inject, Injectable, signal } from '@angular/core';
import { Tarea } from '../interfaces/avisos.interfaces';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { API_BASE_URL } from '../config/api.config';

export interface EmpleadoAsignable {
  id_empleado: number;
  nombre: string;
  apellidos: string;
}

export interface ResultadoOperacionAviso {
  exito: boolean;
  estadoHttp?: number;
}

type DatosGeneralesAviso = Partial<
  Pick<
    Tarea,
    | 'descripcion'
    | 'importancia'
    | 'estado'
    | 'id_cliente'
    | 'persona_contacto'
    | 'telefono_contacto'
  >
>;

@Injectable({
  providedIn: 'root',
})
export class TareasService {
  private http = inject(HttpClient);
  private apiUrl = API_BASE_URL;

  public tareas = signal<Tarea[]>([]);
  public empleadosAsignables = signal<EmpleadoAsignable[]>([]);

  //OBTENER AVISOS
  cargarTareas() {
    this.http.get<Tarea[]>(`${this.apiUrl}/avisos`).subscribe({
      next: (datosReales) => this.tareas.set(datosReales),
      error: () => console.error('No se han podido cargar los avisos.'),
    });
  }

  async cargarTareasAsync(): Promise<void> {
    try {
      const datosReales = await firstValueFrom(this.http.get<Tarea[]>(`${this.apiUrl}/avisos`));
      this.tareas.set(datosReales);
    } catch {
      console.error('No se han podido cargar los avisos.');
    }
  }

  async cargarEmpleadosAsignables(): Promise<ResultadoOperacionAviso> {
    try {
      const empleados = await firstValueFrom(
        this.http.get<EmpleadoAsignable[]>(`${this.apiUrl}/avisos/empleados-asignables`),
      );
      this.empleadosAsignables.set(empleados);
      return { exito: true };
    } catch (error: unknown) {
      this.empleadosAsignables.set([]);
      console.error('No se han podido cargar los empleados asignables.');
      return this.resultadoError(error);
    }
  }

  //CREAR AVISO
  async agregarTarea(nuevaTarea: DatosGeneralesAviso): Promise<boolean> {
    try {
      await firstValueFrom(
        this.http.post(`${this.apiUrl}/avisos`, this.datosGenerales(nuevaTarea)),
      );
      this.cargarTareas();
      return true;
    } catch {
      console.error('No se ha podido guardar el aviso.');
      return false;
    }
  }

  //ACTUALIZAR AVISO
  async actualizarTarea(id: number, datosTarea: DatosGeneralesAviso): Promise<boolean> {
    try {
      await firstValueFrom(
        this.http.put(`${this.apiUrl}/avisos/${id}`, this.datosGenerales(datosTarea)),
      );
      this.cargarTareas();
      return true;
    } catch {
      console.error('No se ha podido actualizar el aviso.');
      return false;
    }
  }

  async asignarAviso(idAviso: number, idEmpleado: number): Promise<ResultadoOperacionAviso> {
    try {
      await firstValueFrom(
        this.http.put(`${this.apiUrl}/avisos/${idAviso}/asignar`, {
          id_empleado: idEmpleado,
        }),
      );
      this.cargarTareas();
      return { exito: true };
    } catch (error: unknown) {
      console.error('No se ha podido actualizar la asignación del aviso.');
      return this.resultadoError(error);
    }
  }

  async cogerAviso(idAviso: number): Promise<ResultadoOperacionAviso> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/avisos/${idAviso}/coger`, {}));
      this.cargarTareas();
      return { exito: true };
    } catch (error: unknown) {
      console.error('No se ha podido coger el aviso.');
      return this.resultadoError(error);
    }
  }

  //ESTADO DEL AVISO
  async finalizarTarea(idTarea: number) {
    await this.actualizarTarea(idTarea, { estado: 'Finalizada' });
  }

  async cancelarTarea(idTarea: number): Promise<ResultadoOperacionAviso> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/avisos/${idTarea}/cancelar`, {}));
      this.cargarTareas();
      return { exito: true };
    } catch (error: unknown) {
      console.error('No se ha podido cancelar el aviso.');
      return this.resultadoError(error);
    }
  }

  private datosGenerales(datos: DatosGeneralesAviso): DatosGeneralesAviso {
    return {
      descripcion: datos.descripcion,
      importancia: datos.importancia,
      estado: datos.estado,
      id_cliente: datos.id_cliente,
      persona_contacto: datos.persona_contacto,
      telefono_contacto: datos.telefono_contacto,
    };
  }

  private resultadoError(error: unknown): ResultadoOperacionAviso {
    return {
      exito: false,
      estadoHttp: error instanceof HttpErrorResponse ? error.status : undefined,
    };
  }
}
