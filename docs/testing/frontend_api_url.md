# Validacion frontend API URL - INC-0012

Fecha: 2026-05-26
Agente: Codex / agente frontend EasyParte
Rama: security/tenant-minimo
Incidencia relacionada: INC-0012

## Objetivo

Validar que la URL base de la API del frontend queda centralizada en un unico punto y que los servicios Angular mantienen el comportamiento actual con XAMPP.

## Configuracion aplicada

URL local mantenida:

```txt
http://localhost/easyTrabajo/backend/public/api
```

Punto unico de configuracion:

```txt
frontend/src/app/core/config/api.config.ts
```

Servicios revisados:

- `frontend/src/app/core/services/auth.service.ts`
- `frontend/src/app/core/services/admin.service.ts`
- `frontend/src/app/core/services/clientes.service.ts`
- `frontend/src/app/core/services/avisos.service.ts`
- `frontend/src/app/core/services/partes.service.ts`
- `frontend/src/app/core/services/dashboard.service.ts`

## Pruebas realizadas

| ID | Prueba | Resultado esperado | Resultado obtenido | Estado | Observaciones |
|---|---|---|---|---|---|
| FE-APIURL-001 | Revision estatica de servicios | No quedan URLs hardcodeadas en `frontend/src/app/core/services` | La URL solo aparece en `frontend/src/app/core/config/api.config.ts` | Correcta | Ejecutado con `rg "http://localhost/easyTrabajo/backend/public/api" frontend/src/app/core/services frontend/src/app/core/config`. |
| FE-APIURL-002 | Revision de imports/configuracion | Los servicios usan `API_BASE_URL` | Los seis servicios importan y usan `API_BASE_URL` | Correcta | No se cambiaron nombres de metodos, endpoints ni payloads. |
| FE-APIURL-003 | Build Angular | `npm run build` finaliza correctamente | Build correcto el 2026-05-26 | Correcta | Ejecutado en `frontend` con la URL centralizada actual. |

Nota: se intento crear la rama recomendada `refactor/frontend-api-url`, pero Git no permitio crear `refs/heads/refactor/frontend-api-url`. No se forzo ni se elimino ninguna referencia.

## Pruebas manuales ejecutadas

Estas pruebas fueron ejecutadas manualmente por la persona responsable del proyecto. Codex solo registra los resultados aportados.

| ID | Prueba | Resultado esperado | Resultado obtenido | Estado | Observaciones |
|---|---|---|---|---|---|
| FE-APIURL-004 | Login | Login sigue llamando a la API local y redirige a dashboard | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |
| FE-APIURL-005 | Dashboard | Dashboard carga datos desde `/dashboard` | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |
| FE-APIURL-006 | Clientes | Clientes lista, crea, edita y elimina usando la API | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |
| FE-APIURL-007 | Avisos | Avisos lista, crea, actualiza estado y elimina/cancela usando la API | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |
| FE-APIURL-008 | Partes/albaranes | Partes carga, crea y actualiza usando la API | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |
| FE-APIURL-009 | Administracion | Roles, empleados y usuarios cargan desde la API | Correcto | Correcta | Sin errores derivados de `API_BASE_URL`. |

## Estado recomendado

INC-0012 puede pasar a `resuelta`.

No quedan pruebas manuales pendientes asociadas a la centralizacion de `API_BASE_URL`.
