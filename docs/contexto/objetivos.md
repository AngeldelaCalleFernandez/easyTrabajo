# objetivos.md

# EasyParte — Objetivos del proyecto

## 1. Propósito del documento

Este documento define los objetivos de EasyParte en su proceso de profesionalización.

La finalidad es separar claramente:

- lo que ya existe en el proyecto TFC;
- lo que debe mantenerse;
- lo que debe refactorizarse;
- lo que debe añadirse para una versión profesional;
- lo que queda como idea futura.

Este archivo debe servir como guía para ChatGPT, Codex o cualquier agente que trabaje en el proyecto. Ningún agente debe asumir como obligatorio algo que esté marcado como futuro o pendiente de decisión.

---

## 2. Objetivo general

Convertir EasyParte de un proyecto TFC funcional en una aplicación web profesional, mantenible y preparada para despliegue, orientada a empresas de servicios que necesitan gestionar clientes, avisos, presupuestos, partes de trabajo, técnicos, materiales, roles, suscripciones y trazabilidad.

La aplicación debe conservar la lógica principal del proyecto original, pero mejorar su arquitectura, seguridad, base de datos, control de permisos, auditoría y capacidad de crecimiento.

---

## 3. Visión del producto

EasyParte debe evolucionar hacia una herramienta SaaS multiempresa.

La visión del producto es que una empresa pueda contratar EasyParte y utilizarlo para gestionar su operativa diaria:

1. Dar de alta usuarios, empleados, departamentos y equipos.
2. Registrar clientes.
3. Crear avisos o incidencias.
4. Asignar técnicos o equipos.
5. Crear presupuestos básicos.
6. Generar partes de trabajo.
7. Registrar horas, materiales y observaciones.
8. Firmar partes.
9. Controlar estados y trazabilidad.
10. Exportar datos a sistemas de facturación.
11. Trabajar según los límites de su plan contratado.

---

## 4. Objetivos principales

Los objetivos principales son:

1. Mantener la lógica de negocio de EasyParte.
2. Separar bien empresas, usuarios y datos.
3. Profesionalizar la base de datos.
4. Añadir modelo SaaS con planes y suscripciones.
5. Mejorar roles y permisos.
6. Hacer que la seguridad dependa del backend.
7. Añadir trazabilidad y auditoría.
8. Añadir presupuestos básicos.
9. Profesionalizar partes de trabajo y firma.
10. Preparar futuras integraciones con facturación.
11. Mantener el estilo visual actual, pero más limpio y consistente.
12. Preparar el proyecto para que Codex pueda trabajar por tareas pequeñas y revisables.

---

## 5. Objetivos a corto plazo

Estos objetivos corresponden a la primera fase de profesionalización.

## 5.1. Ordenar documentación

Crear y mantener los documentos base del proyecto:

- negocio.md;
- modelo_saas.md;
- reglas_de_negocio.md;
- roles_y_permisos.md;
- arquitectura.md;
- trazabilidad_y_auditoria.md;
- estilo_visual.md;
- objetivos.md;
- entidades.md;
- decisiones_tecnicas.md;
- dudas_pendientes.md;
- endpoints_api.md;
- estados_del_sistema.md.

Objetivo: que el proyecto tenga una fuente de verdad clara antes de tocar código.

---

## 5.2. Definir modelo de datos objetivo

Definir el E/R definitivo de la versión profesional.

Debe incluir:

- empresa;
- plan;
- suscripción;
- usuario;
- empresa_usuario;
- rol;
- empresa_usuario_rol;
- empleado;
- departamento;
- equipo;
- cliente;
- aviso;
- aviso_empleado;
- presupuesto;
- presupuesto_linea;
- parte_trabajo;
- parte_empleado;
- parte_hora;
- parte_material;
- parte_firma;
- parte_hash;
- auditoria_evento;
- exportacion_facturacion;
- material;
- movimiento_almacen.

Objetivo: evitar que la nueva base de datos crezca de forma improvisada.

---

## 5.3. Definir arquitectura objetivo

Definir una arquitectura clara:

```txt
Frontend Angular
      ↓
API REST PHP
      ↓
Servicios de negocio
      ↓
Repositorios / PDO
      ↓
MariaDB / MySQL
```

Debe haber separación entre:

- controladores;
- servicios;
- repositorios;
- middlewares;
- helpers;
- auditoría;
- hash;
- exportaciones.

Objetivo: evitar controladores demasiado grandes y lógica duplicada.

---

## 5.4. Definir roles y permisos

Dejar cerrados los roles principales:

- administrador_jefe;
- administrador;
- jefe_departamento;
- jefe_equipo;
- atencion_cliente;
- tecnico;
- solo_lectura.

Objetivo: que cada rol tenga responsabilidades claras y que el backend pueda validar permisos correctamente.

---

## 5.5. Definir reglas de seguridad

La primera fase debe dejar claras estas reglas:

