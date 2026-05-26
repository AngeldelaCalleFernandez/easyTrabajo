# Implementado frontend - EasyParte

Auditoria inicial realizada en modo solo lectura el 2026-05-20 sobre `frontend/src/app/`.

Estado general del frontend: parcial.

Estructura real detectada:

- Angular 21 con standalone components.
- Rutas en `frontend/src/app/app.routes.ts`.
- Configuracion HTTP e interceptor en `frontend/src/app/app.config.ts`.
- Core: `services`, `guards`, `interceptors`, `interfaces`.
- Layout: `frontend/src/app/layouts/dashboard/`.
- Shared: `navbar`, `alert-modal`.
- Features reales: `auth`, `dashboard`, `clientes`, `avisos`, `albaranes`, `administracion`.
- Tailwind CSS importado en `frontend/src/styles.css`.
- Chart.js usado en dashboard.

Rutas reales detectadas:

- `/login`
- `/dashboard`
- `/avisos`
- `/albaranes`
- `/clientes`
- `/administracion`

## Modulo: Login

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/auth/login.ts`
- `frontend/src/app/features/auth/login.html`

### Servicios detectados

- `frontend/src/app/core/services/auth.service.ts`

### Rutas detectadas

- `/login`

### Guards/interceptors

- Interceptor no requerido para login.

### Problemas

- Token y usuario se guardan en `sessionStorage`.
- URL API hardcodeada en `AuthService`.
- `AuthService.login()` usa `any` para respuesta.
- Manejo de error generico; no hay interceptor de errores 401.

### Pruebas realizadas

- Revision estatica. No se ejecuto Angular ni backend.

### Notas

- Login usa Reactive Forms y validadores basicos.

## Modulo: Dashboard

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/dashboard/dashboard.ts`
- `frontend/src/app/features/dashboard/dashboard.html`

### Servicios detectados

- `frontend/src/app/core/services/dashboard.service.ts`

### Rutas detectadas

- `/dashboard`

### Guards/interceptors

- Protegida por `authGuard` en ruta padre.
- `authInterceptor` envia Bearer token.

### Problemas

- Vista depende del dato `esVistaPersonal` devuelto por backend.
- `renderizarGrafico(stats: any)` usa `any`.
- No hay control por jefe/equipo/departamento.
- Errores quedan en `console.error`.

### Pruebas realizadas

- Revision estatica.

### Notas

- Chart.js esta implementado para grafico tipo doughnut.

## Modulo: Navbar/layout

Estado: parcial

### Componentes detectados

- `frontend/src/app/layouts/dashboard/dashboard.ts`
- `frontend/src/app/layouts/dashboard/dashboard.html`
- `frontend/src/app/shared/components/navbar/navbar.ts`
- `frontend/src/app/shared/components/navbar/navbar.html`

### Servicios detectados

- `AuthService`.

### Rutas detectadas

- Layout privado para rutas hijas.

### Guards/interceptors

- `authGuard` en ruta padre.
- Ocultacion visual de administracion si `rol_nombre === 'Administrador'`.

### Problemas

- Solo contempla rol `Administrador`.
- La seguridad visual no sustituye backend.
- No hay tenant/suscripcion visibles.

### Pruebas realizadas

- Revision estatica.

### Notas

- Incluye menu responsive y logout.

## Modulo: Clientes

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/clientes/clientes.ts`
- `frontend/src/app/features/clientes/clientes.html`

### Servicios detectados

- `frontend/src/app/core/services/clientes.service.ts`
- `frontend/src/app/core/services/alert.service.ts`

### Rutas detectadas

- `/clientes`

### Guards/interceptors

- Protegida por `authGuard` en ruta padre.
- Sin `roleGuard` especifico.

### Problemas

- Cualquier usuario autenticado con acceso a ruta puede usar pantalla si backend lo permite.
- Servicio usa URL hardcodeada y varios `any`.
- Validacion NIF rigida en frontend, pero negocio permite datos incompletos en algunos casos.
- Errores se registran en consola y se muestran mensajes genericos.

### Pruebas realizadas

- Revision estatica.

### Notas

- CRUD visual completo para listado, alta, edicion y baja.

## Modulo: Avisos

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/avisos/avisos.ts`
- `frontend/src/app/features/avisos/avisos.html`

### Servicios detectados

- `frontend/src/app/core/services/avisos.service.ts`
- `ClientesService`
- `AdminService`
- `AuthService`
- `AlertService`

### Rutas detectadas

- `/avisos`

### Guards/interceptors

- Protegida por `authGuard`.
- Sin `roleGuard` especifico.

### Problemas

- Formulario contiene `id_empresa: 1`.
- Control visual de tecnico/administrador basado en `rol_nombre`.
- Asignacion usa un unico `id_empleado`.
- Servicios usan `any` y URL hardcodeada.
- La pantalla permite "coger aviso" no asignado; debe validarse en backend.

### Pruebas realizadas

- Revision estatica.

### Notas

- Modulo frontend se llama avisos, servicio `TareasService` y backend/SQL usan `tarea`.

