# backend/AGENTS.md — Backend EasyParte

## Propósito

Instrucciones específicas para trabajar dentro de `backend/`.

El backend de EasyParte es una API REST en PHP vanilla orientado a objetos. Usa PDO, JWT propio, middlewares y MariaDB/MySQL.

---

## Documentos que debes consultar

Para tareas backend lee, según necesidad:

```txt
../docs/agentes/agente_backend.md
../docs/arquitectura/arquitectura.md
../docs/contexto/reglas_de_negocio.md
../docs/contexto/endpoints_api.md
../docs/contexto/roles_y_permisos.md
../docs/contexto/modelo_saas.md
../docs/arquitectura/trazabilidad_y_auditoria.md
../docs/despliegue/seguridad_produccion.md
```

---

## Reglas obligatorias

- No devolver errores SQL, trazas PHP ni mensajes internos al cliente.
- Usar respuestas JSON homogéneas.
- Usar PDO con consultas preparadas.
- No confiar en `id_empresa` enviado desde frontend.
- Obtener usuario, empresa y rol desde token, sesión o contexto validado.
- Filtrar toda consulta sensible por empresa.
- Validar permisos en backend, no solo en Angular.
- Validar pertenencia de IDs a la empresa activa.
- Técnicos solo ven datos propios.
- Administradores ven datos de su empresa, no de otras.
- Registrar acciones críticas en auditoría cuando proceda.
- No mezclar grandes refactors con correcciones pequeñas.

---

## Estructura actual

```txt
backend/
  config/
  controllers/
  helpers/
  middleware/
  models/
  public/
  routes/
```

## Estructura objetivo progresiva

```txt
backend/
  controllers/
  services/
  repositories/
  middleware/
  helpers/
  config/
  public/
  routes/
```

No crees toda la estructura objetivo de golpe salvo que la tarea lo pida.

---

## Seguridad mínima

Revisar especialmente:

```txt
helpers/jwt.php
middleware/AuthMiddleware.php
config/cors.php
config/env.php
controllers/*Controller.php
routes/api.php
```

Puntos críticos:

- expiración del token;
- secreto JWT desde entorno;
- CORS por entorno;
- mensajes de error genéricos;
- filtrado por `id_empresa`;
- control de roles;
- protección de administración;
- no aceptar IDs sensibles del body sin validar.

---

## Formato de salida tras trabajar

Al terminar, responde con:

```txt
Archivos modificados:
- ...

Qué se ha cambiado:
- ...

Riesgos:
- ...

Pruebas recomendadas:
- ...
```
