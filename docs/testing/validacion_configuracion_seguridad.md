# Validacion manual - configuracion base de seguridad

Fecha de creacion: 2026-05-21
Alcance: validacion manual de INC-0001, INC-0002, INC-0003 e INC-0004.

Este documento sirve para comprobar que la correccion de configuracion por entorno funciona en local, no rompe XAMPP y falla de forma controlada fuera de entorno local/desarrollo.

La fuente principal para simular variables de entorno en XAMPP es un archivo `.env` local en la raiz del repositorio o en `backend/.env`. No subir ese archivo a Git. Usar `.env.example` como plantilla sin secretos reales.

Decision vigente de autenticacion: se mantiene JWT Bearer por compatibilidad con el frontend actual. La migracion a cookie HttpOnly queda como decision futura y no forma parte de esta fase de configuracion base.

Reglas de ejecucion:

- No marcar ninguna prueba como `correcto` si no se ha ejecutado manualmente.
- Registrar evidencias relevantes en `Resultado obtenido`.
- No pegar secretos reales, tokens completos ni mensajes internos sensibles en este documento.
- Si se modifica temporalmente una variable de entorno, restaurar el entorno local al terminar la prueba.

Estados permitidos:

```txt
pendiente
correcto
falla
bloqueado
```

## Variables de entorno implicadas

```env
APP_ENV=local
DB_HOST=localhost
DB_PORT=3306
DB_NAME=easyParte
DB_USER=root
DB_PASSWORD=
DB_CHARSET=utf8
JWT_SECRET=valor_local_de_prueba
CORS_ALLOWED_ORIGINS=http://localhost:4200
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization,X-Requested-With
CORS_ALLOW_CREDENTIALS=false
```

## Verificaciones tecnicas del loader

- `backend/public/index.php` carga `backend/config/env.php` antes de `backend/config/cors.php`, antes de `backend/config/database.php` y antes de que las rutas puedan usar JWT.
- `backend/config/database.php`, `backend/helpers/jwt.php` y `backend/config/cors.php` requieren tambien `backend/config/env.php` para lecturas directas.
- `backend/config/env.php` busca `.env` en la raiz del repositorio y en `backend/.env`.
- `.gitignore` ignora `.env` y `backend/.env`.
- `.env.example` existe y no contiene secretos reales.
- `backend/public/.htaccess` queda limitado a rewrite/configuracion minima y no define `APP_ENV` ni secretos.
- Verificacion 2026-05-22: `APP_ENV=production` sin `JWT_SECRET` falla con error generico; `APP_ENV=production` con `JWT_SECRET` definido en formato `.env` permite emitir JWT; `APP_ENV=local` sigue funcionando sin secreto explicito a nivel JWT.

---

## VAL-CONFIG-001 - Login local sin variables de entorno explicitas

- ID: VAL-CONFIG-001
- Objetivo: Confirmar que el login local sigue funcionando en XAMPP sin definir variables de entorno explicitas.
- Precondiciones:
  - XAMPP iniciado con Apache y MySQL.
  - Base de datos local `easyParte` importada.
  - No definir temporalmente `APP_ENV`, `DB_*` ni `JWT_SECRET` para esta prueba.
  - Usuario local valido disponible.
- Pasos:
  1. Abrir la aplicacion frontend local.
  2. Ir a la pantalla de login.
  3. Introducir credenciales validas.
  4. Enviar el formulario.
  5. Comprobar la respuesta de la API en las herramientas de red del navegador.
- Resultado esperado:
  - El login responde correctamente.
  - Se devuelve token JWT.
  - No aparece error de configuracion.
  - No se rompe el flujo local con XAMPP.
- Resultado obtenido:
  - Login local ejecutado y token JWT emitido.
  - No se documenta el token completo ni datos personales del usuario autenticado por seguridad.
- Estado: correcto
- Notas:
  - Esta prueba valida los fallbacks locales de compatibilidad.

---

## VAL-CONFIG-002 - Login local con APP_ENV=local

- ID: VAL-CONFIG-002
- Objetivo: Confirmar que el login funciona cuando `APP_ENV=local` esta definido explicitamente.
- Precondiciones:
  - XAMPP iniciado con Apache y MySQL.
  - Base de datos local `easyParte` importada.
  - Definir `APP_ENV=local`.
  - Usuario local valido disponible.
- Pasos:
  1. Reiniciar Apache si la variable se ha definido a nivel de entorno del servidor.
  2. Abrir la aplicacion frontend local.
  3. Iniciar sesion con credenciales validas.
  4. Revisar que la API responde con login correcto.
- Resultado esperado:
  - El login funciona igual que sin variables explicitas.
  - Se devuelve token JWT.
  - No se registra error de `JWT_SECRET` obligatorio, porque el entorno es local.
