# Backend de EasyParte

![PHP](https://img.shields.io/badge/PHP-Vanilla_OOP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)

Este backend es la API REST de EasyParte. Está desarrollado en PHP vanilla orientado a objetos y se encarga de conectar el frontend Angular con la base de datos MariaDB/MySQL.

Su objetivo es gestionar autenticación, usuarios, roles, clientes, avisos, empleados y partes de trabajo.

## Tecnología usada

- PHP 8.x.
- PDO para consultas a base de datos.
- MariaDB / MySQL.
- JWT propio para autenticación.
- Apache mediante XAMPP en entorno local.

No se usan frameworks externos ni dependencias instaladas con Composer.

## Estructura de carpetas

```text
backend/
├── config/
│   ├── cors.php
│   └── database.php
├── controllers/
│   ├── AuthController.php
│   ├── AvisoController.php
│   ├── ClienteController.php
│   ├── DashboardController.php
│   ├── EmpleadoController.php
│   ├── ParteTrabajoController.php
│   ├── RolController.php
│   └── UsuarioController.php
├── helpers/
│   ├── jwt.php
│   └── response.php
├── middleware/
│   └── AuthMiddleware.php
├── models/
│   ├── Cliente.php
│   └── Usuario.php
├── public/
│   ├── .htaccess
│   └── index.php
└── routes/
    └── api.php
```

## Punto de entrada

El punto de entrada de la API es:

```text
backend/public/index.php
```

Este archivo carga CORS, configura la respuesta JSON, crea la conexión a base de datos y delega las rutas a:

```text
backend/routes/api.php
```

En local, la API queda accesible desde:

```text
http://localhost/easyTrabajo/backend/public/api
```

## Rutas principales detectadas

```text
POST   /api/login
GET    /api/dashboard

GET    /api/clientes
POST   /api/clientes
PUT    /api/clientes/{id}
DELETE /api/clientes/{id}

GET    /api/avisos
POST   /api/avisos
PUT    /api/avisos/{id}
DELETE /api/avisos/{id}

GET    /api/partes
POST   /api/partes
PUT    /api/partes/{id}

GET    /api/empleados
POST   /api/empleados
PUT    /api/empleados/{id}
DELETE /api/empleados/{id}

GET    /api/usuarios
POST   /api/usuarios
PUT    /api/usuarios/{id}
DELETE /api/usuarios/{id}

GET    /api/roles
```

## Controladores principales

- `AuthController.php`: login y generación del token.
- `ClienteController.php`: gestión de clientes.
- `AvisoController.php`: gestión de avisos o tareas.
- `ParteTrabajoController.php`: gestión de partes de trabajo/albaranes.
- `EmpleadoController.php`: gestión de empleados.
- `UsuarioController.php`: gestión de usuarios.
- `RolController.php`: lectura de roles.
- `DashboardController.php`: resumen de datos para la pantalla inicial.

## Modelos principales

Actualmente hay modelos para:

- `Cliente.php`
- `Usuario.php`

Otros controladores contienen consultas SQL directamente. Para un TFC es entendible, aunque una mejora futura sería mover más lógica de acceso a datos a modelos separados.

## Configuración de base de datos

La conexión se configura en:

```text
backend/config/database.php
```

Valores esperados en local:

```text
host: localhost
database: easyParte
user: root
password: vacío
port: 3306
```

Si XAMPP usa otro puerto para MySQL, hay que cambiarlo en ese archivo.

## Instalación y ejecución local

1. Colocar el proyecto en:

```text
C:/xampp/htdocs/easyTrabajo
```

2. Arrancar Apache y MySQL en XAMPP.

3. Importar la base de datos desde:

```text
bbdd/easyparte.sql
```

4. Probar que la API responde desde:

```text
http://localhost/easyTrabajo/backend/public/api
```

## Cómo probar endpoints básicos

Login:

```http
POST http://localhost/easyTrabajo/backend/public/api/login
Content-Type: application/json

{
  "email": "profesor@easyparte.es",
  "password": "1234"
}
```

Petición autenticada:

```http
GET http://localhost/easyTrabajo/backend/public/api/clientes
Authorization: Bearer TOKEN_DEVUELTO_POR_LOGIN
```

## Autenticación

La autenticación se basa en JWT. El login valida usuario y contraseña, y si son correctos devuelve:

- `mensaje`
- `token`
- `usuario`

El token contiene datos como:

- `id_usuario`
- `id_empleado`
- `id_empresa`
- `rol_nombre`
- fecha de emisión
- fecha de expiración

La clave JWT se obtiene con:

```php
getenv('JWT_SECRET')
```

Si no existe variable de entorno, se usa una clave local por defecto para que XAMPP siga funcionando sin configuración extra.

## Middleware

El middleware principal es:

```text
middleware/AuthMiddleware.php
```

Comprueba que la petición tenga cabecera:

```text
Authorization: Bearer <token>
```

Si el token no existe, no tiene formato correcto, no se puede validar o ha caducado, devuelve `401`.

## Seguridad

Medidas implementadas:

- `password_hash` para guardar contraseñas.
- `password_verify` para validar login.
- Tokens JWT con expiración.
- Rutas privadas protegidas por middleware.
- Rutas de administración protegidas por rol `Administrador`.
- Consultas preparadas con PDO.
- Filtrado por `id_empresa` en operaciones principales.
- Errores internos registrados con `error_log`.
- Mensajes genéricos para evitar mostrar SQL o detalles internos al cliente.

Limitaciones de seguridad:

- CORS permite cualquier origen en desarrollo.
- No hay `.env` completo.
- La configuración de base de datos está en archivo PHP.
- El control de roles podría ser más detallado por acción.
- No hay rate limit ni bloqueo por intentos de login.

## Errores comunes

Login devuelve error:

- Revisar que MySQL está arrancado.
- Revisar el nombre de la base de datos.
- Revisar usuario y contraseña en `database.php`.
- Borrar sesión antigua del navegador si se cambió la clave JWT.

Error al crear cliente:

- Puede existir ya un cliente con el mismo NIF.

Error al crear usuario:

- Puede existir ya un usuario con el mismo email.

Error al crear empleado:

- Si el NIF se deja vacío, se guarda como `NULL`.
- Si se rellena, debe ser único.

Respuesta 401:

- Falta token.
- Token caducado.
- Token firmado con una clave distinta.

## Limitaciones actuales

- No todos los controladores usan modelos.
- No hay capa de servicios.
- Las validaciones son básicas.
- No hay tests automatizados.
- El enrutador es manual.
- Algunos mensajes siguen pensados para entorno local.

## Mejoras futuras

- Usar variables de entorno para base de datos y JWT.
- Centralizar respuestas y errores.
- Mover SQL de controladores a modelos.
- Añadir validaciones más completas.
- Crear tests de endpoints.
- Mejorar permisos por rol y acción.
- Añadir logs más ordenados.
- Preparar configuración separada para desarrollo y producción.
