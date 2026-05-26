# testing.md

# EasyParte — Estrategia de testing

## 1. Propósito del documento

Este documento define cómo debe probarse EasyParte durante la profesionalización del proyecto.

El objetivo no es crear una batería perfecta desde el primer día, sino asegurar que los cambios importantes no rompan la aplicación y que las reglas críticas de negocio, seguridad y permisos se validen antes de pasar a producción.

EasyParte no debe considerarse listo para despliegue solo porque “funcione visualmente”. Debe probarse en backend, frontend, base de datos, roles, suscripciones, trazabilidad y flujos completos.

---

## 2. Objetivos del testing

Los objetivos principales son:

1. Comprobar que la aplicación funciona como se espera.
2. Detectar errores antes de producción.
3. Evitar regresiones al refactorizar.
4. Verificar que los roles se cumplen en backend.
5. Confirmar que cada empresa solo ve sus datos.
6. Probar límites de planes y suscripciones.
7. Comprobar que los técnicos solo ven sus avisos y partes.
8. Validar presupuestos, partes, firmas y hashes.
9. Confirmar que la auditoría registra acciones críticas.
10. Preparar el proyecto para despliegue con más seguridad.

---

## 3. Tipos de pruebas recomendadas

## 3.1. Pruebas manuales

Son las primeras que se deben hacer.

Sirven para comprobar:

- login;
- navegación;
- formularios;
- listados;
- creación de datos;
- edición;
- permisos;
- cierre de partes;
- firma;
- dashboard.

Estas pruebas pueden documentarse en `casos_prueba.md`.

---

## 3.2. Pruebas de API

Se realizan contra endpoints del backend.

Herramientas posibles:

- Postman;
- Insomnia;
- Thunder Client;
- curl;
- scripts simples.

Deben comprobar:

- códigos de estado;
- respuestas JSON;
- validaciones;
- errores;
- permisos;
- filtros por empresa;
- datos obligatorios;
- transiciones de estado.

---

## 3.3. Pruebas de frontend

Sirven para comprobar que Angular muestra correctamente la información.

Deben probar:

- rutas protegidas;
- guards;
- modales;
- formularios reactivos;
- mensajes de error;
- tablas;
- filtros;
- dashboard;
- responsive;
- estados visuales.

---

## 3.4. Pruebas de seguridad

Son obligatorias antes de producción.

Deben probar:

- acceso sin token;
- token inválido;
- token caducado;
- usuario bloqueado;
- empresa inactiva;
- suscripción bloqueada;
- rol insuficiente;
- intento de acceder a datos de otra empresa;
- técnico intentando ver partes de otro técnico;
- CORS;
- errores internos.

---

## 3.5. Pruebas de base de datos

Deben comprobar:

- claves foráneas;
- borrado lógico;
- restricciones;
- relaciones N:N;
- transacciones;
- integridad de partes;
- registros de auditoría;
- campos `created_at`, `updated_at`, `deleted_at`.

---

## 3.6. Pruebas de regresión

Después de cada refactor importante, se deben repetir casos clave.

Casos mínimos de regresión:

- login;
- dashboard;
- clientes;
- avisos;
- presupuestos;
- partes;
- firma;
- cierre;
- permisos por rol;
- filtro por empresa.

---

## 4. Entornos de prueba

## Local

Para desarrollo rápido.

Uso:

- pruebas funcionales básicas;
- pruebas de frontend;
- pruebas de backend;
- datos de prueba.

## Staging

Para validar antes de producción.

Uso:

- pruebas de despliegue;
- pruebas con configuración similar a producción;
- pruebas de roles;
- pruebas de suscripción;
- pruebas de backup/restauración;
- pruebas de CORS y HTTPS.

## Producción

No se usa para experimentar.

Solo se hacen pruebas controladas después del despliegue.

---

## 5. Datos de prueba recomendados

Crear datos que cubran casos reales.

## Empresas

- Empresa A.
- Empresa B.
- Empresa Free.
- Empresa con suscripción bloqueada.

## Usuarios

- administrador_jefe;
- administrador;
- jefe_departamento;
- jefe_equipo;
- atencion_cliente;
- tecnico_1;
- tecnico_2;
- solo_lectura;
- usuario bloqueado.

## Clientes

- cliente activo;
- cliente inactivo;
- cliente con avisos;
- cliente con presupuestos;
- cliente con partes.

## Avisos

