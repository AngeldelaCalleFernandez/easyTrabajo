# Checklist frontend — EasyParte

## Antes de modificar

- [ ] He leído estilo visual.
- [ ] He leído arquitectura.
- [ ] He leído reglas de negocio.
- [ ] He leído roles y permisos.
- [ ] He leído endpoints API.
- [ ] La tarea está limitada a pocos archivos.
- [ ] No estoy intentando resolver seguridad solo en Angular.

## Configuración

- [ ] La URL de API no está duplicada innecesariamente.
- [ ] La configuración se puede separar por entorno.
- [ ] No hay secretos en frontend.
- [ ] No se exponen claves privadas.

## Autenticación y sesión

- [ ] Login usa AuthService.
- [ ] Logout limpia sesión.
- [ ] Interceptor añade token si procede.
- [ ] Respuestas 401 se gestionan de forma clara.
- [ ] AuthGuard protege rutas privadas.
- [ ] RoleGuard oculta rutas no permitidas.
- [ ] El frontend no sustituye validación backend.

## Servicios

- [ ] Cada módulo usa su servicio.
- [ ] No se repite lógica HTTP innecesaria.
- [ ] Las respuestas se tipan.
- [ ] Se gestionan errores.
- [ ] No hay uso excesivo de `any`.
- [ ] Los métodos tienen nombres claros.

## Formularios

- [ ] Usan Reactive Forms cuando procede.
- [ ] Campos obligatorios visibles.
- [ ] Validaciones visuales claras.
- [ ] Mensajes comprensibles.
- [ ] No se envían IDs críticos inventados.
- [ ] No se envía `id_empresa` como autoridad de negocio.

## UI y estilo

- [ ] Se mantiene verde oscuro/lima.
- [ ] Se usan tarjetas blancas.
- [ ] Botones consistentes.
- [ ] Modales limpios.
- [ ] Tablas claras.
- [ ] Badges de estado coherentes.
- [ ] No se recarga visualmente la interfaz.
- [ ] Lenguaje claro y no técnico.

## Responsive

- [ ] Navbar funciona en móvil.
- [ ] Tablas grandes tienen alternativa o scroll.
- [ ] Formularios son usables en móvil.
- [ ] Botones táctiles tienen tamaño suficiente.
- [ ] Firma será usable en táctil cuando se implemente.

## Dashboard

- [ ] Métricas dependen del rol.
- [ ] Técnico ve datos personales.
- [ ] Administrador ve datos de empresa.
- [ ] Atención al cliente no ve datos no permitidos.
- [ ] Chart.js se mantiene si hay gráficos.
- [ ] No se calculan permisos críticos solo en frontend.

## Errores

- [ ] Errores se muestran mediante AlertService o sistema equivalente.
- [ ] No se muestra SQLSTATE.
- [ ] No se muestra traza PHP.
- [ ] Mensajes son naturales.
- [ ] Estados de carga se muestran si la operación tarda.

## Antes de entregar

- [ ] Angular compila.
- [ ] No hay errores de consola relevantes.
- [ ] La ruta modificada funciona.
- [ ] Login sigue funcionando.
- [ ] Navegación sigue funcionando.
- [ ] Se han probado roles básicos.
- [ ] He documentado cambios y riesgos.
