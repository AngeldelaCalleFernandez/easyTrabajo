# AGENT — Gobierno de agentes para EasyParte

## 1. Propósito

Esta carpeta define cómo debe trabajar Codex o cualquier agente de IA sobre el repositorio `easyTrabajo`, cuyo producto real es **EasyParte**.

EasyParte está en fase de profesionalización: pasa de un TFC funcional a una aplicación SaaS profesional, mantenible, desplegable y con control real de seguridad, permisos, multiempresa, suscripciones, auditoría y trazabilidad.

El objetivo de `AGENT/` no es que Codex modifique libremente el proyecto, sino darle contexto, límites, criterios de revisión y una forma ordenada de documentar cada tarea.

---

## 2. Regla principal

> Ningún agente debe modificar código sin entender antes el contexto funcional, técnico y de negocio de EasyParte.

Antes de cualquier cambio, el agente debe leer la documentación base del proyecto y respetar este orden de prioridad:

1. Reglas de negocio.
2. Roles y permisos.
3. Modelo SaaS.
4. Entidades / E-R.
5. Arquitectura.
6. Seguridad y producción.
7. Roadmap.
8. Estilo visual.

Si falta información, no se inventa. Se registra como pendiente o duda en los documentos correspondientes.

---

## 3. Estructura real de la carpeta

```txt
AGENT/
├── README.md
├── agentes/
│   ├── agente_auditor.md
│   ├── agente_backend.md
│   ├── agente_frontend.md
│   └── agente_orquestador.md
├── arquitectura/
│   ├── arquitectura.md
│   ├── estilo_visual.md
│   └── trazabilidad_y_auditoria.md
├── checklists/
│   ├── checklist_auditoria.md
│   ├── checklist_backend.md
│   ├── checklist_frontend.md
│   └── checklist_pre_produccion.md
├── contexto/
│   ├── alcance.md
│   ├── decisiones_tecnicas.md
│   ├── dudas_pendientes.md
│   ├── endpoints_api.md
│   ├── entidades.md
│   ├── estados_del_sistema.md
│   ├── glosario.md
│   ├── modelo_saas.md
│   ├── modulos_futuros.md
│   ├── negocio.md
│   ├── objetivos.md
│   ├── reglas_de_negocio.md
│   ├── roadmap_funcional.md
│   └── roles_y_permisos.md
├── despliegue/
│   ├── backup_y_restauracion.md
│   ├── despliegue.md
│   ├── entornos.md
│   ├── seguridad_produccion.md
│   └── variables_entorno.md
├── diagramas/
│   ├── arquitectura_objetivo.mmd
│   ├── ciclo_aviso_parte_objetivo.mmd
│   ├── EASYPARTE_ER_DEFINITIVO.mmd
│   ├── flujo_autenticacion_acceso_objetivo.mmd
│   └── frontend_objetivo.mmd
├── repositorio/
│   ├── convenciones_commits.md
│   ├── estrategia_ramas.md
│   └── repositorio.md
├── seguimiento/
│   ├── implementado_backend.md
│   ├── implementado_frontend.md
│   ├── incidencias.md
│   ├── pendientes.md
│   ├── registro_cambios_agentes.md
│   └── roadmap_tecnico.md
└── testing/
    ├── casos_prueba.md
    └── testing.md
```

---

## 4. Función de cada carpeta

### `agentes/`

Define el comportamiento de cada agente.

- `agente_orquestador.md`: coordina tareas, prioriza, divide trabajo y evita cambios grandes sin control.
- `agente_backend.md`: revisa y trabaja sobre PHP, API REST, seguridad backend, controladores, servicios, repositorios y base de datos desde código.
- `agente_frontend.md`: revisa y trabaja sobre Angular, componentes, servicios, guards, interceptores, formularios, rutas y estilo visual.
- `agente_auditor.md`: revisa seguridad, permisos, trazabilidad, errores, riesgos y cumplimiento de reglas sin asumir que todo está bien.

### `contexto/`

Contiene la fuente principal de verdad del producto.

Aquí están las reglas de negocio, roles, modelo SaaS, entidades, estados, endpoints, alcance, objetivos y dudas pendientes.

Esta carpeta debe leerse antes de tomar decisiones funcionales.

### `arquitectura/`

Define la arquitectura objetivo y las reglas de estructura técnica.

Incluye la separación esperada entre frontend, backend, base de datos, auditoría, trazabilidad e identidad visual.

