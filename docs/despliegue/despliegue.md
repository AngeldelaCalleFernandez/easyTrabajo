# despliegue.md

# EasyParte — Despliegue

## 1. Propósito del documento

Este documento define cómo debe plantearse el despliegue de EasyParte cuando pase de proyecto local/TFC a una aplicación preparada para entornos reales.

No es una guía definitiva de proveedor, sino una base para organizar el despliegue sin improvisar.

EasyParte parte de una arquitectura:

```txt
Frontend Angular
Backend PHP
Base de datos MariaDB/MySQL
API REST
```

---

## 2. Objetivo del despliegue

El objetivo es que EasyParte pueda ejecutarse fuera del entorno local de desarrollo, con:

- HTTPS;
- dominio o subdominio;
- variables de entorno;
- base de datos protegida;
- configuración CORS segura;
- logs;
- backups;
- separación entre desarrollo, pruebas y producción;
- control de errores;
- despliegue reproducible.

---

## 3. Arquitectura de despliegue recomendada

Para una primera versión profesional, la arquitectura puede ser:

```txt
Usuario
  ↓
Frontend Angular compilado
  ↓
Servidor web HTTPS
  ↓
API PHP
  ↓
MariaDB/MySQL
```

Ejemplo de separación lógica:

```txt
frontend.easyparte.com      → Angular compilado
api.easyparte.com           → Backend PHP
db privada                  → MariaDB/MySQL
```

También puede usarse un único dominio con rutas:

```txt
easyparte.com               → Angular
easyparte.com/api           → Backend PHP
```

La opción con subdominios suele ser más limpia para producción.

---

## 4. Opciones de despliegue

## 4.1. Despliegue sencillo

Adecuado para una primera versión con pocos clientes.

```txt
Servidor VPS
├── Nginx o Apache
├── PHP
├── MariaDB/MySQL
├── Frontend Angular compilado
└── Backend PHP
```

Ventajas:

- más económico;
- fácil de entender;
- todo está en un servidor;
- bueno para primera fase.

Inconvenientes:

- si el servidor cae, cae todo;
- requiere configurar seguridad y backups;
- menos escalable.

---

## 4.2. Despliegue separado

Adecuado si el proyecto empieza a crecer.

```txt
Servidor frontend
Servidor API
Base de datos gestionada
Almacenamiento de archivos
```

Ventajas:

- más escalable;
- mejor separación;
- más fácil proteger base de datos;
- backups más cómodos.

Inconvenientes:

- más coste;
- más configuración;
- más complejidad.

---

## 4.3. Despliegue recomendado inicial

Para la primera versión profesional, se recomienda empezar con una solución sencilla pero bien configurada:

```txt
VPS o hosting compatible
Nginx/Apache
PHP 8.x
MariaDB/MySQL
HTTPS
Backups automáticos
Variables de entorno
```

No conviene complicar con Kubernetes, microservicios o arquitectura distribuida en la primera fase.

---

## 5. Compilación del frontend

El frontend Angular debe desplegarse compilado.

Comando orientativo:

```bash
npm install
npm run build
```

El resultado se genera normalmente en:

```txt
dist/
```

Ese contenido se sube al servidor web.

Reglas:

- no subir `node_modules`;
- no subir archivos de entorno con secretos;
- no dejar sourcemaps públicos si no son necesarios;
- configurar correctamente la URL de API.

---

## 6. Despliegue del backend

El backend PHP debe estar en una carpeta separada del frontend.

Estructura recomendada:

```txt
backend/
├── public/
│   ├── index.php
│   └── .htaccess
├── routes/
├── controllers/
├── services/
├── repositories/
├── middleware/
├── helpers/
├── config/
└── storage/
```

Solo `backend/public` debería ser accesible públicamente.

No deben ser públicos:

- controllers;
- services;
- repositories;
- config;
- .env;
- logs;
- backups;
- scripts internos.

---

## 7. Base de datos

La base de datos debe estar protegida.

Reglas:

1. No permitir acceso público directo a MariaDB/MySQL.
2. Crear usuario de base de datos específico para EasyParte.
3. No usar root en producción.
4. Usar contraseña fuerte.
5. Restringir permisos del usuario de base de datos.
6. Hacer backups automáticos.
7. Probar restauraciones.
8. Mantener migraciones o scripts versionados.

---

## 8. Archivos subidos y firmas