- aviso pendiente;
- aviso asignado;
- aviso en proceso;
- aviso finalizado;
- aviso cancelado;
- aviso con varios técnicos.

## Presupuestos

- borrador;
- enviado;
- aceptado;
- rechazado;
- caducado;
- convertido.

## Partes

- abierto;
- en curso;
- cerrado;
- cerrado dentro de 7 días;
- cerrado fuera de 7 días;
- facturado;
- con varios empleados;
- con firma;
- con materiales.

---

## 6. Convención de resultado

Cada prueba debe marcarse como:

```txt
Pendiente
Correcta
Fallida
Bloqueada
No aplica
```

Y debe incluir:

- fecha;
- entorno;
- usuario probado;
- resultado esperado;
- resultado obtenido;
- observaciones;
- incidencia asociada si falla.

---

## 7. Prioridad de pruebas

## Prioridad crítica

Estas pruebas bloquean producción:

- autenticación;
- autorización backend;
- separación por empresa;
- roles;
- suscripción;
- técnicos no ven datos de otros;
- partes facturados bloqueados;
- backups;
- errores internos ocultos.

## Prioridad alta

- clientes;
- avisos;
- presupuestos;
- partes;
- firma;
- hash;
- auditoría.

## Prioridad media

- dashboard;
- filtros;
- responsive;
- exportaciones;
- materiales.

## Prioridad baja

- mejoras visuales menores;
- textos;
- ordenaciones secundarias;
- detalles no críticos.

---

## 8. Pruebas por rol

## Administrador jefe

Debe poder:

- ver toda la empresa;
- gestionar usuarios;
- bloquear usuarios;
- ver suscripción;
- ver auditoría;
- autorizar rectificaciones.

No debe poder:

- ver empresas donde no está vinculado;
- modificar datos internos de plataforma si no existe rol específico.

## Administrador

Debe poder:

- gestionar clientes;
- gestionar avisos;
- gestionar partes;
- gestionar presupuestos;
- ver dashboard de empresa.

No debe poder:

- bloquear al administrador_jefe;
- ver otras empresas;
- saltarse suscripción.

## Técnico

Debe poder:

- ver sus avisos;
- ver sus partes;
- registrar horas;
- registrar materiales;
- firmar parte;
- rectificar dentro del plazo.

No debe poder:

- ver partes de otros técnicos;
- ver dashboard global;
- gestionar usuarios;
- cambiar roles;
- modificar partes facturados.

---

## 9. Pruebas de auditoría

Toda acción crítica debe generar registro.

Acciones a comprobar:

- crear usuario;
- bloquear usuario;
- asignar rol;
- crear aviso;
- asignar técnico;
- crear presupuesto;
- aceptar presupuesto;
- crear parte;
- cerrar parte;
- firmar parte;
- rectificar parte;
- marcar parte como facturado;
- cambiar suscripción.

---

## 10. Pruebas de hash e integridad

Comprobar:

1. Al firmar, se guarda firma y hash.
2. Al cerrar parte, se genera hash de integridad.
3. Si se rectifica, se genera nueva versión o nuevo hash.
4. Si se factura, el parte queda bloqueado.
5. No se puede modificar directamente un parte facturado.
6. El payload firmado es estable.

---

## 11. Pruebas de límites de plan

Comprobar plan Free:

- máximo 2 usuarios;
- máximo 15 clientes;
- máximo 5 avisos;
- máximo 5 partes.

Casos:

- crear dentro del límite;
- intentar crear fuera del límite;
- cambiar de plan;
- bloquear por suscripción;
- reactivar suscripción.

---

## 12. Automatización futura

En fases futuras se pueden automatizar pruebas con:

- PHPUnit para backend PHP;
- pruebas de integración API;
- pruebas e2e con Playwright o Cypress;
- scripts SQL de validación;
- pruebas en CI/CD.

Primera prioridad:

- documentar casos;
- probar manualmente;
- preparar API para automatizar después.

---

## 13. Qué no debe hacerse

No dar por bueno el proyecto solo porque:

- el frontend oculta botones;
- una ruta no aparece en el menú;
- el login redirige bien;
- funciona con usuario administrador;
- funciona en local;
- no hay errores visibles.

Debe probarse backend y permisos reales.

---

## 14. Conclusión

El testing de EasyParte debe centrarse en seguridad, roles, empresa, suscripción, avisos, presupuestos, partes y trazabilidad.

La regla principal es:

```txt
Lo que no se prueba, no se puede considerar listo para producción.
```