### `despliegue/`

Contiene la documentación relacionada con producción, entornos, variables, backups, restauración y seguridad de despliegue.

Codex no debe modificar configuraciones de producción sin revisar esta carpeta.

### `checklists/`

Contiene listas de comprobación para revisar backend, frontend, auditoría y preproducción.

Sirve para validar si una tarea está realmente terminada.

### `repositorio/`

Define cómo debe usarse Git en el proyecto.

Incluye convenciones de commits, estrategia de ramas y reglas de trabajo sobre el repositorio.

### `seguimiento/`

Carpeta viva de seguimiento técnico.

Aquí Codex debe registrar qué está implementado, qué está pendiente, qué incidencias existen y qué cambios han hecho los agentes.

### `testing/`

Define la estrategia de pruebas y casos de prueba.

Codex debe consultar esta carpeta antes de dar por finalizada una modificación importante.

### `diagramas/`

Contiene diagramas Mermaid del sistema objetivo.

Sirven como apoyo visual para entender arquitectura, flujo, frontend, ciclo aviso-parte y modelo E/R.

---

## 5. Orden de lectura recomendado para Codex

Antes de modificar código, Codex debe leer como mínimo:

1. `AGENT/README.md`
2. `AGENT/contexto/reglas_de_negocio.md`
3. `AGENT/contexto/roles_y_permisos.md`
4. `AGENT/contexto/modelo_saas.md`
5. `AGENT/contexto/entidades.md`
6. `AGENT/contexto/decisiones_tecnicas.md`
7. `AGENT/arquitectura/arquitectura.md`
8. `AGENT/arquitectura/trazabilidad_y_auditoria.md`
9. `AGENT/despliegue/seguridad_produccion.md`
10. `AGENT/repositorio/estrategia_ramas.md`
11. `AGENT/seguimiento/pendientes.md`

Si la tarea afecta a frontend, también debe leer:

- `AGENT/arquitectura/estilo_visual.md`
- `AGENT/checklists/checklist_frontend.md`
- `AGENT/contexto/endpoints_api.md`

Si la tarea afecta a backend, también debe leer:

- `AGENT/checklists/checklist_backend.md`
- `AGENT/contexto/endpoints_api.md`
- `AGENT/contexto/estados_del_sistema.md`

Si la tarea afecta a seguridad, permisos, empresas, suscripciones, firmas, hashes, auditoría o despliegue, debe tratarse como cambio crítico.

---

## 6. Forma de trabajo obligatoria

Los agentes deben trabajar así:

1. Leer documentación antes de tocar código.
2. Trabajar por tareas pequeñas.
3. No modificar frontend, backend y base de datos a la vez salvo tarea justificada.
4. No cambiar reglas de negocio sin aprobación humana.
5. No eliminar tablas, columnas, datos o endpoints sin aprobación.
6. No inventar roles, estados, permisos, entidades ni límites de plan.
7. No marcar nada como implementado sin comprobarlo en el código.
8. Documentar cada cambio realizado.
9. Indicar riesgos.
10. Proponer pruebas manuales o automatizadas.
11. Esperar revisión humana en cambios críticos.

---

## 7. Primera tarea obligatoria

La primera intervención de Codex sobre el repositorio debe ser una auditoría en modo solo lectura.

Codex debe revisar la estructura real del proyecto y completar únicamente:

- `AGENT/seguimiento/implementado_backend.md`
- `AGENT/seguimiento/implementado_frontend.md`
- `AGENT/seguimiento/incidencias.md`
- `AGENT/seguimiento/pendientes.md`

No debe modificar código hasta que esta auditoría inicial esté revisada por una persona.

---

## 8. Cambios críticos

Se consideran cambios críticos:

- autenticación;
- JWT, sesión o cookies;
- permisos;
- roles;
- empresa activa;
- multiempresa;
- suscripción;
- planes;
- límites de plan;
- base de datos;
- migraciones;
- borrados;
- partes cerrados;
- firmas;
- hashes;
- auditoría;
- despliegue;
- CORS;
- variables de entorno;
- tratamiento de errores internos.

Un cambio crítico debe hacerse en una tarea separada, con explicación, riesgo, prueba y revisión humana.

---

## 9. Qué no debe hacer Codex

Codex no debe:

