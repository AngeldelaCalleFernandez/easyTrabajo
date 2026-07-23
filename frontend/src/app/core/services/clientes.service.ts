import { inject, Injectable, signal } from '@angular/core';
import { Cliente } from '../interfaces/cliente.interfaces';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { API_BASE_URL } from '../config/api.config';



@Injectable({
  providedIn: 'root'
})

export class ClientesService {
  private http = inject(HttpClient);
  private apiUrl = API_BASE_URL;
  // Usamos un Signal para guardar la lista de clientes reactivamente
  public clientes = signal<Cliente[]>([]);


  // Función para cargar los clientes desde PHP
  cargarClientes() {
    this.http.get<Cliente[]>(`${this.apiUrl}/clientes`).subscribe({
      next: (datosReales) => {
        this.clientes.set(datosReales);
      },
      error: (error) => {
        console.error("Error al cargar clientes:", error);
      }
    });
  }

  async agregarCliente(nuevoCliente: Cliente): Promise<boolean> {
    try {
      // Hacemos la petición POST a PHP esperando la respuesta
      const respuesta: any = await firstValueFrom(
        this.http.post(`${this.apiUrl}/clientes`, nuevoCliente)
      );

      // Si PHP lo ha guardado bien, nos devolverá el cliente con su ID real de MariaDB.
      // Lo añadimos a nuestro Signal para que aparezca en la tabla al instante.
      this.clientes.update(actuales => [respuesta.cliente, ...actuales]);
      return true;

    } catch (error) {
      console.error("Error al guardar el cliente en PHP:", error);
      return false;
    }
  }

  async actualizarCliente(id: number, datosCliente: any): Promise<boolean> {
    try {
      await firstValueFrom(this.http.put(`${this.apiUrl}/clientes/${id}`, datosCliente));

      // Actualizamos la tabla visual sustituyendo el cliente viejo por el nuevo
      this.clientes.update(actuales =>
        actuales.map(c => c.id_cliente === id ? { ...c, ...datosCliente } : c)
      );
      return true;
    } catch (error) {
      console.error("Error al actualizar el cliente:", error);
      return false;
    }
  }

  async eliminarCliente(id: number): Promise<boolean> {
    try {
      await firstValueFrom(this.http.delete(`${this.apiUrl}/clientes/${id}`));

      // Lo quitamos de la tabla visual al instante
      this.clientes.update(actuales => actuales.filter(c => c.id_cliente !== id));
      return true;
    } catch (error) {
      console.error("Error al eliminar el cliente:", error);
      return false;
    }
  }

}

