import { inject, Injectable, signal } from '@angular/core';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { Usuario } from '../interfaces/auth.interfaces';
import { API_BASE_URL } from '../config/api.config';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private router = inject(Router);
  private http = inject(HttpClient);

  public usuarioActual = signal<Usuario | null>(null);

  private apiUrl = API_BASE_URL;

  constructor() {
    // Recuperamos al recargar
    const user = sessionStorage.getItem('easyparte_user');
    if (user) {
      this.usuarioActual.set(JSON.parse(user));
    }
  }

  async login(email: string, password: string): Promise<boolean> {
    try {
      // Llamada limpia a /login
      const respuesta: any = await firstValueFrom(
        this.http.post(`${this.apiUrl}/login`, { email, password })
      );

      // Guardamos el usuario para el Signal y el token para el Interceptor
      this.usuarioActual.set(respuesta.usuario);

      sessionStorage.setItem('easyparte_token', respuesta.token);
      sessionStorage.setItem('easyparte_user', JSON.stringify(respuesta.usuario));

      this.router.navigate(['/dashboard']);
      return true;

    } catch (error) {
      console.error("Fallo de autenticación", error);
      return false;
    }
  }

  logout() {
    this.usuarioActual.set(null);
    sessionStorage.removeItem('easyparte_token');
    sessionStorage.removeItem('easyparte_user');
    this.router.navigate(['/login']);
  }
}