## Modulo: Partes/albaranes

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/albaranes/albaranes.ts`
- `frontend/src/app/features/albaranes/albaranes.html`

### Servicios detectados

- `frontend/src/app/core/services/partes.service.ts`
- `TareasService`
- `ClientesService`
- `AuthService`

### Rutas detectadas

- `/albaranes`

### Guards/interceptors

- Protegida por `authGuard`.
- Sin `roleGuard` especifico.

### Problemas

- No hay firma, captura tactil, hash, rectificacion ni facturacion.
- Cierre de parte se hace por `PUT /partes/{id}` con `estado: 'Cerrado'`.
- Usa `alert()` en errores de guardar/cerrar.
- No hay control visual avanzado por rol para editar/cerrar salvo bloqueo por estado cerrado.
- Material y horas son campos simples.

### Pruebas realizadas

- Revision estatica.

### Notas

- Puede crear parte manual o precargado desde aviso.

## Modulo: Administracion

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/administracion/administracion.ts`
- `frontend/src/app/features/administracion/administracion.html`

### Servicios detectados

- `frontend/src/app/core/services/admin.service.ts`
- `AlertService`

### Rutas detectadas

- `/administracion`

### Guards/interceptors

- `roleGuard('Administrador')`.
- `authInterceptor`.

### Problemas

- Solo contempla rol `Administrador`, no roles objetivo.
- Formularios contienen `id_empresa: 1`.
- Servicio usa `any`, URL hardcodeada y errores por consola.
- No hay departamentos/equipos ni bloqueo por empresa.

### Pruebas realizadas

- Revision estatica.

### Notas

- Agrupa empleados y usuarios en pestanas.

## Modulo: Usuarios

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/administracion/administracion.ts`
- `frontend/src/app/features/administracion/administracion.html`

### Servicios detectados

- `frontend/src/app/core/services/admin.service.ts`

### Rutas detectadas

- `/administracion`

### Guards/interceptors

- `roleGuard('Administrador')`.

### Problemas

- Gestion dentro de administracion, no modulo independiente.
- No hay multiempresa, bloqueo por empresa ni roles objetivo.
- Password minimo 4 caracteres en frontend.
- `id_empresa: 1` en formulario.

### Pruebas realizadas

- Revision estatica.

### Notas

- Alta, edicion y baja visual existen.

## Modulo: Empleados

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/administracion/administracion.ts`
- `frontend/src/app/features/administracion/administracion.html`

### Servicios detectados

- `frontend/src/app/core/services/admin.service.ts`

### Rutas detectadas

- `/administracion`

### Guards/interceptors

- `roleGuard('Administrador')`.

### Problemas

- No hay departamentos/equipos en pantalla.
- `id_empresa: 1` en formulario.
- Relacion empleado-usuario basica.

### Pruebas realizadas

- Revision estatica.

### Notas

- Alta, edicion y baja visual existen.

## Modulo: Roles

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/administracion/administracion.ts`

### Servicios detectados

- `frontend/src/app/core/services/admin.service.ts`

### Rutas detectadas

- `/administracion`

### Guards/interceptors

- `roleGuard('Administrador')`.

### Problemas

- Solo se listan/asignan roles reales del backend.
- No hay gestion de permisos ni roles por empresa.
- Roles objetivo no existen en frontend.

### Pruebas realizadas

- Revision estatica.

### Notas

- Depende de `GET /api/roles`.

## Modulo: Presupuestos

Estado: pendiente

### Componentes detectados

- No detectados.

### Servicios detectados

- No detectados.

### Rutas detectadas

- No detectadas.

### Guards/interceptors

- No aplica.

### Problemas

- Modulo objetivo no implementado en frontend.

### Pruebas realizadas

- Revision estatica.

### Notas

- Solo aparece en documentacion AGENT.

## Modulo: Suscripcion

Estado: pendiente

### Componentes detectados

- No detectados.

### Servicios detectados

- No detectados.

### Rutas detectadas

- No detectadas.

### Guards/interceptors

- No aplica.

### Problemas

- No hay vista de plan, uso ni limites.

### Pruebas realizadas

- Revision estatica.

### Notas

- Modelo SaaS pendiente tambien en backend/SQL.

## Modulo: Materiales

Estado: parcial

### Componentes detectados

- `frontend/src/app/features/albaranes/albaranes.ts`
- `frontend/src/app/features/albaranes/albaranes.html`

### Servicios detectados

- `PartesService`.

### Rutas detectadas

- `/albaranes`

### Guards/interceptors

- `authGuard` por ruta padre.

### Problemas

- Materiales son texto libre del albaran.
- No hay catalogo, stock, movimientos ni modulo propio.

### Pruebas realizadas

- Revision estatica.

### Notas

- Cubre solo entrada manual.

## Modulo: Auditoria

Estado: pendiente

### Componentes detectados

- No detectados.

### Servicios detectados

- No detectados.

### Rutas detectadas

- No detectadas.

### Guards/interceptors

- No aplica.

### Problemas

- No hay interfaz de auditoria ni restricciones de consulta.

### Pruebas realizadas

- Revision estatica.

### Notas

- Requisito critico pendiente.

## Modulo: Alertas/modales

Estado: parcial

### Componentes detectados

- `frontend/src/app/shared/components/alert-modal/alert-modal/alert-modal.ts`
- `frontend/src/app/shared/components/alert-modal/alert-modal/alert-modal.html`

### Servicios detectados

- `frontend/src/app/core/services/alert.service.ts`

### Rutas detectadas

- No aplica.

### Guards/interceptors

- No aplica.

### Problemas

- No todo el frontend usa el sistema centralizado; albaranes mantiene `alert()`.
- No hay `ErrorInterceptor`.

### Pruebas realizadas

- Revision estatica.

### Notas

- Hay servicio de alertas con confirmacion.