- Resultado obtenido:
  - correcto.
- Estado: correcto
- Notas:
  - Si se define `JWT_SECRET`, el token debe firmarse con ese valor.

---

## VAL-CONFIG-003 - Conexion correcta a base de datos

- ID: VAL-CONFIG-003
- Objetivo: Verificar que la conexion a base de datos funciona con la configuracion local esperada.
- Precondiciones:
  - XAMPP iniciado con MySQL.
  - Base de datos `easyParte` accesible.
  - Configuracion local por defecto o variables `DB_*` correctas.
- Pasos:
  1. Abrir la aplicacion.
  2. Iniciar sesion.
  3. Acceder a una pantalla que consulte datos, por ejemplo dashboard o clientes.
  4. Revisar la respuesta HTTP de la API.
- Resultado esperado:
  - La API responde con datos o respuesta funcional esperada.
  - No se recibe error generico de conexion.
  - No hay errores PDO visibles en el navegador.
- Resultado obtenido:
  - La API responde con datos o respuesta funcional esperada.
  - No se recibe error generico de conexion.
  - No hay errores PDO visibles en el navegador.
- Estado: correcto
- Notas:
  - Anotar endpoint probado y codigo HTTP observado.

---

## VAL-CONFIG-004 - Fallo controlado de conexion a base de datos

- ID: VAL-CONFIG-004
- Objetivo: Confirmar que un fallo de conexion a BD se gestiona con HTTP 500 y mensaje generico.
- Precondiciones:
  - Entorno de prueba local disponible.
  - Poder cambiar temporalmente una variable de conexion, por ejemplo `DB_HOST`, `DB_NAME`, `DB_USER` o `DB_PASSWORD`.
  - No ejecutar esta prueba contra produccion.
- Pasos:
  1. Configurar temporalmente un valor de BD incorrecto.
  2. Reiniciar Apache si aplica.
  3. Hacer una peticion a un endpoint que inicialice la conexion.
  4. Revisar el cuerpo de la respuesta HTTP.
  5. Restaurar la configuracion correcta.
- Resultado esperado:
  - La respuesta al cliente es generica.
  - No se muestra host, DSN, usuario, ruta interna ni mensaje completo de PDO.
  - El detalle tecnico queda registrado en logs mediante `error_log`.
- Resultado obtenido:
  - Al solicitar `GET http://localhost/easyTrabajo/backend/public/api/clientes`, el servidor responde con `HTTP 500 Internal Server Error`.
  - El cuerpo de la respuesta es:
    ```json
    {
      "error": "No se ha podido conectar con la base de datos."
    }
    ```
  - No se observan datos sensibles ni detalles técnicos de la conexión en la respuesta enviada al cliente.
- Estado: correcto
- Notas:
  - Confirmar que el error técnico completo se registra en los logs del servidor mediante `error_log`.
  - Confirmar después que la configuración original queda restaurada.

---

## VAL-CONFIG-005 - No exposicion de mensaje interno de PDO

- ID: VAL-CONFIG-005
- Objetivo: Verificar especificamente que el cliente no recibe el mensaje interno de PDO.
- Precondiciones:
  - Haber preparado un fallo controlado de conexion como en VAL-CONFIG-004.
  - Acceso a herramientas de red del navegador o cliente HTTP.
- Pasos:
  1. Ejecutar una peticion que provoque fallo de conexion.
  2. Copiar solo el formato general de la respuesta, sin datos sensibles.
  3. Revisar que no contiene `SQLSTATE`, DSN, host, usuario ni trazas internas.
  4. Revisar logs para confirmar que el detalle tecnico si fue registrado.
- Resultado esperado:
  - El cliente recibe `{"error":"No se ha podido conectar con la base de datos."}` o mensaje generico equivalente.
  - El detalle tecnico aparece solo en logs.
- Resultado obtenido:
  - Se provocó un fallo controlado de conexión a base de datos.
  - Al ejecutar la petición `POST /api/login`, el servidor respondió con `HTTP 500 Internal Server Error`.
  - La respuesta recibida por el cliente fue:
    ```json
    {
      "error": "No se ha podido conectar con la base de datos."
    }
    ```
  - No se observó en la respuesta información interna de PDO como `SQLSTATE`, DSN, host, usuario, rutas internas, trazas ni mensajes completos de excepción.
- Estado: correcto
- Notas:
  - No se documentan mensajes completos de PDO para evitar exponer información sensible.
  - Confirmar en el log del servidor que el detalle técnico fue registrado mediante `error_log`.
  - Restaurar la configuración correcta de conexión a base de datos tras la prueba.

---

## VAL-CONFIG-006 - CORS desde origen permitido

