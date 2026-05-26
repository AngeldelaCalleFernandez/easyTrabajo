# convenciones_commits.md

# EasyParte — Convenciones de commits

## 1. Propósito

Este documento define cómo escribir commits en EasyParte.

El objetivo es que el historial de Git sea claro, fácil de revisar y útil para entender la evolución del proyecto.

---

## 2. Formato recomendado

Formato general:

```txt
tipo: descripción breve
```

Ejemplos:

```txt
feat: añade presupuestos básicos
fix: corrige visibilidad de partes por técnico
refactor: separa lógica de avisos en servicio
docs: actualiza reglas de negocio
security: valida empresa en endpoints de partes
```

---

## 3. Tipos de commit

## feat

Nueva funcionalidad.

Ejemplos:

```txt
feat: añade módulo de presupuestos
feat: permite asignar varios técnicos a un aviso
feat: añade firma de cliente en partes
```

## fix

Corrección de error.

Ejemplos:

```txt
fix: corrige cálculo de horas del dashboard
fix: evita que técnicos vean partes ajenos
fix: corrige respuesta JSON en login
```

## refactor

Cambio interno sin cambiar comportamiento esperado.

Ejemplos:

```txt
refactor: mueve lógica de partes a ParteService
refactor: reorganiza servicios Angular
refactor: separa repositorios PDO
```

## docs

Cambios de documentación.

Ejemplos:

```txt
docs: añade documentación de despliegue
docs: actualiza endpoints API
docs: documenta roles y permisos
```

## security

Cambios de seguridad.

Ejemplos:

```txt
security: restringe CORS en producción
security: valida roles en backend
security: mueve secretos a variables de entorno
```

## test

Cambios relacionados con pruebas.

Ejemplos:

```txt
test: añade casos de prueba de roles
test: documenta pruebas de partes
test: añade checklist preproducción
```

## style

Cambios visuales o de formato que no afectan lógica.

Ejemplos:

```txt
style: mejora estilos del dashboard
style: ajusta tarjetas de avisos
style: unifica badges de estado
```

## chore

Tareas de mantenimiento.

Ejemplos:

```txt
chore: actualiza gitignore
chore: limpia archivos temporales
chore: reorganiza carpetas del proyecto
```

## db

Cambios de base de datos.

Ejemplos:

```txt
db: añade tablas de presupuestos
db: crea relación empresa_usuario
db: añade campos de hash en partes
```

## ci

Cambios de integración o despliegue.

Ejemplos:

```txt
ci: añade workflow de pruebas
ci: prepara script de build frontend
ci: configura despliegue staging
```

---

## 4. Reglas de redacción

Un commit debe:

- ser breve;
- explicar qué cambia;
- usar presente;
- evitar textos genéricos;
- no mezclar muchas cosas;
- no usar frases como “cambios”, “arreglo cosas” o “final”.

Buenos ejemplos:

```txt
fix: evita acceso a partes de otros técnicos
feat: añade líneas de presupuesto
docs: crea estrategia de ramas
security: oculta errores internos del backend
```

Malos ejemplos:

```txt
cambios
arreglos
final final
subida
prueba
todo ok
```

---

## 5. Commits con cuerpo

Si el cambio es importante, añadir cuerpo.

Ejemplo:

```txt
security: valida empresa en endpoints de partes

Se añade comprobación de empresa activa antes de consultar partes.
Los técnicos solo pueden acceder a partes donde participan.
También se actualiza la respuesta de error para accesos no autorizados.
```

---

## 6. Commits de base de datos

Si el commit afecta a base de datos, debe indicar claramente el impacto.

Ejemplo:

```txt
db: añade tablas de presupuestos y líneas

Crea presupuesto, presupuesto_linea e historial de estados.
No elimina tablas existentes.
Requiere ejecutar script SQL antes de probar el módulo.
```

Si es destructivo:

```txt
db!: renombra tarea a aviso
```

El símbolo `!` indica cambio importante o incompatible.

Regla:

No hacer commits destructivos de base de datos sin revisión humana.

---

## 7. Commits de seguridad

Los commits de seguridad deben ser específicos.

Ejemplos:

```txt
security: mueve JWT_SECRET a variables de entorno
security: bloquea acceso de técnicos a partes ajenos
security: añade validación de suscripción activa
```

Evitar:

```txt
security: mejora seguridad
```

---

## 8. Commits de documentación

Los commits de documentación deben indicar qué documento cambia.

Ejemplos:

```txt
docs: añade modelo SaaS
docs: actualiza endpoints API
docs: crea checklist preproducción
docs: documenta estrategia de ramas
```

---

## 9. Commits generados por Codex

Cuando Codex haga cambios, el commit debe ser revisable.

Recomendación:

```txt
tipo: descripción del cambio realizado por codex
```

Ejemplo:

```txt
refactor: reorganiza controladores de avisos
```

En el cuerpo del commit puede añadirse:

```txt
Cambio generado con ayuda de Codex y revisado manualmente.
```

No hacer commits automáticos sin revisar.

---

## 10. Commits pequeños

Evitar commits enormes.

Correcto:

```txt
feat: añade tabla presupuesto
feat: añade endpoints de presupuesto
feat: crea servicio Angular de presupuestos
```

Incorrecto:

```txt
feat: rehace toda la aplicación
```

---

## 11. Relación entre rama y commits

La rama debe agrupar commits relacionados.

Ejemplo:

Rama:

```txt
feature/presupuestos-basicos
```

Commits:

```txt
db: añade tablas de presupuestos
feat: crea endpoints de presupuestos
feat: añade servicio Angular de presupuestos
style: añade vista inicial de presupuestos
docs: actualiza endpoints API
```

---

## 12. Commits antes de Pull Request

Antes de abrir Pull Request:

- revisar mensajes;
- evitar commits basura;
- confirmar que compila;
- confirmar que no hay secretos;
- confirmar documentación actualizada.

---

## 13. Resumen rápido

Tipos recomendados:

```txt
feat
fix
refactor
docs
security
test
style
chore
db
ci
```

Formato:

```txt
tipo: descripción clara
```

Regla:

```txt
Un commit debe explicar una intención concreta.
```

---

## 14. Conclusión

Los commits deben ayudar a entender el proyecto.

Un buen historial facilita:

- revisar cambios;
- encontrar errores;
- trabajar con Codex;
- preparar releases;
- volver atrás si algo falla.
