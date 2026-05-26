# seguridad_produccion.md

# EasyParte — Seguridad en producción

## 1. Propósito

Este documento define las medidas mínimas de seguridad que EasyParte debe cumplir antes de desplegarse en producción.

EasyParte gestionará datos de empresas, clientes, usuarios, avisos, presupuestos, partes, firmas y trazabilidad. Por tanto, no debe tratarse como una demo cuando esté desplegado.

---

## 2. Principios básicos

1. El backend es la fuente de verdad.
2. El frontend no decide permisos reales.
3. Toda acción sensible debe validar usuario, empresa, suscripción y rol.
4. No se deben exponer errores internos.
5. No se deben usar IDs hardcodeados.
6. No se deben subir secretos al repositorio.
7. No se debe usar root para base de datos.
8. Las firmas y partes cerrados deben protegerse.
9. Los backups deben estar protegidos.
10. Producción debe usar HTTPS.

---

## 3. Autenticación

La autenticación debe ser real.

Requisitos:

- validar credenciales en backend;
- usar hash seguro de contraseña;
- no guardar contraseña en texto plano;
- no devolver `password_hash` al frontend;
- limitar información en errores de login;
- registrar intentos sospechosos si procede.

Errores recomendados:

```txt
Credenciales incorrectas.
```

Evitar:

```txt
El email existe pero la contraseña es incorrecta.
```

---

## 4. Contraseñas

Requisitos:

- usar `password_hash` o equivalente seguro;
- preferible Argon2id si el entorno lo soporta;
- mínimo bcrypt si no se usa Argon2id;
- nunca MD5;
- nunca SHA1 simple;
- nunca texto plano.

---

## 5. Sesiones o JWT

Pendiente de decisión final.

Si se usa JWT:

- secreto fuerte;
- expiración;
- no guardar información sensible;
- validar firma en backend;
- invalidación o refresh controlado;
- no aceptar tokens manipulados.

Si se usa cookie:

- HttpOnly;
- Secure;
- SameSite;
- dominio correcto;
- protección CSRF si aplica.

---

## 6. Autorización

La autorización debe validarse siempre en backend.

Validar en cada endpoint protegido:

1. usuario autenticado;
2. usuario activo;
3. empresa activa;
4. vinculación empresa_usuario activa;
5. usuario no bloqueado en esa empresa;
6. suscripción activa o prueba;
7. rol permitido;
8. ámbito permitido: empresa, departamento, equipo o técnico.

---

## 7. Multitenant

Regla crítica:

> Una empresa nunca debe poder acceder a datos de otra empresa.

Medidas:

- filtrar por `id_empresa`;
- obtener empresa desde contexto validado;
- no confiar en `id_empresa` enviado por frontend;
- comprobar relación usuario-empresa;
- comprobar ámbito del rol.

Casos críticos:

- técnico no ve partes de otro técnico;
- jefe de equipo no ve equipos ajenos;
- jefe de departamento no ve otros departamentos;
- administrador solo ve su empresa;
- usuario bloqueado en empresa no accede a esa empresa.

---

## 8. CORS

En producción, CORS debe ser restrictivo.

Permitir solo:

```txt
https://app.easyparte.com
```

o dominios reales definidos.

No usar:

```txt
Access-Control-Allow-Origin: *
```

si hay autenticación o credenciales.

---

## 9. Errores

No mostrar al usuario:

- trazas PHP;
- errores SQL;
- rutas internas;
- nombres de archivos del servidor;
- detalles de configuración;
- secretos.

Respuesta recomendada:

```json
{
  "success": false,
  "error": {
    "code": "INTERNAL_ERROR",
    "message": "Se ha producido un error inesperado"
  }
}
```

El detalle técnico debe ir a logs.

---

## 10. Validación de entrada

Todo dato recibido debe validarse en backend.

Validar:

- tipos;
- campos obligatorios;
- tamaños máximos;
- formatos;
- IDs;
- permisos sobre IDs;
- estados permitidos;
- transiciones de estado.

Ejemplo:

No basta con comprobar que `id_cliente` existe. También hay que comprobar que pertenece a la empresa activa.

---

## 11. SQL

Requisitos:

- usar PDO;
- consultas preparadas;
- no concatenar datos del usuario en SQL;
- controlar transacciones;
- validar ordenaciones y filtros;
- no exponer errores SQL.

---

## 12. Archivos y firmas

Riesgos:

- subida de archivos maliciosos;
- acceso público a firmas;
- sobrescritura;
- rutas manipuladas;
- archivos demasiado grandes.

Medidas:

- validar extensión;
- validar MIME;
- limitar tamaño;
- generar nombre seguro;
- guardar fuera de carpeta pública si es posible;
- servir archivos mediante endpoint protegido;
- calcular hash;
- registrar auditoría.

---

## 13. Partes cerrados y facturados

Reglas:

- al cerrar parte, generar hash de integridad;
- si se modifica, debe quedar auditoría;
- técnico solo rectifica dentro de 7 días;
- fuera de plazo requiere autorización;
- facturado queda bloqueado definitivamente;
- no editar parte facturado directamente.

---

## 14. Auditoría

Deben auditarse acciones críticas:

- login sospechoso;
- cambio de roles;
- bloqueo de usuario;
- cambio de suscripción;
- creación/cambio de aviso;
- asignación de técnico;
- aceptación de presupuesto;
- cierre de parte;
- firma de parte;
- rectificación;
- exportación a facturación.

---

## 15. Variables y secretos

No deben estar en código:

- DB_PASSWORD;
- JWT_SECRET;
- claves API;
- credenciales SMTP;
- claves de facturación;
- credenciales de backup.

Usar `.env`.

El archivo `.env` real debe estar fuera del repositorio.

---

## 16. Servidor

Recomendaciones:

- PHP actualizado;
- servidor actualizado;
- permisos de archivos revisados;
- directorios internos no públicos;
- HTTPS;
- firewall;
- base de datos no pública;
- backups protegidos;
- logs revisables.

---

## 17. Cabeceras de seguridad

Configurar cuando sea posible:

```txt
Strict-Transport-Security
X-Content-Type-Options
X-Frame-Options
Referrer-Policy
Content-Security-Policy
```

La política CSP debe probarse bien para no romper Angular.

---

## 18. Checklist mínimo de seguridad

Antes de producción:

- [ ] HTTPS activo.
- [ ] APP_DEBUG=false.
- [ ] Errores internos ocultos.
- [ ] JWT_SECRET seguro.
- [ ] Contraseñas hasheadas.
- [ ] CORS restringido.
- [ ] Base de datos no pública.
- [ ] Usuario DB no root.
- [ ] Endpoints protegidos.
- [ ] Roles validados en backend.
- [ ] Empresa validada en backend.
- [ ] Suscripción validada en backend.
- [ ] IDs hardcodeados eliminados.
- [ ] `/setup` eliminado.
- [ ] Logs activos.
- [ ] Backups activos.
- [ ] Firma y hash protegidos.
- [ ] Auditoría activa en acciones críticas.

---

## 19. Cosas prohibidas en producción

- `APP_DEBUG=true`.
- CORS con `*`.
- Contraseñas en texto plano.
- Errores SQL en respuesta.
- Root como usuario de base de datos.
- `.env` público.
- Backups dentro de carpeta pública.
- Endpoints temporales.
- IDs fijos como usuario actual.
- Roles solo en frontend.
- Datos de demo mezclados con datos reales.

---

## 20. Conclusión

EasyParte no debe desplegarse como si fuera una práctica local.

La seguridad mínima debe estar antes de clientes reales:

```txt
autenticación → autorización → multitenant → suscripción → auditoría → backups
```