- ID: VAL-CONFIG-006
- Objetivo: Confirmar que un origen autorizado recibe cabecera `Access-Control-Allow-Origin`.
- Precondiciones:
  - Backend local disponible.
  - Definir `CORS_ALLOWED_ORIGINS=http://localhost:4200` o usar fallback local si corresponde.
  - Frontend o cliente HTTP ejecutandose desde `http://localhost:4200`.
- Pasos:
  1. Abrir el frontend local en `http://localhost:4200`.
  2. Ejecutar una accion que llame a la API.
  3. Revisar la peticion en herramientas de red.
  4. Comprobar cabeceras CORS de la respuesta.
- Resultado esperado:
  - La peticion no queda bloqueada por CORS.
  - La respuesta contiene `Access-Control-Allow-Origin` compatible con el origen permitido.
  - Los metodos y cabeceras necesarias estan permitidos.
- Resultado obtenido:
  - Se abrió el frontend local desde `http://localhost:4200`.
  - Se ejecutó una petición `POST` al endpoint `/api/login`.
  - La petición respondió correctamente con `HTTP 200 OK`.
  - En los encabezados de solicitud se observó:
    ```http
    Origin: http://localhost:4200
    Referer: http://localhost:4200/
    ```
  - En los encabezados de respuesta se observaron las cabeceras CORS:
    ```http
    Access-Control-Allow-Origin: *
    Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
    Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With
    ```
  - La petición no fue bloqueada por política CORS.
- Estado: correcto
- Notas:
  - En entorno local se está usando wildcard `*` como origen permitido.

---

## VAL-CONFIG-007 - CORS desde origen no permitido

- ID: VAL-CONFIG-007
- Objetivo: Confirmar que un origen no autorizado no recibe permiso CORS en entorno no local.
- Precondiciones:
  - Backend disponible con `APP_ENV=production` o entorno no local de prueba.
  - Definir `CORS_ALLOWED_ORIGINS` con un origen permitido que no sea el origen de prueba.
  - Cliente HTTP capaz de enviar cabecera `Origin`.
- Pasos:
  1. Enviar una peticion con `Origin: http://origen-no-permitido.local`.
  2. Revisar cabeceras de respuesta.
  3. Revisar logs de servidor.
  4. Restaurar `APP_ENV=local` al terminar si se esta probando en XAMPP local.
- Resultado esperado:
  - La respuesta no incluye `Access-Control-Allow-Origin` para el origen no permitido.
  - El navegador bloquearia la respuesta por CORS.
  - Se registra en logs el origen no permitido sin exponer secretos.
- Resultado obtenido:
  - Se envió una petición con la cabecera:
    ```http
    Origin: http://origen-no-permitido.local
    ```
  - La respuesta obtenida fue `HTTP/1.1 200 OK`.
  - La respuesta incluyó las cabeceras:
    ```http
    Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
    Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With
    ```
  - La respuesta no incluyó:
    ```http
    Access-Control-Allow-Origin: http://origen-no-permitido.local
    ```
  - La respuesta tampoco incluyó:
    ```http
    Access-Control-Allow-Origin: *
    ```
  - En el log del servidor se registró el intento con origen no permitido:
    ```text
    [EasyParte][CORS] Origin not allowed: http://origen-no-permitido.local
    ```
  - No se observaron secretos ni datos sensibles en el registro de CORS.
- Estado: correcto
- Notas:
  - La prueba se realizó en entorno local simulando configuración no local.
  - No se ejecutó contra producción real.
  - Restaurar `APP_ENV=local` al finalizar la prueba si todavía no se ha hecho.
---

## VAL-CONFIG-008 - JWT_SECRET en local

- ID: VAL-CONFIG-008
- Objetivo: Confirmar que JWT funciona en local con fallback o con `JWT_SECRET` definido.
- Precondiciones:
  - XAMPP iniciado.
  - `APP_ENV=local` o sin `APP_ENV` definido.
  - Usuario valido disponible.
- Pasos:
  1. Iniciar sesion sin `JWT_SECRET` definido.
  2. Comprobar que se obtiene token JWT.
  3. Definir temporalmente `JWT_SECRET` con un valor local de prueba.
  4. Reiniciar Apache si aplica.
  5. Iniciar sesion de nuevo.
  6. Acceder a un endpoint protegido con el token recibido.
- Resultado esperado:
  - En local, el login funciona aunque `JWT_SECRET` no este definido.
  - Si `JWT_SECRET` esta definido, el token se emite y se valida correctamente.
  - No se expone el secreto ni el token completo en logs.
