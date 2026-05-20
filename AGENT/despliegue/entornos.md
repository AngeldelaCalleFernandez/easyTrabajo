# entornos.md

# EasyParte — Entornos

## 1. Propósito del documento

Este documento define los entornos recomendados para trabajar con EasyParte.

Separar entornos evita que pruebas, datos falsos, errores o cambios incompletos afecten a producción.

---

## 2. Entornos recomendados

EasyParte debería trabajar con al menos tres entornos:

```txt
local
staging
production
```

Opcionalmente puede añadirse:

```txt
testing
```

---

## 3. Entorno local

El entorno local es para desarrollo.

Uso:

- programar;
- probar cambios;
- depurar errores;
- trabajar con datos ficticios;
- ejecutar migraciones de prueba;
- probar con Angular y PHP local.

Ejemplo actual:

```txt
Frontend: http://localhost:4200
Backend: http://localhost/easyTrabajo/backend/public/api
Base de datos: MariaDB en XAMPP
```

Reglas:

- puede tener datos de prueba;
- puede mostrar errores técnicos;
- puede usar CORS abierto solo para localhost;
- no debe contener datos reales de clientes;
- no debe compartir secretos de producción.

---

## 4. Entorno staging

Staging es un entorno intermedio similar a producción.

Uso:

- probar cambios antes de producción;
- validar despliegues;
- revisar migraciones;
- comprobar permisos;
- probar con datos simulados realistas;
- probar integraciones.

Reglas:

- debe parecerse lo máximo posible a producción;
- no debe usar datos reales salvo anonimización;
- debe tener HTTPS si es posible;
- debe tener variables propias;
- debe tener base de datos separada;
- debe tener logs activos;
- puede tener usuarios de prueba.

Ejemplo:

```txt
https://staging.easyparte.com
https://api-staging.easyparte.com
```

---

## 5. Entorno production

Producción es el entorno real usado por empresas cliente.

Uso:

- clientes reales;
- usuarios reales;
- datos reales;
- suscripciones reales;
- partes y firmas reales.

Reglas:

- HTTPS obligatorio;
- CORS restringido;
- errores internos ocultos;
- backups automáticos;
- logs controlados;
- base de datos protegida;
- datos sensibles protegidos;
- cambios revisados;
- despliegues controlados.

Ejemplo:

```txt
https://app.easyparte.com
https://api.easyparte.com
```

---

## 6. Entorno testing

Opcional.

Uso:

- pruebas automáticas;
- pruebas de endpoints;
- pruebas de base de datos;
- integración continua.

Reglas:

- base de datos desechable;
- datos generados;
- no usar datos reales;
- puede reiniciarse sin problema.

---

## 7. Variables por entorno

Cada entorno debe tener su propia configuración.

Ejemplo:

```txt
.env.local
.env.staging
.env.production
.env.testing
```

Nunca se deben copiar secretos de producción al entorno local.

---

## 8. Base de datos por entorno

Cada entorno debe tener su propia base de datos.

Ejemplo:

```txt
easyparte_local
easyparte_staging
easyparte_production
easyparte_testing
```

Reglas:

- no probar migraciones directamente en producción;
- hacer backup antes de cambios;
- no usar datos reales en local;
- anonimizar datos si se copian a staging.

---

## 9. Usuarios por entorno

Los usuarios de prueba deben diferenciarse claramente de los reales.

Ejemplo local/staging:

```txt
admin.demo@easyparte.test
tecnico.demo@easyparte.test
cliente.demo@easyparte.test
```

No usar correos reales si no es necesario.

---

## 10. Configuración CORS por entorno

## Local

```txt
http://localhost:4200
http://127.0.0.1:4200
```

## Staging

```txt
https://staging.easyparte.com
```

## Production

```txt
https://app.easyparte.com
```

No usar `*` en producción.

---

## 11. Logs por entorno

## Local

- logs detallados;
- errores visibles;
- debug activado.

## Staging

- logs detallados;
- errores no visibles al usuario;
- trazabilidad activa.

## Production

- logs controlados;
- errores internos ocultos;
- alertas en fallos importantes;
- no guardar datos sensibles.

---

## 12. APP_ENV

Valores recomendados:

```txt
local
staging
production
testing
```

El código debe comportarse distinto según entorno.

Ejemplo:

- en local se puede mostrar detalle técnico;
- en producción no se deben mostrar trazas;
- en staging se registran más detalles, pero no se muestran al usuario.

---

## 13. Datos de prueba

Local y staging pueden usar datos de prueba.

Producción no debe contener datos demo mezclados con datos reales.

Si se instala EasyParte para una empresa real, debe existir un proceso claro para crear la primera empresa y el primer administrador_jefe.

---

## 14. Checklist por entorno

## Local

- [ ] Angular ejecuta.
- [ ] Backend responde.
- [ ] Base de datos local conectada.
- [ ] Datos demo.
- [ ] CORS local.
- [ ] Logs visibles.

## Staging

- [ ] HTTPS.
- [ ] Base de datos separada.
- [ ] Variables propias.
- [ ] Datos no reales o anonimizados.
- [ ] Pruebas de login.
- [ ] Pruebas de roles.
- [ ] Pruebas de suscripción.

## Production

- [ ] HTTPS.
- [ ] Variables seguras.
- [ ] Backups.
- [ ] Logs.
- [ ] CORS restringido.
- [ ] Errores ocultos.
- [ ] Base de datos protegida.
- [ ] Pruebas post-despliegue.

---

## 15. Conclusión

Separar entornos es obligatorio para profesionalizar EasyParte.

La regla básica es:

```txt
local para desarrollar
staging para validar
production para clientes reales
```
