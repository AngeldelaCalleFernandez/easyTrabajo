import { inject, Injectable, signal } from '@angular/core';
import { Tarea } from '../interfaces/avisos.interfaces';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { API_BASE_URL } from '../config/api.config';

@Injectable({
  providedIn: 'root',
})
export class TareasService {
  private http=inject(HttpClient);
  private apiUrl = API_BASE_URL;

  public tareas = signal<Tarea[]>([]);

  //OBTENER AVISOS
  cargarTareas(){
    this.http.get<Tarea[]>(`${this.apiUrl}/avisos`).subscribe({
      next:(datosReales)=>this.tareas.set(datosReales),
      error:(error)=>console.error("Error de cargar las tareas",error)
    });
  }

  async cargarTareasAsync(): Promise<void> {
    try {
      const datosReales = await firstValueFrom(this.http.get<Tarea[]>(`${this.apiUrl}/avisos`));
      this.tareas.set(datosReales);
    } catch (error) {
      console.error("Error de cargar las tareas", error);
    }
  }

  //CREAR AVISO
  async agregarTarea(nuevaTarea: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.post(`${this.apiUrl}/avisos`, nuevaTarea));
      this.cargarTareas();
      return true;
    } catch (error) {
      console.error("Error al guardar la tarea:", error);
      return false;
    }
  }

  //ACTUALIZAR AVISO
  async actualizarTarea(id:number,datosTarea:any):Promise<boolean>{
    try{
      await firstValueFrom(this.http.put(`${this.apiUrl}/avisos/${id}`,datosTarea));
      this.cargarTareas();
      return true;
    }catch(error){
      console.error("Error al actualizar la tarea", error);
      return false;
    }
  }

  //ESTADO DEL AVISO
  async asignarTarea(idTarea:number,idEmpleado:number){
    await this.actualizarTarea(idTarea,{id_empleado:idEmpleado,estado:'En proceso'});
  }

  async finalizarTarea(idTarea:number){
    await this.actualizarTarea(idTarea,{estado: 'Finalizada'});
  }

  async cancelarTarea(idTarea:number): Promise<boolean>{
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/avisos/${idTarea}/cancelar`, {}));
      this.cargarTareas();
      return true;
    } catch (error) {
      console.error("Error al cancelar la tarea:", error);
      return false;
    }
  }

  //ELIMINAR TAREA
  async eliminarTarea(id: number): Promise<boolean> {
    try {
      await firstValueFrom(this.http.delete(`${this.apiUrl}/avisos/${id}`));
      this.cargarTareas();
      return true;
    } catch (error) {
      console.error("Error al eliminar la tarea:", error);
      return false;
    }
  }
}