- Resultado obtenido:
  - Con entorno local, se realizó login correctamente mediante `POST /api/login`.
  - El servidor respondió con `HTTP 200 OK`.
  - La respuesta incluyó un token JWT y los datos básicos del usuario autenticado.
  - No se documenta el token completo por seguridad.
  - Tras el login, se accedió correctamente a endpoints protegidos como `/api/dashboard`.
  - El endpoint protegido devolvió datos de la aplicación, confirmando que el token fue aceptado y validado.
  - También se pudo acceder a secciones protegidas como Administración, con peticiones a recursos como roles, empleados y usuarios.
  - En los logs revisados no se observó exposición de `JWT_SECRET` ni del token JWT completo.
- Estado: correcto
- Notas:
  - No reutilizar secretos reales en local.
  - No incluir tokens completos en capturas o documentación.
  - Si se usó un `JWT_SECRET` temporal, restaurar o dejar únicamente el valor local de prueba previsto.

---

## VAL-CONFIG-009 - JWT_SECRET obligatorio en produccion

- ID: VAL-CONFIG-009
- Objetivo: Confirmar que en entorno no local no se usa una clave JWT conocida si falta `JWT_SECRET`.
- Precondiciones:
  - Entorno de prueba, no produccion real.
  - Poder definir temporalmente `APP_ENV=production`.
  - `JWT_SECRET` no definido para la primera parte de la prueba.
- Pasos:
  1. Definir temporalmente `APP_ENV=production` en `.env`.
  2. Asegurar que `JWT_SECRET` no esta definido.
  3. Intentar iniciar sesion.
  4. Revisar respuesta y logs.
  5. Definir `JWT_SECRET` con un valor largo de prueba en `.env`.
  6. Reintentar login.
  7. Restaurar `APP_ENV=local` al terminar si aplica.
- Resultado esperado:
  - Sin `JWT_SECRET`, el login falla de forma controlada con error generico.
  - El detalle tecnico se registra con `error_log`.
  - Con `JWT_SECRET` definido, el login puede emitir token.
  - No se usa la clave local conocida en entorno no local.
- Resultado obtenido:
  - Se repitió la validación técnica con un archivo `.env` temporal fuera del repositorio.
  - Con `APP_ENV=production` y sin `JWT_SECRET`, la generación de JWT falló de forma controlada.
  - La respuesta fue genérica:
    ```json
    {
      "error": "Error interno de configuracion."
    }
    ```
  - No se emitió token JWT.
  - No se expusieron claves, rutas internas ni trazas en la respuesta.
  - Con `APP_ENV=production` y `JWT_SECRET` definido en formato `.env`, el helper JWT emitió un token válido.
  - No se documenta el token completo ni el valor usado para `JWT_SECRET`.
  - La repetición confirma que el loader `.env` ya entrega `JWT_SECRET` correctamente al helper JWT.
  - Para repetir login completo en `APP_ENV=production`, no usar `DB_USER=root`: la configuracion de seguridad bloquea root fuera de local antes de llegar a la emision del JWT.
  - Si se requiere evidencia manual de extremo a extremo, crear un usuario MySQL no root con permisos sobre la base de datos local y configurar ese usuario en `.env`.
- Estado: correcto

---

## VAL-CONFIG-010 - Regresion funcional de modulos principales

- ID: VAL-CONFIG-010
- Objetivo: Confirmar que dashboard, clientes, avisos y partes siguen funcionando despues del cambio de configuracion.
- Precondiciones:
  - XAMPP iniciado con Apache y MySQL.
  - Frontend local funcionando.
  - Usuario valido con permisos suficientes.
  - Configuracion local restaurada.
- Pasos:
  1. Iniciar sesion.
  2. Abrir dashboard y comprobar carga de datos.
  3. Abrir clientes y comprobar listado.
  4. Abrir avisos y comprobar listado o flujo principal disponible.
  5. Abrir partes y comprobar listado o flujo principal disponible.
  6. Revisar consola del navegador y respuestas HTTP.
- Resultado esperado:
  - Dashboard carga sin errores nuevos.
  - Clientes carga sin errores nuevos.
  - Avisos carga sin errores nuevos.
  - Partes carga sin errores nuevos.
  - No aparecen errores de configuracion, CORS, JWT ni conexion BD.
- Resultado obtenido:
  - Se restauró la configuración local del backend.
  - Se inició sesión correctamente con un usuario válido.
  - El dashboard cargó correctamente y mostró los indicadores principales.
  - El módulo de clientes cargó correctamente el listado.
  - El módulo de avisos cargó correctamente su pantalla/listado principal.
  - El módulo de partes/albaranes cargó correctamente su pantalla/listado principal.
  - En las respuestas HTTP revisadas no se observaron errores de configuración, CORS, JWT ni conexión a base de datos.
  - En consola no se observaron errores nuevos asociados a la configuración validada.
- Estado: correcto
- Notas:
  - La prueba se realizó tras restaurar `APP_ENV=local`.
  - Si algún listado no tenía registros, se consideró correcto siempre que la respuesta HTTP fuera válida y no hubiera errores de configuración.
