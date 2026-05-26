# Checklist auditoría — EasyParte

## Preparación

- [ ] He leído documentación base.
- [ ] He leído reglas de negocio.
- [ ] He leído roles y permisos.
- [ ] He leído modelo SaaS.
- [ ] He leído arquitectura.
- [ ] He leído seguridad producción.
- [ ] He leído trazabilidad y auditoría.
- [ ] No voy a modificar código salvo petición explícita.

## Seguridad

- [ ] No hay contraseñas en claro.
- [ ] No hay secretos en código.
- [ ] JWT_SECRET no queda fijo para producción.
- [ ] Los endpoints privados tienen middleware.
- [ ] CORS no queda abierto para producción.
- [ ] No se devuelven errores internos.
- [ ] Login tiene respuesta genérica ante fallo.
- [ ] Token/sesión caduca.
- [ ] No hay endpoints temporales peligrosos.

## Permisos

- [ ] Backend valida roles.
- [ ] Backend valida empresa.
- [ ] Backend valida recurso concreto.
- [ ] Técnicos no ven partes de otros.
- [ ] Técnicos no suman horas de otros.
- [ ] Atención al cliente no gestiona usuarios si no procede.
- [ ] Admin no ve empresas ajenas.
- [ ] Rutas frontend no sustituyen permisos backend.

## Multiempresa

- [ ] Consultas filtran por empresa.
- [ ] No se confía en `id_empresa` del body.
- [ ] IDs relacionados pertenecen a la empresa activa.
- [ ] No hay mezcla de datos entre empresas.
- [ ] Dashboard está separado por rol y empresa.

## Backend

- [ ] Controladores no contienen lógica excesiva.
- [ ] SQL complejo no se repite.
- [ ] Consultas usan PDO preparado.
- [ ] Respuestas JSON son homogéneas.
- [ ] Errores se registran internamente.
- [ ] No hay dependencias innecesarias.

## Frontend

- [ ] API URL no está duplicada por todas partes.
- [ ] Interceptor funciona.
- [ ] Guards existen.
- [ ] Errores HTTP se tratan.
- [ ] No hay `any` excesivo en zonas críticas.
- [ ] UI mantiene estilo definido.
- [ ] Responsive no está roto.

## Base de datos

- [ ] Tablas críticas tienen relaciones.
- [ ] Hay borrado lógico donde procede.
- [ ] Estados son coherentes.
- [ ] No hay datos demo inseguros para producción.
- [ ] No hay usuarios demo con contraseñas conocidas en entorno real.
- [ ] Las FK no impiden operaciones normales sin plan de baja lógica.

## Auditoría e integridad

- [ ] Acciones críticas generan auditoría o están planificadas.
- [ ] Parte cerrado tiene trazabilidad.
- [ ] Firma tiene hash o está planificado.
- [ ] Parte facturado queda bloqueado.
- [ ] Rectificaciones quedan registradas.
- [ ] No se sobrescriben firmas sin evento.

## Despliegue

- [ ] Variables de entorno definidas.
- [ ] Logs configurados.
- [ ] Backups definidos.
- [ ] HTTPS previsto.
- [ ] Build frontend documentado.
- [ ] Configuración producción separada.
- [ ] Checklist preproducción existe.

## Informe final

- [ ] Riesgos críticos separados.
- [ ] Riesgos medios separados.
- [ ] Mejoras recomendadas separadas.
- [ ] Dudas pendientes documentadas.
- [ ] No se ha inventado funcionalidad.
