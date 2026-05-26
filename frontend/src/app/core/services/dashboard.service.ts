import { inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { API_BASE_URL } from '../config/api.config';

// Definimos la estructura exacta que nos devuelve PHP
export interface DashboardResumen {
  clientesActivos: number;
  avisosPendientes: number;
  albaranesAbiertos: number;
  horasMes: number;
  esVistaPersonal: boolean;
}

@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private http = inject(HttpClient);
  private apiUrl = API_BASE_URL;

  // Usamos un Signal para que la vista se actualice
  public resumen = signal<DashboardResumen | null>(null);

  cargarResumen() {
    this.http.get<DashboardResumen>(`${this.apiUrl}/dashboard`).subscribe({
      next: (datos) => this.resumen.set(datos),
      error: (err) => console.error("Error al cargar el dashboard", err)
    });
  }
}
