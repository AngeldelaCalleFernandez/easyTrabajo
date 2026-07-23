# variables_entorno.md

# EasyParte — Variables de entorno

## 1. Propósito

Este documento define las variables de entorno recomendadas para EasyParte.

Las variables de entorno permiten separar configuración sensible del código.

Nunca se deben subir secretos reales al repositorio.

---

## 2. Archivos recomendados

Se pueden usar archivos separados por entorno:

```txt
.env.local
.env.staging
.env.production
.env.testing
```

También puede existir un archivo de ejemplo:

```txt
.env.example
```

`env.example` no debe contener secretos reales.

---

## 3. Variables generales

```env
APP_NAME=EasyParte
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Europe/Madrid
```

## Explicación

`APP_ENV`: entorno actual.

Valores:

```txt
local
staging
production
testing
```

`APP_DEBUG`: en producción debe ser `false`.

---

## 4. URLs

```env
FRONTEND_URL=http://localhost:4200
API_URL=http://localhost/easyTrabajo/backend/public/api
PUBLIC_URL=http://localhost
```

En producción:

```env
FRONTEND_URL=https://app.easyparte.com
API_URL=https://api.easyparte.com
PUBLIC_URL=https://app.easyparte.com
```

---

## 5. Base de datos

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=easyparte
DB_USER=easyparte_user
DB_PASSWORD=change_me
DB_CHARSET=utf8mb4
```

Reglas:

- no usar root en producción;
- contraseña fuerte;
- base de datos privada;
- usuario con permisos mínimos necesarios.

---

## 6. Seguridad y autenticación

```env
JWT_SECRET=change_me_to_a_long_random_secret
JWT_EXPIRES_IN=3600
JWT_REFRESH_EXPIRES_IN=604800
PASSWORD_HASH_ALGO=argon2id
```

Estado actual:

- EasyParte mantiene autenticación con JWT Bearer por compatibilidad con el frontend actual.
- `JWT_SECRET` es obligatorio fuera de `local`, `development`, `dev` y `testing`.
- El secreto debe definirse en `.env` o en variables reales del entorno.
- No guardar tokens completos ni secretos en logs o documentación.

Decisión futura:

Si se usa cookie HttpOnly:

```env
SESSION_COOKIE_NAME=easyparte_session
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_HTTPONLY=true
SESSION_COOKIE_SAMESITE=Lax
SESSION_DOMAIN=.easyparte.com
```

Reglas:

- `JWT_SECRET` debe ser largo y aleatorio;
- no debe estar en Git;
- no debe compartirse entre entornos;
- producción debe tener su propio secreto;
- la migración a cookie HttpOnly debe abordarse en una tarea separada.

---

## 7. CORS

```env
CORS_ALLOWED_ORIGINS=http://localhost:4200
CORS_ALLOWED_METHODS=GET,POST,PATCH,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization
CORS_ALLOW_CREDENTIALS=false
```

En producción:

```env
CORS_ALLOWED_ORIGINS=https://app.easyparte.com
CORS_ALLOW_CREDENTIALS=true
```

Solo poner `CORS_ALLOW_CREDENTIALS=true` si se usan cookies o credenciales.

No usar `*` en producción.

---

## 8. Logs

```env
LOG_CHANNEL=file
LOG_LEVEL=debug
LOG_PATH=storage/logs/app.log
```

En producción:

```env
LOG_LEVEL=warning
```

No guardar:

- contraseñas;
- tokens completos;
- datos bancarios;
- secretos;
- payloads sensibles innecesarios.

---

## 9. Almacenamiento

```env
STORAGE_PATH=storage
SIGNATURES_PATH=storage/firmas
ATTACHMENTS_PATH=storage/adjuntos
EXPORTS_PATH=storage/exportaciones
BACKUP_PATH=storage/backups
```

Reglas:

- proteger carpetas privadas;
- no exponer firmas directamente sin control;
- validar archivos subidos;
- incluir archivos importantes en backup.

---

## 10. Backups

```env
BACKUP_ENABLED=true
BACKUP_FREQUENCY=daily
BACKUP_RETENTION_DAYS=30
BACKUP_DATABASE=true
BACKUP_FILES=true
```

Pendiente:

Definir herramienta concreta de backup según hosting.

---

## 11. Email y notificaciones

Pendiente de implementación, pero preparado:

```env
MAIL_ENABLED=false
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=no-reply@easyparte.com
MAIL_FROM_NAME=EasyParte
```

---

## 12. Facturación e integraciones

Pendiente de implementación:

```env
BILLING_EXPORT_ENABLED=false
BILLING_EXPORT_DRIVER=csv
BILLING_EXPORT_PATH=storage/exportaciones/facturacion
```

Si en el futuro se integra una API externa:

```env
BILLING_API_URL=
BILLING_API_KEY=
```

---

## 13. Límites por defecto

Aunque los límites deben estar en base de datos, pueden existir valores por defecto:

```env
FREE_PLAN_MAX_USERS=2
FREE_PLAN_MAX_CLIENTS=15
FREE_PLAN_MAX_AVISOS=5
FREE_PLAN_MAX_PARTES=5
```

La fuente principal debería ser la tabla `plan`.

---

## 14. Ejemplo de .env.example

```env
APP_NAME=EasyParte
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Europe/Madrid

FRONTEND_URL=http://localhost:4200
API_URL=http://localhost/easyTrabajo/backend/public/api

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=easyparte
DB_USER=easyparte_user
DB_PASSWORD=change_me
DB_CHARSET=utf8mb4

JWT_SECRET=change_me_to_a_long_random_secret
JWT_EXPIRES_IN=3600
JWT_REFRESH_EXPIRES_IN=604800

CORS_ALLOWED_ORIGINS=http://localhost:4200
CORS_ALLOWED_METHODS=GET,POST,PATCH,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization
CORS_ALLOW_CREDENTIALS=false

LOG_CHANNEL=file
LOG_LEVEL=debug
LOG_PATH=storage/logs/app.log

STORAGE_PATH=storage
SIGNATURES_PATH=storage/firmas
ATTACHMENTS_PATH=storage/adjuntos
EXPORTS_PATH=storage/exportaciones
BACKUP_PATH=storage/backups
```

---

## 15. Variables que nunca deben subirse reales

Nunca subir valores reales de:

- DB_PASSWORD;
- JWT_SECRET;
- MAIL_PASSWORD;
- BILLING_API_KEY;
- claves de pago;
- credenciales de servidor;
- credenciales de backup.

---

## 16. Conclusión

Las variables de entorno son obligatorias para profesionalizar EasyParte.

El código no debe depender de valores hardcodeados para:

- base de datos;
- URLs;
- secretos;
- CORS;
- rutas de almacenamiento;
- entorno;
- logs.