- el backend valida autenticación;
- el backend valida roles;
- el backend valida empresa;
- el backend valida suscripción;
- el frontend no es fuente de verdad;
- no se usan IDs hardcodeados;
- no se exponen errores internos;
- las consultas usan PDO seguro;
- los secretos se sacan a variables de entorno.

Objetivo: que la profesionalización no se base solo en cambios visuales, sino en seguridad real.

---

## 6. Objetivos a medio plazo

Estos objetivos corresponden a la construcción de una primera versión profesional mínima.

## 6.1. Refactorizar backend

Reorganizar el backend para hacerlo más mantenible.

Objetivos:

- aplicar Front Controller correctamente;
- ordenar rutas en api.php;
- crear middlewares;
- separar controllers, services y repositories;
- unificar respuestas JSON;
- centralizar validaciones;
- centralizar errores;
- centralizar auditoría;
- eliminar código temporal o duplicado.

---

## 6.2. Refactorizar frontend

Reorganizar el frontend para mantener una estructura clara.

Objetivos:

- separar core, shared, layouts y features;
- usar servicios por módulo;
- usar interfaces claras;
- aplicar guards;
- aplicar interceptor de autenticación;
- reutilizar modales, botones, cards y tablas;
- mantener el estilo visual actual;
- mejorar responsive.

---

## 6.3. Implementar modelo SaaS

Implementar el modelo de planes y suscripciones.

Objetivos:

- crear planes por tramos;
- crear suscripción Free o prueba;
- limitar recursos según plan;
- comprobar límites en backend;
- mostrar uso del plan en frontend;
- bloquear acciones si se superan límites;
- auditar cambios de plan y suscripción.

Planes definidos:

- Free / prueba;
- Básico;
- Medio;
- Superior;
- 30+ / negociar.

---

## 6.4. Implementar usuarios multiempresa

Permitir que un usuario pueda estar vinculado a varias empresas.

Objetivos:

- usar empresa_usuario;
- asignar roles por empresa;
- permitir bloqueo por empresa;
- permitir que un administrador gestione varias empresas;
- evitar que un usuario vea datos de empresas no autorizadas.

---

## 6.5. Implementar presupuestos básicos

Añadir módulo de presupuestos.

Objetivos:

- crear presupuesto;
- añadir líneas;
- calcular subtotal, impuestos y total;
- vincular presupuesto a cliente;
- vincular presupuesto opcionalmente a aviso;
- aceptar o rechazar presupuesto;
- convertir presupuesto aceptado en trabajo;
- preparar exportación a facturación.

---

## 6.6. Profesionalizar partes de trabajo

Mejorar el módulo de partes/albaranes.

Objetivos:

- permitir varios empleados por parte;
- registrar horas por empleado;
- registrar materiales;
- registrar observaciones;
- capturar firma como imagen;
- guardar hash de firma;
- cerrar parte;
- generar hash de integridad;
- permitir rectificación durante 7 días;
- bloquear definitivamente al facturar.

---

## 6.7. Implementar auditoría

Añadir auditoría real.

Objetivos:

- registrar acciones críticas;
- guardar usuario;
- guardar empresa;
- guardar entidad afectada;
- guardar descripción;
- guardar valores anteriores y nuevos cuando proceda;
- registrar IP y user agent si es viable;
- restringir acceso a auditoría.

---

## 7. Objetivos a largo plazo

Estos objetivos no forman parte de la primera versión profesional mínima, pero deben tenerse en cuenta para no cerrar el diseño.

## 7.1. Integración con facturación

Objetivo futuro:

- exportar partes;
- exportar presupuestos;
- exportar clientes;
- exportar materiales;
- exportar suscripciones;
- conectar con programas externos de facturación o contabilidad.

No se define aún un programa concreto.

---

## 7.2. Almacén de materiales

Objetivo futuro:

- catálogo de materiales;
- stock;
- entradas;
- salidas;
- ajustes;
- devoluciones;
- importación;
- exportación;
- conexión con facturación o ERP.

En primera versión puede mantenerse material manual, pero el modelo debe estar preparado para catálogo.

---

## 7.3. Flota de vehículos

Objetivo futuro:

- registrar vehículos;
- asignar vehículo a empleado;
- asociar vehículo a parte;
- controlar mantenimiento;
- controlar kilometraje;
- controlar revisiones.

Este módulo puede añadirse después de estabilizar el núcleo.

---

## 7.4. Solicitud de materiales

Objetivo futuro:

- técnico solicita material;
- jefe de equipo o departamento revisa;
- almacén aprueba o rechaza;
- se genera movimiento de almacén;
- se asocia al aviso o parte.

---

## 7.5. Gestión de proyectos

Objetivo futuro:

- proyectos;
- tableros;
- columnas;
- tarjetas;
- asignaciones;
- seguimiento tipo Trello/Notion.

Decisión pendiente: definir si será módulo interno de EasyParte o proyecto paralelo conectado.

---

## 8. Alcance de la primera versión profesional mínima

La primera versión profesional no debe intentar hacerlo todo.

Debe centrarse en:

- autenticación real;
- roles backend;
- empresas;
- suscripciones;
- clientes;
- avisos;
- presupuestos básicos;
- partes;
- firma;
- hash;
- auditoría;
- dashboard;
- estilo visual consistente;
- base de datos preparada para crecer.

No debe intentar resolver completamente:

- facturación completa;
- ERP completo;
- almacén avanzado;
- flota;
- proyectos tipo Trello/Notion;
- automatizaciones avanzadas;
- app móvil nativa.

---

## 9. Funcionalidades que deben mantenerse del TFC

Se debe conservar como base:

- gestión de clientes;
- gestión de avisos/tareas;
- gestión de partes/albaranes;
- administración de empleados y usuarios;
- dashboard;
- uso de Angular;
- uso de PHP;
- uso de MariaDB/MySQL;
- estilo visual con verde como identidad principal;
- tarjetas, tablas, modales y formularios;
- Chart.js en dashboard.

---

## 10. Funcionalidades que deben replantearse

Deben revisarse o rehacerse:

- autenticación;
- autorización;
- roles solo visuales;
- control de empresa;
- IDs hardcodeados;
- CORS;
- secretos;
- errores internos expuestos;
- relación usuario/empresa;
- relación aviso/técnicos;
- relación parte/empleados;
- materiales en texto plano;
- firma sin integridad suficiente;
- partes sin trazabilidad fuerte.

---

## 11. Objetivos de calidad

El proyecto debe buscar:

- código claro;
- nombres entendibles;
- clases con responsabilidad definida;
- funciones reutilizables;
- menos duplicación;
- respuestas API homogéneas;
- control de errores limpio;
- documentación actualizada;
- arquitectura comprensible;
- seguridad razonable para producción;
- facilidad para añadir nuevos módulos.

---

## 12. Objetivos de seguridad

Objetivos mínimos:

1. Autenticación real.
2. Contraseñas hasheadas correctamente.
3. Backend protegido por middleware.
4. Roles validados en backend.
5. Empresa validada en backend.
6. Suscripción validada en backend.
7. Eliminación de endpoints temporales.
8. Eliminación de IDs hardcodeados.
9. Variables de entorno.
10. CORS configurado para producción.
11. Errores internos no expuestos.
12. Auditoría en acciones críticas.

---

## 13. Objetivos para Codex/agentes

Cuando se use Codex o cualquier agente, debe trabajar así:

1. Leer documentación antes de tocar código.
2. No modificar todo el proyecto a la vez.
3. Trabajar por tareas pequeñas.
4. No cambiar arquitectura sin justificarlo.
5. No inventar reglas de negocio.
6. No eliminar datos ni columnas sin aprobación.
7. Documentar cambios.
8. Indicar riesgos.
9. Crear pruebas o pasos de verificación.
10. Esperar revisión humana para cambios críticos.

---

## 14. Indicadores de que una fase está completada

Una fase se considera completada cuando:

- el código compila;
- el backend responde correctamente;
- no hay errores evidentes de consola;
- los endpoints principales funcionan;
- los roles se validan en backend;
- los datos se filtran por empresa;
- la documentación se actualiza;
- se registran cambios en auditoría cuando corresponde;
- se han probado los casos básicos;
- no se han roto funcionalidades existentes.

---

## 15. Prioridad de desarrollo

Orden recomendado:

1. Documentación base.
2. Modelo de datos objetivo.
3. Arquitectura objetivo.
4. Seguridad crítica.
5. Usuarios multiempresa y roles.
6. Modelo SaaS.
7. Clientes.
8. Avisos.
9. Presupuestos.
10. Partes.
11. Firma y hash.
12. Auditoría.
13. Exportación a facturación.
14. Materiales.
15. Dashboard avanzado.
16. Módulos futuros.

---

## 16. Dudas pendientes

Estas dudas deben resolverse antes de implementar ciertas partes:

1. ¿JWT en cliente o cookie HttpOnly?
2. ¿Qué duración tendrá el plan Free o prueba?
3. ¿Qué programa de facturación será el primero en integrarse?
4. ¿Los presupuestos tendrán impuestos configurables por empresa?
5. ¿El cliente podrá aceptar presupuesto online o solo internamente?
6. ¿El módulo de proyectos será interno o aplicación separada?
7. ¿El módulo de almacén entra en la primera versión o fase posterior?
8. ¿Qué política legal se seguirá para conservar auditoría y firmas?
9. ¿Se necesita firma con validez legal avanzada o solo firma simple de conformidad?
10. ¿Habrá pagos reales de suscripción dentro de la app o gestión manual inicial?

---

## 17. Conclusión

El objetivo de EasyParte no es solo “limpiar código”, sino transformar una aplicación funcional de TFC en una base sólida para un producto real.

La profesionalización debe hacerse con orden:

```txt
documentación → arquitectura → base de datos → seguridad → módulos → auditoría → despliegue
```

Cualquier decisión técnica debe respetar la lógica de negocio, el modelo SaaS, los roles, la trazabilidad y el estilo visual definido para EasyParte.
