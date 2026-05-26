# frontend/AGENTS.md — Frontend EasyParte

## Propósito

Instrucciones específicas para trabajar dentro de `frontend/`.

El frontend de EasyParte está hecho con Angular, standalone components, TypeScript, Reactive Forms, Signals, Tailwind CSS y Chart.js.

---

## Documentos que debes consultar

Para tareas frontend lee, según necesidad:

```txt
../docs/agentes/agente_frontend.md
../docs/arquitectura/estilo_visual.md
../docs/arquitectura/arquitectura.md
../docs/contexto/roles_y_permisos.md
../docs/contexto/endpoints_api.md
../docs/testing/frontend_api_url.md
```

---

## Reglas obligatorias

- Mantener Angular con standalone components.
- Mantener estructura por `core`, `features`, `layouts` y `shared`.
- Usar servicios para llamadas HTTP.
- Usar guards e interceptor para sesión.
- No convertir Angular en fuente de seguridad real.
- Ocultar opciones por rol solo como mejora visual.
- Mantener estilo verde/lima, tarjetas blancas, modales limpios y tablas claras.
- Evitar `any` nuevo si puede tiparse de forma razonable.
- No hardcodear nuevas URLs de API si se está centralizando configuración.
- Mantener compatibilidad con el backend actual salvo tarea contraria.

---

## Zonas sensibles

Revisar con especial cuidado:

```txt
src/app/core/services/
src/app/core/guards/
src/app/core/interceptors/
src/app/features/avisos/
src/app/features/albaranes/
src/app/features/administracion/
src/app/features/dashboard/
```

---

## Estilo visual

Mantener:

- navbar verde oscuro;
- acentos lima;
- fondos claros;
- cards blancas;
- bordes suaves;
- sombras ligeras;
- modales para crear/editar;
- alertas claras y no técnicas.

Evitar:

- interfaces recargadas;
- colores nuevos sin criterio;
- textos técnicos tipo `payload inválido`;
- mensajes de error SQL;
- duplicar componentes visuales sin necesidad.

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
