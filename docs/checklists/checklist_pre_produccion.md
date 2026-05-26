# checklist_pre_produccion.md

# EasyParte — Checklist preproducción

## 1. Propósito

Este documento define las comprobaciones mínimas antes de considerar EasyParte listo para desplegar en producción o presentar una versión profesional estable.

No sustituye a las pruebas, pero sirve como lista rápida de control.

---

## 2. Estado general

- [ ] El código compila.
- [ ] El frontend arranca sin errores críticos.
- [ ] El backend responde correctamente.
- [ ] La base de datos conecta.
- [ ] No hay errores visibles en consola del navegador.
- [ ] No hay errores PHP visibles al usuario.
- [ ] Los README están actualizados.
- [ ] La documentación base está en Drive/proyecto.
- [ ] El roadmap está actualizado.

---

## 3. Seguridad

- [ ] Autenticación real implementada.
- [ ] Contraseñas con hash seguro.
- [ ] No hay contraseñas en texto plano.
- [ ] JWT_SECRET o secreto de sesión fuerte.
- [ ] APP_DEBUG=false en producción.
- [ ] Errores internos ocultos.
- [ ] Logs activos.
- [ ] CORS restringido.
- [ ] HTTPS activo.
- [ ] No existe endpoint `/setup`.
- [ ] No hay endpoints temporales públicos.
- [ ] No se devuelven trazas SQL.
- [ ] No se exponen rutas internas.
- [ ] No se sube `.env` al repositorio.
- [ ] No hay secretos hardcodeados.

---

## 4. Autorización y roles

- [ ] El backend valida roles.
- [ ] El frontend no es la única barrera.
- [ ] `authGuard` funciona.
- [ ] `roleGuard` funciona.
- [ ] `tenantGuard` o equivalente funciona.
- [ ] `subscriptionGuard` o equivalente funciona.
- [ ] Administrador jefe tiene permisos correctos.
- [ ] Administrador tiene permisos correctos.
- [ ] Jefe de departamento limitado a su ámbito.
- [ ] Jefe de equipo limitado a su equipo.
- [ ] Técnico solo ve sus avisos y partes.
- [ ] Solo lectura no modifica datos.
- [ ] Usuario bloqueado no accede.
- [ ] Roles se asignan por empresa.

---

## 5. Multiempresa

- [ ] Cada entidad importante filtra por empresa.
- [ ] No se confía en `id_empresa` enviado por frontend.
- [ ] Usuario puede pertenecer a varias empresas.
- [ ] Se puede resolver empresa activa.
- [ ] Empresa A no ve datos de Empresa B.
- [ ] Técnico de Empresa A no ve partes de Empresa B.
- [ ] Auditoría registra empresa afectada.
- [ ] Suscripción se valida por empresa.

---

## 6. Modelo SaaS

- [ ] Existe tabla o modelo de plan.
- [ ] Existe tabla o modelo de suscripción.
- [ ] Plan Free/prueba definido.
- [ ] Límite de 2 usuarios en Free.
- [ ] Límite de 15 clientes en Free.
- [ ] Límite de 5 avisos en Free.
- [ ] Límite de 5 partes en Free.
- [ ] Backend bloquea creación al superar límites.
- [ ] Suscripción bloqueada limita acceso.
- [ ] Cambio de plan genera auditoría.

---

## 7. Clientes

- [ ] Crear cliente funciona.
- [ ] Editar cliente funciona.
- [ ] Baja lógica funciona.
- [ ] Reactivar cliente funciona si procede.
- [ ] Cliente pertenece a empresa correcta.
- [ ] Cliente no se borra físicamente si tiene actividad.
- [ ] Cambios generan auditoría.

---

## 8. Avisos

- [ ] Crear aviso funciona.
- [ ] Editar aviso funciona.
- [ ] Cambiar estado funciona.
- [ ] Asignar varios técnicos funciona.
- [ ] Quitar técnico funciona.
- [ ] Técnico asignado ve el aviso.
- [ ] Técnico no asignado no ve el aviso.
- [ ] Aviso pertenece a empresa correcta.
- [ ] Cambios generan auditoría.

---

## 9. Presupuestos

- [ ] Crear presupuesto funciona.
- [ ] Añadir líneas funciona.
- [ ] Editar líneas funciona.
- [ ] Calcular subtotal funciona.
- [ ] Calcular impuestos funciona si aplica.
- [ ] Calcular total funciona.
- [ ] Enviar presupuesto funciona.
- [ ] Aceptar presupuesto funciona.
- [ ] Rechazar presupuesto funciona.
- [ ] Convertir presupuesto funciona si aplica.
- [ ] Cambios generan auditoría.

---

## 10. Partes de trabajo

- [ ] Crear parte desde aviso funciona.
- [ ] Crear parte desde presupuesto aceptado funciona si aplica.
- [ ] Añadir varios empleados funciona.
- [ ] Registrar horas funciona.
- [ ] Registrar materiales funciona.
- [ ] Guardar observaciones funciona.
- [ ] Cerrar parte funciona.
- [ ] Parte pertenece a empresa correcta.
- [ ] Técnico solo ve partes propios.
- [ ] Administrador ve partes de empresa.
- [ ] Cambios generan auditoría.

