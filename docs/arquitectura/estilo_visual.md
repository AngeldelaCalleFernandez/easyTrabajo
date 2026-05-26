# estilo_visual.md

# EasyParte — Estilo visual

## 1. Objetivo

Este documento define el estilo visual que debe mantenerse en la profesionalización de EasyParte.

La idea es conservar la identidad visual actual del proyecto, pero hacerla más consistente, limpia y preparada para una aplicación real.

EasyParte debe transmitir:

- claridad;
- confianza;
- organización;
- sencillez;
- sensación de herramienta profesional;
- enfoque en productividad.

---

## 2. Identidad visual general

El estilo actual debe mantenerse como base:

- uso de verdes oscuros y verdes lima;
- fondos claros;
- tarjetas blancas;
- bordes suaves;
- diseño limpio;
- dashboard con métricas;
- formularios claros;
- modales para crear y editar;
- tablas de gestión;
- gráficos con Chart.js.

No se busca una interfaz recargada. Debe ser una aplicación de gestión sencilla y clara.

---

## 3. Paleta de colores recomendada

Colores principales:

```txt
Verde oscuro principal: #14532D
Verde medio: #166534
Verde lima/acento: #84CC16
Verde claro suave: #DCFCE7
Fondo general: #F8FAFC
Blanco tarjetas: #FFFFFF
Texto principal: #111827
Texto secundario: #6B7280
Borde suave: #E5E7EB
Error: #DC2626
Aviso: #F59E0B
Información: #2563EB
Éxito: #16A34A
```

Uso recomendado:

- Verde oscuro para navbar, títulos importantes o botones principales.
- Verde lima para acentos, indicadores y llamadas de atención.
- Blanco para tarjetas y modales.
- Gris claro para fondos.
- Rojo solo para errores o acciones destructivas.
- Amarillo/naranja para advertencias.
- Azul para información secundaria.

---

## 4. Tipografía

Usar una tipografía sans-serif clara.

Recomendación:

```txt
Inter
system-ui
Arial
sans-serif
```

Jerarquía recomendada:

```txt
Título de página: text-2xl / font-semibold
Subtítulo: text-lg / font-medium
Texto normal: text-sm o text-base
Texto auxiliar: text-xs / text-gray-500
Botones: text-sm / font-medium
```

La interfaz debe priorizar lectura rápida.

---

## 5. Layout general

La aplicación debe tener:

- login limpio;
- layout principal con navbar;
- dashboard inicial;
- páginas por módulo;
- tablas o cards para listados;
- modales para crear/editar;
- alertas centralizadas.

Estructura típica:

```txt
DashboardLayout
  Navbar
  MainContent
    Header de página
    Acciones principales
    Filtros/búsqueda
    Listado o dashboard
```

---

## 6. Navbar

El navbar debe:

- mostrar logo/nombre EasyParte;
- mostrar rutas según rol;
- permitir cerrar sesión;
- ser responsive;
- ocultar opciones no permitidas;
- mantener diseño sencillo.

Rutas recomendadas:

```txt
Dashboard
Avisos
Presupuestos
Partes
Clientes
Materiales
Administración
Suscripción
Auditoría
Perfil
```

No todos los roles verán todas las rutas.

---

## 7. Dashboard

El dashboard debe mostrar información útil y no solo decoración.

Métricas recomendadas:

- avisos pendientes;
- avisos en proceso;
- partes abiertos;
- partes cerrados;
- horas registradas;
- presupuestos enviados;
- presupuestos aceptados;
- clientes activos;
- límite del plan usado;
- alertas de suscripción si aplica.

Gráficos posibles con Chart.js:

- avisos por estado;
- partes por mes;
- horas por técnico;
- presupuestos aceptados/rechazados;
- uso del plan.

Para técnicos, el dashboard debe ser limitado a sus datos.

Para administradores, puede mostrar visión global de empresa.

---

## 8. Tarjetas

Las cards deben usarse para:

- métricas;
- resúmenes;
- bloques de información;
- acciones rápidas.

Estilo recomendado con Tailwind:

```txt
bg-white
rounded-2xl
shadow-sm
border
border-gray-100
p-4
```

No abusar de sombras fuertes.

---

## 9. Botones

Tipos de botón:

## Primario

Uso:

- guardar;
- crear;
- aceptar;
- enviar.

Estilo:

```txt
bg-green-700
text-white
hover:bg-green-800
rounded-xl
```

## Secundario

Uso:

- cancelar;
- volver;
- acciones menos importantes.

Estilo:

```txt
bg-white
border
text-gray-700
hover:bg-gray-50
```

## Destructivo

Uso:

- eliminar;
- bloquear;
- cancelar definitivamente.

Estilo:

```txt
bg-red-600
text-white
hover:bg-red-700
```

## Advertencia

Uso:

- rectificar;
- liberar;
- revisar.

Estilo:

```txt
bg-amber-500
text-white
hover:bg-amber-600
```

---

## 10. Formularios

Los formularios deben ser claros y no demasiado largos.

Recomendaciones:

- labels visibles;
- mensajes de error debajo del campo;
- placeholders moderados;
- campos agrupados por secciones;
- botones al final;
- validación visual en frontend;
- validación real en backend.

Ejemplos de secciones:

```txt
Datos generales
Datos de contacto
Asignación
Fechas
Observaciones
```

---

## 11. Modales

Los modales se deben usar para:

- crear cliente;
- editar cliente;
- crear aviso;
- asignar técnico;
- crear parte;
- registrar firma;
- confirmar cierre;
- confirmar acción destructiva.

Reglas:

1. No usar modales enormes si el proceso es largo.
2. Para procesos complejos, usar página propia o wizard.
3. Confirmar acciones sensibles.
4. Mostrar claramente errores de backend.

---

## 12. Estados visuales

Los estados deben tener colores consistentes.

## Avisos

```txt
Pendiente: gris/amarillo
Asignado: azul
En proceso: naranja
Finalizado: verde
Cancelado: rojo/gris
```

## Partes

```txt
Abierto: azul
En curso: naranja
Pausado: amarillo
Cerrado: verde
Anulado: rojo/gris
Facturado: verde oscuro o morado
```

## Presupuestos

```txt
Borrador: gris
Enviado: azul
Aceptado: verde
Rechazado: rojo
Caducado: amarillo/gris
Convertido: verde oscuro
Cancelado: gris/rojo
```

## Suscripción

```txt
Prueba: azul
Activa: verde
Pendiente de pago: amarillo
Cancelada: gris
Caducada: naranja
Bloqueada: rojo
```

---

## 13. Tablas y listados

Las tablas deben ser limpias.

Recomendaciones:

- cabecera clara;
- acciones alineadas a la derecha;
- badges de estado;
- buscador;
- filtros por estado;
- paginación;
- ordenación si procede;
- mensaje cuando no hay datos.

En móvil, una tabla grande puede convertirse en cards.

---

## 14. Alertas

Debe existir un sistema centralizado tipo `AlertService`.

Tipos:

- success;
- error;
- warning;
- info;
- confirmación.

Las alertas deben ser claras y no técnicas.

Ejemplo correcto:

```txt
No tienes permisos para cerrar este parte.
```

Ejemplo incorrecto:

```txt
SQLSTATE[23000]: Integrity constraint violation...
```

---

## 15. Responsive

La aplicación debe funcionar en escritorio y móvil.

Prioridad:

1. Escritorio para administración y gestión.
2. Tablet para técnicos o jefes.
3. Móvil para técnicos en campo.

En móvil:

- navbar responsive;
- botones grandes;
- formularios fáciles;
- firma táctil;
- partes sencillos;
- evitar tablas enormes.

---

## 16. Firma del cliente

La pantalla de firma debe ser simple:

- nombre del cliente o firmante;
- zona de firma;
- botón limpiar;
- botón aceptar;
- texto de confirmación;
- aviso de que la firma quedará asociada al parte.

Después de firmar, debe mostrar estado claro:

```txt
Firma registrada correctamente.
```

---

## 17. Pantallas clave

Pantallas mínimas:

- Login.
- Dashboard.
- Clientes.
- Avisos.
- Presupuestos.
- Partes.
- Administración.
- Suscripción.
- Perfil.

Pantallas futuras:

- Materiales.
- Almacén.
- Flota.
- Proyectos.
- Auditoría avanzada.
- Exportaciones.

---

## 18. Estilo de redacción en interfaz

El lenguaje debe ser claro y natural.

Usar:

```txt
Crear aviso
Asignar técnico
Cerrar parte
Registrar firma
Presupuesto aceptado
Suscripción activa
```

Evitar lenguaje demasiado técnico:

```txt
Insertar registro
Actualizar entidad
Ejecutar mutación
Payload inválido
```

---

## 19. Reglas para mantener coherencia

1. No mezclar estilos visuales distintos.
2. No usar demasiados colores.
3. No inventar componentes visuales para cada pantalla.
4. Reutilizar botones, badges, cards, modales y tablas.
5. Mantener verde como color principal.
6. Usar estados visuales coherentes.
7. Evitar interfaces saturadas.
8. Priorizar claridad sobre decoración.
9. Mantener el dashboard útil.
10. Diseñar pensando en técnicos y administradores.

---

## 20. Referencia visual del producto

EasyParte debe parecer una herramienta profesional de gestión interna, no una landing comercial.

Referencia de sensación:

```txt
Panel SaaS sencillo
Aplicación de gestión empresarial
Dashboard operativo
Herramienta para técnicos y administradores
```

No debe parecer:

```txt
red social
plantilla genérica sin identidad
aplicación experimental
interfaz sobrecargada
```

