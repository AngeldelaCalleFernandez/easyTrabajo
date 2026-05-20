# dudas_pendientes.md

# EasyParte — Dudas pendientes

## 1. Propósito del documento

Este documento recoge decisiones que todavía no están cerradas.

Regla principal:

> Si algo aparece en este documento, ningún agente debe inventarlo ni implementarlo como definitivo sin revisión humana.

Las dudas deben resolverse antes de convertirlas en tareas de desarrollo.

---

## 2. Seguridad y autenticación

## Duda 1 — JWT o cookie HttpOnly

Pendiente decidir si producción usará:

- JWT guardado en cliente;
- cookie HttpOnly + Secure + SameSite;
- sistema híbrido.

Pregunta pendiente:

> ¿Quieres priorizar simplicidad inicial con JWT o seguridad más profesional con cookie HttpOnly?

---

## 3. Plan Free / prueba

## Duda 2 — Duración del plan Free

Ya se han definido límites:

- 2 usuarios;
- 15 clientes;
- 5 avisos;
- 5 partes.

Pendiente:

- ¿será gratuito permanente con límites?
- ¿será prueba de 14/30 días?
- ¿se bloqueará al caducar?
- ¿se permitirá exportar a facturación?

---

## 4. Precios de planes

## Duda 3 — Precio de planes

Planes definidos:

- Básico: 0-5 usuarios.
- Medio: 6-15 usuarios.
- Superior: 16-30 usuarios.
- 30+ negociar.

Pendiente:

- precio mensual;
- precio anual;
- descuentos;
- si se cobra por empresa o por usuarios activos;
- si los usuarios inactivos cuentan para el límite.

---

## 5. Facturación

## Duda 4 — Programa de facturación destino

Pendiente decidir primer sistema de facturación:

- FacturaScripts;
- Holded;
- Sage;
- Quipu;
- Excel/CSV;
- API genérica;
- otro.

Pregunta pendiente:

> ¿Quieres empezar con exportación CSV/Excel para simplificar o ir directamente a una API concreta?

---

## 6. Pagos de suscripción

## Duda 5 — Pago real desde la app

Pendiente decidir si EasyParte gestionará:

- solo estado manual de suscripción;
- pagos reales con pasarela;
- facturación externa;
- integración con programa contable.

---

## 7. Presupuestos

## Duda 6 — Aceptación de presupuesto

Pendiente decidir si el cliente podrá aceptar el presupuesto:

- desde la propia aplicación;
- por enlace público;
- manualmente por un usuario interno;
- mediante firma;
- por correo externo.

## Duda 7 — Impuestos

Pendiente decidir si los impuestos serán:

- fijos;
- configurables por empresa;
- configurables por línea;
- adaptados a diferentes países.

---

## 8. Partes y firma

## Duda 8 — Validez de la firma

Pendiente decidir si la firma será:

- simple conformidad del cliente;
- firma con más datos de evidencia;
- firma legal avanzada;
- firma con certificado digital.

Primera decisión provisional:

- firma como imagen + hash + fecha + firmante + IP/user agent si es posible.

---

## 9. Rectificación de partes

## Duda 9 — Proceso para parte facturado erróneo

Ya está definido:

- un parte facturado queda bloqueado definitivamente.

Pendiente decidir:

- si se crea parte rectificativo;
- si se genera documento correctivo;
- si se permite anulación contable;
- cómo se exporta la corrección a facturación.

---

## 10. Almacén

## Duda 10 — Alcance del almacén inicial

Pendiente decidir si en primera versión habrá:

- solo material manual;
- catálogo de materiales básico;
- stock;
- movimientos;
- importación/exportación;
- múltiples almacenes.

Recomendación provisional:

- primera versión con material manual + modelo preparado para catálogo.

---

## 11. Flota

## Duda 11 — Módulo de vehículos

Pendiente decidir si se desarrollará como:

- módulo interno futuro;
- integración externa;
- no prioritario.

---

## 12. Proyectos tipo Trello/Notion

## Duda 12 — Módulo de proyectos

Pendiente decidir si será:

- módulo interno de EasyParte;
- proyecto paralelo conectado;
- descartado para evitar dispersión.

Recomendación provisional:

- no incluir en primera versión profesional.

---

## 13. Auditoría

## Duda 13 — Retención de auditoría

Pendiente decidir:

- cuánto tiempo conservar auditoría;
- quién puede verla;
- si se puede exportar;
- si se puede anonimizar;
- política legal de borrado.

---

## 14. Despliegue

## Duda 14 — Entorno de producción

Pendiente decidir:

- hosting;
- dominio;
- proveedor;
- base de datos gestionada o servidor propio;
- backups;
- logs;
- HTTPS;
- despliegue manual o CI/CD.

---

## 15. Git y ramas

## Duda 15 — Estrategia de ramas

Pendiente decidir:

- rama principal;
- rama de desarrollo;
- ramas por tarea;
- pull requests;
- revisión humana obligatoria.

Recomendación:

```txt
main
develop
feature/*
fix/*
refactor/*
```

---

## 16. Administración interna de EasyParte

## Duda 16 — Gestión global de plataforma

Se ha decidido no crear inicialmente `superadmin_easyparte`.

Pendiente:

- cómo se gestionarán las empresas clientes desde el lado propietario de EasyParte;
- quién podrá crear empresas;
- quién podrá bloquear suscripciones;
- quién podrá ver métricas globales.

---

## 17. Preguntas directas para resolver más adelante

1. ¿El plan Free será permanente o prueba temporal?
2. ¿Qué precio quieres para Básico, Medio y Superior?
3. ¿Cuál será el primer sistema de facturación a integrar?
4. ¿Quieres que el cliente acepte presupuestos por enlace público?
5. ¿Quieres cookie HttpOnly desde el principio o JWT para primera fase?
6. ¿El almacén entra en versión 1 o fase 2?
7. ¿Quieres crear un rol interno futuro para gestionar EasyParte como plataforma?
8. ¿El módulo de proyectos será otro producto o módulo dentro de EasyParte?
9. ¿Qué tipo de hosting tienes pensado?
10. ¿Quieres que Codex trabaje con ramas y PR siempre?