---

## 11. Firma e integridad

- [ ] Firma del cliente se captura.
- [ ] Firma se guarda como imagen o archivo.
- [ ] Se calcula hash de firma.
- [ ] Se registra fecha de firma.
- [ ] Al cerrar parte se genera hash de integridad.
- [ ] Si se rectifica, se registra versión o nuevo hash.
- [ ] Parte facturado queda bloqueado.
- [ ] No se sobrescribe firma sin auditoría.

---

## 12. Rectificaciones

- [ ] Técnico puede rectificar dentro de 7 días.
- [ ] Técnico no puede rectificar fuera de 7 días sin autorización.
- [ ] Administrador o jefe autorizado puede liberar rectificación.
- [ ] Rectificación registra motivo.
- [ ] Rectificación registra solicitante.
- [ ] Rectificación registra autorizador si procede.
- [ ] Parte facturado no se rectifica directamente.
- [ ] Rectificación genera auditoría.

---

## 13. Auditoría

- [ ] Crear aviso genera auditoría.
- [ ] Asignar técnico genera auditoría.
- [ ] Crear presupuesto genera auditoría.
- [ ] Aceptar presupuesto genera auditoría.
- [ ] Cerrar parte genera auditoría.
- [ ] Firmar parte genera auditoría.
- [ ] Cambiar rol genera auditoría.
- [ ] Bloquear usuario genera auditoría.
- [ ] Cambiar suscripción genera auditoría.
- [ ] Valores anteriores/nuevos se guardan cuando procede.
- [ ] Solo roles autorizados ven auditoría.

---

## 14. Exportación a facturación

- [ ] Existe modelo de exportación.
- [ ] Se puede registrar exportación de parte.
- [ ] Se puede registrar exportación de presupuesto.
- [ ] Se guarda sistema destino.
- [ ] Se guarda payload.
- [ ] Se guarda respuesta externa si existe.
- [ ] Se guarda estado.
- [ ] Errores de exportación no borran datos.

---

## 15. Frontend

- [ ] Login correcto.
- [ ] Logout correcto.
- [ ] Navbar cambia según rol.
- [ ] Rutas protegidas.
- [ ] Formularios validan campos.
- [ ] Errores backend se muestran de forma clara.
- [ ] Modales funcionan.
- [ ] Tablas muestran estados.
- [ ] Dashboard carga.
- [ ] Diseño mantiene estilo visual.
- [ ] Responsive básico probado.

---

## 16. Backend

- [ ] Rutas organizadas.
- [ ] Middlewares aplicados.
- [ ] Controllers no contienen demasiada lógica.
- [ ] Services aplican reglas de negocio.
- [ ] Repositories encapsulan SQL.
- [ ] PDO con consultas preparadas.
- [ ] Respuestas JSON homogéneas.
- [ ] Transacciones donde proceda.
- [ ] Validaciones backend.
- [ ] Logs de errores.

---

## 17. Base de datos

- [ ] Claves primarias.
- [ ] Claves foráneas.
- [ ] Índices básicos.
- [ ] `created_at`.
- [ ] `updated_at`.
- [ ] `deleted_at` en entidades principales.
- [ ] Borrado lógico.
- [ ] Relaciones N:N correctas.
- [ ] Datos demo separados de datos reales.
- [ ] Script de creación o migración controlado.

---

## 18. Despliegue

- [ ] Entorno definido.
- [ ] Variables de entorno configuradas.
- [ ] HTTPS.
- [ ] CORS producción.
- [ ] Base de datos no pública.
- [ ] Usuario DB no root.
- [ ] Permisos de carpetas revisados.
- [ ] Frontend compilado.
- [ ] Backend solo expone `public`.
- [ ] Logs protegidos.
- [ ] Storage protegido.

---

## 19. Backups

- [ ] Backup de base de datos configurado.
- [ ] Backup de archivos configurado.
- [ ] Backup antes de despliegue.
- [ ] Backup almacenado fuera de carpeta pública.
- [ ] Retención definida.
- [ ] Restauración probada.
- [ ] Responsable definido.

---

## 20. Documentación

- [ ] negocio.md actualizado.
- [ ] modelo_saas.md actualizado.
- [ ] reglas_de_negocio.md actualizado.
- [ ] roles_y_permisos.md actualizado.
- [ ] arquitectura.md actualizado.
- [ ] trazabilidad_y_auditoria.md actualizado.
- [ ] estilo_visual.md actualizado.
- [ ] endpoints_api.md actualizado.
- [ ] testing.md actualizado.
- [ ] casos_prueba.md actualizado.
- [ ] dudas_pendientes.md actualizado.

---

## 21. Decisión final

Antes de pasar a producción debe responderse:

```txt
¿Hay algún fallo crítico abierto?
¿Hay algún endpoint sensible sin proteger?
¿Hay datos de una empresa visibles desde otra?
¿Hay backups verificados?
¿Hay errores internos visibles?
¿Los técnicos pueden ver datos ajenos?
¿Los partes facturados están bloqueados?
```

Si alguna respuesta es sí en sentido negativo, no se debe desplegar.
