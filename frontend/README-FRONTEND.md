# Frontend de EasyParte

![Angular](https://img.shields.io/badge/Angular-21+-DD0031?style=for-the-badge&logo=angular&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

Este frontend es la parte visual de EasyParte. Está desarrollado con Angular y se comunica con el backend PHP mediante peticiones HTTP.

La aplicación permite iniciar sesión, consultar el dashboard, gestionar clientes, avisos, partes de trabajo, empleados y usuarios.

## Tecnología usada

- Angular 21 o versión equivalente usada durante el desarrollo.
- TypeScript.
- Standalone components.
- Angular Router.
- Reactive Forms.
- Signals.
- Tailwind CSS.
- Chart.js.
- npm como gestor de paquetes.

## Estructura de carpetas

```text
frontend/
├── public/
├── src/
│   ├── app/
│   │   ├── core/
│   │   │   ├── guards/
│   │   │   ├── interceptors/
│   │   │   ├── interfaces/
│   │   │   └── services/
│   │   ├── features/
│   │   │   ├── administracion/
│   │   │   ├── albaranes/
│   │   │   ├── auth/
│   │   │   ├── avisos/
│   │   │   ├── clientes/
│   │   │   └── dashboard/
│   │   ├── layouts/
│   │   └── shared/
│   ├── index.html
│   ├── main.ts
│   └── styles.css
├── angular.json
├── package.json
└── package-lock.json
```

## Instalación de dependencias

Desde la carpeta `frontend`:

```bash
npm install
```

## Ejecutar Angular

```bash
npm start
```

o directamente:

```bash
ng serve
```

La aplicación se abre en:

```text
http://localhost:4200
```

## Build

Para generar una compilación:

```bash
npm run build
```

La salida se genera en:

```text
frontend/dist/
```

Esta carpeta no es necesaria para revisar el código fuente.

## Comunicación con la API

Los servicios usan como URL base:

```text
http://localhost/easyTrabajo/backend/public/api
```

Esta URL está escrita directamente en los servicios. Para el proyecto local funciona bien, aunque una mejora futura sería moverla a los archivos de entorno de Angular.

## Servicios principales

- `auth.service.ts`: login, logout y usuario actual.
- `clientes.service.ts`: carga, alta, edición y baja de clientes.
- `avisos.service.ts`: carga, alta, edición, asignación, finalización y cancelación de avisos.
- `partes.service.ts`: carga, alta y actualización de partes.
- `admin.service.ts`: gestión de empleados, usuarios y roles.
- `dashboard.service.ts`: resumen para el dashboard.
- `alert.service.ts`: modal simple de mensajes y confirmaciones.

## Rutas principales

```text
/login
/dashboard
/avisos
/albaranes
/clientes
/administracion
```

La ruta de administración está protegida para usuarios con rol `Administrador`.

## Páginas y componentes

Features:

- `auth/login`: pantalla de acceso.
- `dashboard`: resumen de clientes, avisos, albaranes y horas.
- `clientes`: gestión de clientes.
- `avisos`: gestión de avisos o tareas.
- `albaranes`: gestión de partes de trabajo.
- `administracion`: gestión de empleados y usuarios.

Layout:

- `layouts/dashboard`: estructura privada con navbar y contenido.

Shared:

- `navbar`: menú principal.
- `alert-modal`: modal de avisos y confirmaciones.

## Formularios

El proyecto usa Reactive Forms en las pantallas principales:

- Login.
- Clientes.
- Avisos.
- Albaranes.
- Administración de empleados.
- Administración de usuarios.

Las validaciones de frontend son básicas y ayudan a evitar envíos incompletos, pero el backend y la base de datos también deben validar los datos importantes.

## Guards

Hay dos guards principales:

- `auth.guard.ts`: evita entrar en rutas privadas si no hay usuario en sesión.
- `role.guard.ts`: restringe administración al rol requerido.

Estos guards mejoran la experiencia de usuario, pero la seguridad real debe estar también en backend.

## Gestión de sesión y token

Al hacer login, el backend devuelve un token JWT y los datos del usuario. El frontend guarda:

```text
easyparte_token
easyparte_user
```

en `sessionStorage`.

El interceptor:

```text
core/interceptors/auth.interceptor.ts
```

añade el token a las peticiones con:

```text
Authorization: Bearer <token>
```

Si el token caduca, puede ser necesario cerrar sesión o limpiar `sessionStorage` y volver a iniciar sesión.

## Comunicación con avisos y albaranes

Desde la pantalla de avisos se puede navegar a albaranes con el identificador del aviso en la URL. La pantalla de albaranes carga los avisos si aún no estaban disponibles y después precarga los datos principales del aviso.

Esto evita que el formulario se abra sin cliente o descripción cuando se entra desde una URL directa.

## Errores comunes

No deja iniciar sesión:

- Revisar que Apache y MySQL están arrancados.
- Revisar que la API responde en `localhost`.
- Borrar `sessionStorage` si había una sesión anterior.

Error al crear cliente:

- Puede que el NIF ya exista.

Error al crear usuario:

- Puede que el email ya exista.

Error al crear empleado:

- Si se rellena NIF, debe ser único.

La pantalla de administración no aparece:

- El usuario debe tener rol `Administrador`.

Peticiones con 401:

- Token no enviado, caducado o no válido.

## Limitaciones actuales

- La URL de API no está centralizada en environments.
- No hay manejo global de errores HTTP.
- Hay uso puntual de tipos `any`.
- No hay tests reales de componentes o servicios.
- El control de sesión es sencillo.
- Algunas pantallas mezclan bastante lógica de formulario y vista.

## Mejoras futuras

- Crear `environment.ts` para la URL de API.
- Manejar automáticamente respuestas 401.
- Reducir `any` usando interfaces más completas.
- Añadir tests unitarios básicos.
- Mejorar validaciones visuales en formularios.
- Unificar todos los errores con el modal de alertas.
- Revisar accesibilidad y responsive.
- Separar más lógica compleja en servicios o utilidades.