EasyParte puede guardar firmas de cliente e imágenes en el futuro.

Recomendación:

```txt
storage/
├── firmas/
├── adjuntos/
├── exportaciones/
└── logs/
```

Reglas:

- no guardar archivos sensibles en carpetas públicas sin control;
- validar tipo y tamaño de archivo;
- generar nombres seguros;
- evitar sobrescritura;
- proteger acceso por permisos;
- guardar hash de firma;
- hacer backup de archivos importantes.

---

## 9. HTTPS

Producción debe usar HTTPS.

Reglas:

- redirigir HTTP a HTTPS;
- usar certificados válidos;
- marcar cookies como Secure si se usan cookies;
- evitar contenido mixto;
- configurar correctamente dominios y subdominios.

---

## 10. CORS

En desarrollo se puede permitir:

```txt
http://localhost:4200
```

En producción solo se deben permitir dominios reales:

```txt
https://easyparte.com
https://app.easyparte.com
```

No usar en producción:

```txt
Access-Control-Allow-Origin: *
```

especialmente si se usan credenciales o cookies.

---

## 11. Variables de entorno

No deben subirse secretos al repositorio.

Deben gestionarse por entorno:

```txt
.env.development
.env.staging
.env.production
```

Variables importantes:

- APP_ENV;
- APP_URL;
- FRONTEND_URL;
- API_URL;
- DB_HOST;
- DB_PORT;
- DB_NAME;
- DB_USER;
- DB_PASSWORD;
- JWT_SECRET;
- CORS_ALLOWED_ORIGINS;
- LOG_LEVEL;
- STORAGE_PATH;
- BACKUP_PATH.

---

## 12. Logs

Deben existir logs de backend.

Tipos:

- errores;
- accesos críticos;
- auditoría funcional;
- exportaciones;
- fallos de integración.

No se deben guardar contraseñas ni tokens completos en logs.

---

## 13. Despliegue manual inicial

Flujo recomendado:

1. Hacer backup.
2. Poner aplicación en modo mantenimiento si procede.
3. Actualizar código.
4. Instalar dependencias si aplica.
5. Compilar frontend.
6. Subir frontend compilado.
7. Subir backend.
8. Actualizar base de datos con scripts controlados.
9. Limpiar caché si existe.
10. Probar login.
11. Probar endpoint `/auth/me`.
12. Probar clientes, avisos, presupuestos y partes.
13. Revisar logs.
14. Quitar modo mantenimiento.

---

## 14. Despliegue con Git

Cuando el proyecto esté más maduro, se recomienda usar ramas.

Propuesta:

```txt
main        → producción estable
develop     → integración
feature/*   → nuevas funciones
fix/*       → correcciones
refactor/*  → refactorizaciones
```

Regla:

No desplegar directamente desde ramas sin revisar.

---

## 15. Checklist de despliegue

Antes de desplegar:

- [ ] `.env` configurado.
- [ ] `APP_ENV=production`.
- [ ] HTTPS activo.
- [ ] CORS limitado.
- [ ] Base de datos no pública.
- [ ] Usuario DB no es root.
- [ ] Backups configurados.
- [ ] Logs activos.
- [ ] Errores internos ocultos.
- [ ] JWT_SECRET fuerte.
- [ ] Permisos de carpetas revisados.
- [ ] Frontend apunta a API correcta.
- [ ] Endpoints protegidos.
- [ ] Prueba de login correcta.
- [ ] Prueba de roles correcta.
- [ ] Prueba de empresa/tenant correcta.
- [ ] Prueba de suscripción correcta.
- [ ] Prueba de backup/restauración planificada.

---

## 16. Qué no hacer

No hacer en producción:

- subir `.env` al repositorio;
- usar usuario root de base de datos;
- dejar CORS con `*`;
- mostrar errores PHP al usuario;
- dejar `/setup` o endpoints temporales;
- subir `node_modules`;
- dejar carpetas internas públicas;
- guardar backups dentro de una carpeta pública;
- desplegar sin backup previo;
- modificar base de datos manualmente sin registro.

---

## 17. Conclusión

El despliegue inicial de EasyParte debe ser sencillo, pero no improvisado.

Prioridad:

```txt
seguridad → backups → variables → HTTPS → CORS → logs → pruebas
```

La aplicación debe poder crecer, pero la primera versión debe priorizar estabilidad, protección de datos y facilidad de mantenimiento.
