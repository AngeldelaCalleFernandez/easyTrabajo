# backup_y_restauracion.md

# EasyParte — Backup y restauración

## 1. Propósito

Este documento define cómo debe plantearse el backup y la restauración de EasyParte.

EasyParte puede contener datos importantes:

- empresas;
- clientes;
- usuarios;
- avisos;
- presupuestos;
- partes;
- firmas;
- materiales;
- auditoría;
- exportaciones.

La pérdida de datos puede afectar directamente al negocio de una empresa cliente.

---

## 2. Objetivo

El sistema de backup debe permitir recuperar la aplicación ante:

- error humano;
- borrado accidental;
- fallo de servidor;
- fallo de base de datos;
- actualización fallida;
- ataque;
- corrupción de datos;
- pérdida de archivos.

---

## 3. Qué debe respaldarse

## 3.1. Base de datos

Debe incluir:

- empresas;
- usuarios;
- roles;
- clientes;
- avisos;
- presupuestos;
- partes;
- materiales;
- auditoría;
- suscripciones;
- exportaciones.

## 3.2. Archivos

Debe incluir:

- firmas;
- adjuntos futuros;
- exportaciones generadas;
- documentos PDF si se generan;
- archivos de evidencia.

## 3.3. Configuración

Debe guardarse de forma segura:

- `.env` de producción;
- configuración del servidor;
- configuración de tareas programadas;
- configuración de dominios;
- configuración de certificados si procede.

No guardar secretos en lugares inseguros.

---

## 4. Qué no debe respaldarse como fuente principal

No es necesario respaldar como fuente principal:

- `node_modules`;
- cachés;
- builds temporales;
- logs antiguos sin valor;
- archivos temporales;
- dependencias que se puedan reinstalar.

---

## 5. Frecuencia recomendada

Para una primera versión profesional:

```txt
Base de datos: diario
Archivos: diario
Configuración: cuando cambie
Backup antes de despliegue: obligatorio
```

Si hay clientes reales y mucho uso:

```txt
Base de datos: cada 6 o 12 horas
Archivos: diario
Retención: 30 días mínimo
```

---

## 6. Retención

Propuesta inicial:

```txt
Backups diarios: 30 días
Backups semanales: 8 semanas
Backups mensuales: 6-12 meses
```

Esto puede ajustarse según coste y obligaciones legales.

---

## 7. Ubicación de backups

No guardar backups solo en el mismo servidor.

Recomendación:

- copia local temporal;
- copia externa;
- copia en almacenamiento seguro;
- cifrado si contiene datos sensibles.

No guardar backups en carpeta pública del servidor web.

---

## 8. Nombre de backups

Formato recomendado:

```txt
easyparte_db_YYYY-MM-DD_HH-mm.sql.gz
easyparte_files_YYYY-MM-DD_HH-mm.tar.gz
easyparte_config_YYYY-MM-DD_HH-mm.tar.gz
```

Ejemplo:

```txt
easyparte_db_2026-05-20_03-00.sql.gz
```

---

## 9. Backup antes de despliegue

Antes de desplegar cambios importantes:

1. Crear backup de base de datos.
2. Crear backup de archivos.
3. Confirmar que el backup existe.
4. Guardar versión del código desplegado.
5. Ejecutar despliegue.
6. Probar aplicación.

Nunca ejecutar migraciones destructivas sin backup.

---

## 10. Restauración

La restauración debe estar documentada y probada.

Pasos generales:

1. Activar modo mantenimiento si procede.
2. Detener procesos que escriban datos.
3. Restaurar base de datos.
4. Restaurar archivos.
5. Restaurar configuración si procede.
6. Revisar permisos.
7. Probar login.
8. Probar datos críticos.
9. Revisar logs.
10. Quitar modo mantenimiento.

---

## 11. Pruebas de restauración

Un backup que no se ha probado puede no servir.

Frecuencia recomendada:

```txt
Prueba de restauración: mensual o antes de cambios grandes
```

Entorno recomendado:

```txt
staging
```

No probar restauraciones sobre producción salvo emergencia.

---

## 12. Datos sensibles

Los backups pueden contener datos sensibles.

Medidas:

- cifrar backups si es posible;
- limitar acceso;
- no compartir por correo;
- no subir a repositorios;
- no dejarlos en carpetas públicas;
- definir quién puede descargarlos;
- registrar restauraciones.

---

## 13. Auditoría y backups

La auditoría también debe respaldarse.

No se debe borrar auditoría sin política clara.

Si se restaura una copia antigua, se puede perder auditoría reciente. Esto debe tenerse en cuenta en incidentes reales.

---

## 14. Backups de firmas y hashes

Las firmas y hashes son importantes.

Debe respaldarse:

- imagen o archivo de firma;
- registro `parte_firma`;
- registro `parte_hash`;
- payload firmado si se guarda;
- auditoría asociada.

Si se pierde la imagen de firma pero queda el registro, la evidencia queda incompleta.

---

## 15. Escenarios de recuperación

## 15.1. Error en despliegue

Solución:

- volver a versión anterior del código;
- restaurar backup si hubo cambios en base de datos;
- revisar logs.

## 15.2. Borrado accidental de cliente

Solución:

- si hay borrado lógico, reactivar;
- si se borró físicamente, restaurar desde backup en entorno separado y recuperar datos.

## 15.3. Corrupción de base de datos

Solución:

- detener aplicación;
- restaurar backup válido;
- revisar causa;
- comprobar integridad.

## 15.4. Pérdida de archivos

Solución:

- restaurar carpeta storage;
- comprobar firmas;
- comprobar adjuntos;
- revisar hashes.

---

## 16. Checklist de backup

- [ ] Backup automático de base de datos.
- [ ] Backup automático de archivos.
- [ ] Backup antes de despliegue.
- [ ] Retención definida.
- [ ] Copia externa.
- [ ] Backups no públicos.
- [ ] Acceso restringido.
- [ ] Restauración documentada.
- [ ] Restauración probada.
- [ ] Responsable definido.

---

## 17. Responsable

Pendiente definir.

Debe quedar claro:

- quién revisa backups;
- quién puede restaurar;
- quién recibe alertas;
- quién documenta incidentes.

---

## 18. Conclusión

El backup no es opcional.

EasyParte debe asumir que puede gestionar información importante de empresas reales.

Regla básica:

```txt
Si no se ha probado restaurar, el backup no está validado.
```