- convertir el proyecto a otra tecnología sin aprobación;
- migrar a Laravel sin que se le pida expresamente;
- cambiar Angular por React/Vue;
- cambiar MariaDB/MySQL por otra base de datos;
- borrar datos demo o scripts sin explicar impacto;
- introducir dependencias grandes sin justificarlo;
- crear roles nuevos por su cuenta;
- inventar estados de avisos, partes, presupuestos o suscripciones;
- exponer errores SQL o trazas;
- confiar en `id_empresa` recibido desde frontend;
- resolver permisos solo ocultando botones en Angular;
- modificar base de datos sin revisión;
- romper el estilo visual verde/lima definido para EasyParte;
- mezclar cambios de seguridad, frontend, backend y base de datos en una sola tarea grande.

---

## 10. Estados de seguimiento

En los documentos de seguimiento se deben usar estos estados:

```txt
pendiente
implementado
parcial
dudoso
bloqueado
```

Significado:

- `pendiente`: no existe o no se ha trabajado todavía.
- `implementado`: existe y se ha comprobado en el código.
- `parcial`: existe una parte, pero falta completar, validar o proteger.
- `dudoso`: no hay evidencia suficiente o el comportamiento no está claro.
- `bloqueado`: no puede avanzarse sin decisión, información o revisión humana.

---

## 11. Resultado esperado de cada tarea

Cada intervención debe terminar con este formato:

```txt
Resumen:
- Qué se ha cambiado.

Archivos modificados:
- ruta/archivo.ext

Motivo:
- Por qué era necesario.

Riesgos:
- Qué podría fallar.

Pruebas:
- Cómo comprobarlo.

Pendiente:
- Qué queda por revisar.
```

Si no se modifica código y solo se audita, debe indicarse claramente:

```txt
No se ha modificado código.
Auditoría realizada sobre:
- rutas revisadas
- archivos revisados
- incidencias detectadas
- pendientes añadidos
```

---

## 12. Registro de cambios

Todo cambio realizado por agentes debe registrarse en:

```txt
AGENT/seguimiento/registro_cambios_agentes.md
```

Formato mínimo:

```txt
Fecha:
Agente:
Rama:
Tipo de cambio:
Resumen:
Archivos modificados:
Motivo:
Pruebas realizadas:
Riesgos:
Estado:
Siguiente paso:
```

No debe registrarse como finalizado un cambio que no compile, no pueda probarse o dependa de una decisión pendiente.

---

## 13. Incidencias y pendientes

Las incidencias deben registrarse en:

```txt
AGENT/seguimiento/incidencias.md
```

Los pendientes técnicos deben registrarse en:

```txt
AGENT/seguimiento/pendientes.md
```

Diferencia:

- `incidencias.md`: errores, fallos, comportamientos incorrectos o riesgos detectados.
- `pendientes.md`: tareas futuras, mejoras, documentación, testing, despliegue o decisiones técnicas.

Las dudas funcionales o de negocio deben registrarse en:

```txt
AGENT/contexto/dudas_pendientes.md
```

---

## 14. Recomendación de ramas

Para cada tarea, crear una rama específica:

```txt
feature/nombre-tarea
fix/nombre-bug
refactor/nombre-refactor
security/nombre-ajuste
docs/nombre-documentacion
```

No trabajar directamente sobre `main`.

Antes de crear ramas o pull requests, revisar:

```txt
AGENT/repositorio/estrategia_ramas.md
AGENT/repositorio/convenciones_commits.md
```

---

## 15. Criterios para dar una tarea por terminada

Una tarea no debe darse por terminada hasta que:

1. El código compile o se indique claramente que no se ha podido comprobar.
2. El backend responda correctamente si la tarea afecta a API.
3. El frontend cargue sin errores si la tarea afecta a Angular.
4. Se hayan revisado permisos y empresa si afecta a datos de negocio.
5. No se expongan errores internos.
6. Se hayan propuesto pruebas.
7. Se haya actualizado documentación de seguimiento.
8. Se haya indicado cualquier riesgo pendiente.

---

## 16. Estado inicial

Esta carpeta es documentación de gobierno. No implementa funcionalidad por sí sola.

Sirve para que EasyParte crezca de forma controlada y para que Codex pueda trabajar sin actuar libremente sobre todo el proyecto.

El primer uso recomendado es:

```txt
Auditoría inicial en modo solo lectura.
```

Después de esa auditoría, se podrán crear tareas pequeñas y ramas concretas para corregir problemas, mejorar seguridad, refactorizar backend, refactorizar frontend, añadir testing o preparar despliegue.
