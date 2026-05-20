# Checklist backend — EasyParte

## Antes de modificar

- [ ] He leído reglas de negocio.
- [ ] He leído roles y permisos.
- [ ] He leído modelo SaaS.
- [ ] He leído arquitectura.
- [ ] He leído endpoints API.
- [ ] He identificado los archivos exactos que tocaré.
- [ ] La tarea es pequeña y revisable.
- [ ] No hay una duda pendiente que bloquee la tarea.

## Autenticación

- [ ] Login valida email y contraseña.
- [ ] Contraseña se comprueba con `password_verify`.
- [ ] La contraseña nunca se devuelve.
- [ ] El token/sesión caduca.
- [ ] Los endpoints privados exigen autenticación.
- [ ] El usuario inactivo no puede operar.
- [ ] El usuario bloqueado en empresa no puede operar.

## Autorización

- [ ] El backend comprueba rol.
- [ ] El backend comprueba empresa.
- [ ] El backend comprueba recurso concreto.
- [ ] El técnico solo ve lo asignado.
- [ ] Administración y atención al cliente solo ven su empresa.
- [ ] No se confía en permisos del frontend.

## Multiempresa

- [ ] Toda consulta sensible filtra por `id_empresa`.
- [ ] `id_empresa` se obtiene del token/sesión validada.
- [ ] No se usa `id_empresa` recibido del body como fuente de verdad.
- [ ] Los IDs relacionados pertenecen a la misma empresa.
- [ ] No hay consultas por ID sin empresa en recursos de negocio.

## SaaS y suscripción

- [ ] La empresa tiene suscripción o estado equivalente.
- [ ] El backend comprueba límites antes de crear usuarios/clientes/avisos/partes.
- [ ] Las acciones bloqueadas devuelven error claro.
- [ ] Los cambios de plan se auditan.
- [ ] No se inventan límites no documentados.

## PDO y base de datos

- [ ] Todas las consultas usan prepared statements.
- [ ] No se concatena entrada del usuario en SQL.
- [ ] Las transacciones se usan si hay operaciones dependientes.
- [ ] No se eliminan datos críticos físicamente sin aprobación.
- [ ] Se respeta borrado lógico cuando proceda.

## Errores y respuestas

- [ ] Respuesta correcta usa formato homogéneo.
- [ ] Error usa formato homogéneo.
- [ ] No se devuelven trazas.
- [ ] No se devuelven SQLSTATE al cliente.
- [ ] El detalle interno se registra con `error_log` o sistema equivalente.
- [ ] Los códigos HTTP son coherentes.

## Auditoría

- [ ] Acciones críticas generan auditoría.
- [ ] Se registra usuario.
- [ ] Se registra empresa.
- [ ] Se registra entidad y entidad_id.
- [ ] Se registra acción.
- [ ] Se registran valores anteriores/nuevos cuando proceda.
- [ ] No se registran contraseñas ni secretos.

## Partes, firma e integridad

- [ ] Parte cerrado no se modifica directamente sin trazabilidad.
- [ ] Parte facturado queda bloqueado.
- [ ] Firma no se sobrescribe sin auditoría.
- [ ] Hash de firma se guarda cuando aplique.
- [ ] Hash de parte se genera al cerrar cuando aplique.
- [ ] Rectificación respeta plazo de 7 días.

## Antes de entregar

- [ ] El backend arranca.
- [ ] Los endpoints afectados responden.
- [ ] Login sigue funcionando.
- [ ] Los roles siguen funcionando.
- [ ] No hay errores PHP visibles.
- [ ] He documentado cambios.
- [ ] He indicado riesgos.
- [ ] He indicado pruebas realizadas.
