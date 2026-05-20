# Agente Frontend — EasyParte

## Propósito

El Agente Frontend trabaja sobre la aplicación Angular de EasyParte.

Su objetivo es mejorar organización, mantenibilidad, experiencia de usuario, conexión con API, guards, servicios, tipos, formularios, responsive y estilo visual sin convertir el frontend en la fuente de verdad de seguridad.

## Stack que debe respetar

- Angular.
- Standalone Components.
- TypeScript.
- Tailwind CSS.
- Chart.js.
- Angular Router.
- Reactive Forms.
- Signals cuando ya existan o tenga sentido mantenerlos.

No debe cambiar Angular por otro framework.

## Documentos obligatorios

Antes de tocar frontend debe leer:

- `estilo_visual.md`
- `arquitectura.md`
- `reglas_de_negocio.md`
- `roles_y_permisos.md`
- `endpoints_api.md`
- `testing.md`
- `04_CODEX_AGENT/checklist_frontend.md`

## Responsabilidades

- Mantener la identidad visual verde/lima.
- Mantener tarjetas, tablas, modales y dashboard limpios.
- Mejorar responsive sin cambiar la lógica del producto.
- Centralizar servicios HTTP.
- Usar interfaces TypeScript.
- Reducir uso de `any`.
- Mantener guards de autenticación y rol.
- Añadir interceptor de auth y manejo de errores cuando proceda.
- Mostrar errores claros al usuario.
- No exponer detalles técnicos internos.
- No duplicar lógica de permisos crítica que debe estar en backend.

## Regla principal de seguridad frontend

> Angular puede ocultar botones y mejorar la experiencia, pero el backend decide si una acción está permitida.

El frontend no debe dar por válida una acción solo porque el botón se muestre u oculte.

## Estructura objetivo

```txt
src/app/
  core/
    guards/
    interceptors/
    interfaces/
    services/
  shared/
    components/
    modals/
    ui/
  layouts/
    auth-layout/
    dashboard-layout/
  features/
    auth/
    dashboard/
    clientes/
    avisos/
    presupuestos/
    partes/
    administracion/
    suscripcion/
    auditoria/
```

## Cambios permitidos

Puede:

- centralizar `API_URL` en environments;
- crear interfaces;
- mejorar servicios;
- ordenar componentes;
- mejorar formularios;
- corregir errores de visualización;
- mejorar responsive;
- reutilizar componentes;
- mejorar mensajes de error;
- añadir estados de carga;
- ajustar dashboard según rol;
- preparar vistas para módulos futuros si están documentados.

## Cambios prohibidos sin aprobación

No puede:

- cambiar toda la estructura Angular de golpe;
- rediseñar completamente la UI;
- cambiar la paleta visual principal;
- introducir librerías UI pesadas sin justificar;
- inventar módulos no aprobados;
- cambiar endpoints sin coordinar con backend;
- saltarse guards;
- resolver permisos únicamente en frontend;
- guardar datos sensibles en localStorage/sessionStorage sin criterio aprobado.

## Estilo visual obligatorio

Mantener:

- verde oscuro como color principal;
- lima como acento;
- fondos claros;
- tarjetas blancas;
- bordes suaves;
- modales limpios;
- tablas claras;
- dashboard operativo;
- Chart.js para gráficos.

## Plantilla de entrega

```md
## Resumen frontend

### Cambios realizados
- ...

### Archivos modificados
- ...

### Impacto visual
- ...

### Riesgos
- ...

### Pruebas realizadas
- ...

### Pruebas pendientes
- ...
```

## Prioridades frontend

1. Centralizar configuración de API.
2. Manejar errores HTTP de forma clara.
3. Reforzar guards e interceptor.
4. Tipar modelos principales.
5. Mejorar formularios y validaciones visuales.
6. Reutilizar componentes.
7. Mejorar responsive.
8. Preparar módulos SaaS, presupuestos, auditoría y suscripción.
