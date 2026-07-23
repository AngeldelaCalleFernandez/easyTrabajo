import { inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { ParteTrabajo } from '../interfaces/partes.interfaces';
import { API_BASE_URL } from '../config/api.config';

@Injectable({
  providedIn: 'root',
})
export class PartesService {
  private http = inject(HttpClient);
  private apiUrl = API_BASE_URL;

  public partes = signal<ParteTrabajo[]>([]);

  // CARGAR TODOS LOS PARTES
  cargarPartes() {
    this.http.get<ParteTrabajo[]>(`${this.apiUrl}/partes`).subscribe({
      next: (datosReales) => this.partes.set(datosReales),
      error: (error) => console.error("Error al cargar los partes de trabajo", error)
    });
  }

  // CREAR UN PARTE DE TRABAJO
  async agregarParte(nuevoParte: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.post(`${this.apiUrl}/partes`, nuevoParte));
      this.cargarPartes();
      return true;
    } catch (error) {
      console.error("Error al guardar el parte de trabajo:", error);
      return false;
    }
  }

  // ACTUALIZAR O CERRAR PARTE
  async actualizarParte(id: number, datosParte: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/partes/${id}`, datosParte));
      this.cargarPartes(); // Recargamos para ver los cambios
      return true;
    } catch (error) {
      console.error("Error al actualizar el parte:", error);
      return false;
    }
  }
}
