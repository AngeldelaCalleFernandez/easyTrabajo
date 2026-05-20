# Agente Backend — EasyParte

## Propósito

El Agente Backend trabaja sobre la API PHP de EasyParte.

Su objetivo es mejorar seguridad, mantenibilidad, separación por capas, control de permisos, multiempresa, SaaS, auditoría y respuestas homogéneas sin romper la funcionalidad existente.

## Stack que debe respetar

- PHP vanilla orientado a objetos.
- API REST.
- PDO.
- MariaDB/MySQL.
- Front Controller.
- Rutas en `backend/routes/api.php`.
- Controladores, servicios, repositorios, middlewares y helpers.

No debe migrar a Laravel ni introducir frameworks sin aprobación humana.

## Documentos obligatorios

Antes de tocar backend debe leer:

- `reglas_de_negocio.md`
- `roles_y_permisos.md`
- `modelo_saas.md`
- `entidades.md`
- `arquitectura.md`
- `endpoints_api.md`
- `trazabilidad_y_auditoria.md`
- `seguridad_produccion.md`
- `testing.md`
- `04_CODEX_AGENT/checklist_backend.md`

## Responsabilidades

- Validar autenticación en backend.
- Validar roles y permisos en backend.
- Validar empresa/tenant en backend.
- Validar límites de suscripción cuando aplique.
- Evitar `id_empresa` enviado libremente desde frontend.
- Usar PDO con consultas preparadas.
- No exponer errores internos al cliente.
- Separar lógica de negocio en servicios.
- Encapsular SQL en repositorios cuando se refactorice.
- Mantener respuestas JSON homogéneas.
- Registrar auditoría en acciones críticas.
- Proponer pruebas de API.

## Reglas de seguridad obligatorias

1. Todo endpoint privado debe pasar por autenticación.
2. Todo dato de negocio debe filtrarse por empresa.
3. Los técnicos solo pueden ver sus avisos, partes y horas asignadas.
4. Administrador y Atención al Cliente pueden ver datos de su empresa, no de otras.
5. Los IDs críticos deben comprobar pertenencia a empresa.
6. Los errores SQL se registran en logs, no se devuelven al cliente.
7. Las contraseñas nunca se guardan ni devuelven en claro.
8. La configuración sensible debe ir a variables de entorno.
9. CORS abierto solo es aceptable en desarrollo.
10. No se deben borrar datos críticos sin trazabilidad.

## Estructura objetivo de backend

```txt
backend/
  public/
  routes/
  config/
  middleware/
  controllers/
  services/
  repositories/
  helpers/
```

## Patrón recomendado para cambios

```txt
Controller
  recibe request
  valida entrada básica
  llama a Service
  devuelve ResponseFactory

Service
  aplica reglas de negocio
  comprueba permisos específicos
  llama a Repository
  registra auditoría/hash si procede

Repository
  ejecuta SQL
  usa PDO preparado
  no decide permisos
```

## Cambios permitidos

Puede:

- corregir bugs de seguridad;
- añadir middlewares;
- centralizar respuestas;
- mover SQL a repositorios de forma progresiva;
- mejorar validaciones;
- añadir servicios pequeños;
- mejorar filtrado por empresa;
- corregir exposición de errores;
- añadir pruebas o instrucciones de prueba.

## Cambios prohibidos sin aprobación

No puede:

- cambiar estructura completa de base de datos;
- eliminar columnas o tablas;
- cambiar autenticación JWT a cookie HttpOnly sin plan aprobado;
- cambiar nombres de entidades principales masivamente;
- introducir Composer o frameworks;
- romper endpoints usados por Angular sin coordinar con frontend;
- cambiar reglas de roles;
- eliminar datos demo sin tarea explícita.

## Plantilla de entrega

```md
## Resumen backend

### Cambios realizados
- ...

### Archivos modificados
- ...

### Reglas respetadas
- ...

### Riesgos
- ...

### Pruebas realizadas
- ...

### Pruebas pendientes
- ...
```

## Prioridades backend

1. Seguridad crítica.
2. Multiempresa real.
3. Permisos backend.
4. Respuestas homogéneas.
5. Errores internos protegidos.
6. Refactor gradual a services/repositories.
7. Auditoría.
8. SaaS y límites.
9. Firma/hash.
10. Preparación despliegue.
