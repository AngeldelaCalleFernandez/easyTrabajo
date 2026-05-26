# Validacion frontend API URL - INC-0012

Fecha: 2026-05-25
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
| FE-APIURL-003 | Build Angular | `npm run build` finaliza correctamente | Build correcto | Correcta | Primer intento en sandbox fallo con `spawn EPERM`; repetido fuera del sandbox con aprobacion y finalizo correctamente. |

## Pruebas manuales pendientes

| ID | Prueba | Resultado esperado | Estado | Observaciones |
|---|---|---|---|---|
| FE-APIURL-004 | Login | Login sigue llamando a la API local y redirige a dashboard | Pendiente | Requiere XAMPP/backend y navegador. |
| FE-APIURL-005 | Dashboard | Dashboard carga datos desde `/dashboard` | Pendiente | Requiere sesion valida. |
| FE-APIURL-006 | Clientes | Clientes lista, crea, edita y elimina usando la API | Pendiente | No ejecutar acciones destructivas sin datos de prueba. |
| FE-APIURL-007 | Avisos | Avisos lista, crea, actualiza estado y elimina/cancela usando la API | Pendiente | Validar con datos locales. |
| FE-APIURL-008 | Partes/albaranes | Partes carga, crea y actualiza usando la API | Pendiente | Validar flujo actual sin cambiar contratos. |
| FE-APIURL-009 | Administracion | Roles, empleados y usuarios cargan desde la API | Pendiente | Validar con rol permitido. |

## Estado recomendado

INC-0012 puede pasar a `en revision`.

No debe pasar a `resuelta` hasta completar las pruebas manuales pendientes y documentar resultados reales.
