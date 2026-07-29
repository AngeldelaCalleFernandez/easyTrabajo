# Arnés de integración de avisos multiempresa

## Propósito

Este arnés PHP CLI automatiza la matriz
`AVISOS-REASIGNACION-MULTIEMPRESA` ya validada manualmente. Comprueba:

- aislamiento de listados de avisos y empleados asignables;
- permisos de asignación, reasignación y toma de avisos;
- bloqueo de asignación, reasignación y toma en estados `Finalizada` y
  `Cancelada`;
- ausencia de cambios y eventos ante peticiones rechazadas;
- empresa, usuario, aviso y empleado de cada evento de auditoría;
- ausencia de cruces entre empresas;
- rollback y limpieza final del fixture.

El arnés no usa Composer, PHPUnit ni dependencias nuevas. Usa el fixture
dedicado:

```text
bbdd/seed_avisos_reasignacion_multiempresa_pruebas.sql
```

> **No usar en producción.** Los modos que escriben datos están limitados a
> entornos `local` o `test`, requieren confirmación explícita y usan
> exclusivamente el fixture reservado entre los IDs 9201 y 9272.

## Requisitos

- Apache y MariaDB/MySQL del entorno local o de pruebas iniciados.
- Esquema base de EasyParte disponible.
- Migración `auditoria_evento` aplicada.
- Roles base `Administrador`, `Tecnico` y `Atencion al Cliente` disponibles.
- PHP CLI con las extensiones `curl`, `PDO` y `pdo_mysql`.
- `mysql` para aplicar el seed.
- `mysqldump` en ejecuciones `local`, donde el backup es obligatorio.
- Directorio de backup existente, escribible y fuera de
  `backend/public`.

En XAMPP para Windows se recomienda:

```text
C:\xampp\php\php.exe
C:\xampp\mysql\bin\mysql.exe
C:\xampp\mysql\bin\mysqldump.exe
```

Los binarios son configurables y esas rutas no están impuestas como única
opción.

## Variables de entorno

| Variable | Uso |
|---|---|
| `EASYPARTE_TEST_ENV` | Debe ser exactamente `local` o `test`. |
| `EASYPARTE_API_URL` | URL base real de la API, terminada en `/api`; no se exige en `--rollback-only`. |
| `EASYPARTE_TEST_DB_HOST` | Host de la base de pruebas. |
| `EASYPARTE_TEST_DB_PORT` | Puerto MariaDB/MySQL. |
| `EASYPARTE_TEST_DB_NAME` | Base local/test que usa también la API. |
| `EASYPARTE_TEST_DB_USER` | Usuario DB local/test. |
| `EASYPARTE_TEST_DB_PASSWORD` | Contraseña DB; puede estar vacía únicamente en `local`. |
| `EASYPARTE_FIXTURE_PASSWORD` | Contraseña que debe verificar el hash bcrypt del seed; no se exige en `--rollback-only`. |
| `EASYPARTE_TEST_BACKUP_DIR` | Directorio protegido para backup; obligatorio en `local`, salvo `--rollback-only`. |
| `EASYPARTE_TEST_CONFIRM` | Debe ser exactamente `AVISOS_MULTIEMPRESA_9201_9272`. |
| `EASYPARTE_PHP_BIN` | Ruta del PHP CLI; por defecto usa el ejecutable en curso. |
| `EASYPARTE_MYSQL_BIN` | Ruta de `mysql`. |
| `EASYPARTE_MYSQLDUMP_BIN` | Ruta de `mysqldump`. |

La URL API y la configuración DB deben señalar al mismo entorno. Después de
aplicar el fixture, el arnés comprueba sus recuentos y que las sesiones API
reciben los datos esperados.

## Política de secretos

- No pasar contraseñas, tokens ni cabeceras mediante argumentos CLI.
- Definir los valores sensibles únicamente en el entorno del proceso o en un
  mecanismo local equivalente que esté excluido de Git.
- No pegar valores reales en este README, documentación, incidencias o
  resultados.
- Los JWT se mantienen solo en memoria durante la ejecución.
- La salida redacta contraseñas, tokens, `Authorization`, hashes bcrypt y DSN.
- El fichero temporal usado por las herramientas MySQL se elimina en un
  bloque `finally`.
- Los backups contienen datos sensibles: deben permanecer fuera del
  directorio público y no deben versionarse.

## Preparación en PowerShell

Ejemplo con valores no sensibles. Las contraseñas deben inyectarse por
separado en la sesión y no escribirse como argumentos del arnés:

```powershell
$env:EASYPARTE_TEST_ENV = 'local'
$env:EASYPARTE_API_URL = 'http://localhost/easyTrabajo/backend/public/api'
$env:EASYPARTE_TEST_DB_HOST = 'localhost'
$env:EASYPARTE_TEST_DB_PORT = '3306'
$env:EASYPARTE_TEST_DB_NAME = 'easyParte'
$env:EASYPARTE_TEST_DB_USER = 'root'
$env:EASYPARTE_TEST_BACKUP_DIR = 'C:\ruta-protegida\backups'
$env:EASYPARTE_TEST_CONFIRM = 'AVISOS_MULTIEMPRESA_9201_9272'
$env:EASYPARTE_PHP_BIN = 'C:\xampp\php\php.exe'
$env:EASYPARTE_MYSQL_BIN = 'C:\xampp\mysql\bin\mysql.exe'
$env:EASYPARTE_MYSQLDUMP_BIN = 'C:\xampp\mysql\bin\mysqldump.exe'
```

Además debe existir en el entorno:

```text
EASYPARTE_FIXTURE_PASSWORD
```

`EASYPARTE_TEST_DB_PASSWORD` también debe inyectarse cuando la cuenta tenga
contraseña y es obligatoria en `test`; solo puede omitirse o quedar vacía en
`local`. No se ofrece un valor de ejemplo para ninguna contraseña.

## Preflight

```powershell
& 'C:\xampp\php\php.exe' `
  'tests/integration/avisos_reasignacion_multiempresa/run.php' `
  --preflight
```

`--preflight`:

- valida entorno, confirmación, URL, DB, extensiones y binarios;
- comprueba que la API responde;
- abre la conexión DB y ejecuta únicamente consultas de lectura;
- comprueba esquema, roles base y ausencia de colisiones/residuos;
- valida el seed y la contraseña del fixture;
- no aplica el seed, no crea datos y no ejecuta rollback.

## Ejecución completa

Revisar primero que `--preflight` termina correctamente:

```powershell
& 'C:\xampp\php\php.exe' `
  'tests/integration/avisos_reasignacion_multiempresa/run.php' `
  --run
```

`--run`:

1. adquiere un bloqueo para impedir ejecuciones simultáneas;
2. repite el preflight;
3. crea y verifica un backup obligatorio en `local`;
4. aplica el seed dedicado y comprueba los recuentos;
5. captura un baseline dinámico de auditoría;
6. crea las cinco sesiones fixture;
7. ejecuta primero las negativas y después las positivas;
8. verifica estado, eventos y cero cruces;
9. intenta siempre el rollback en `finally`;
10. comprueba que no quedan filas o eventos y que los tres roles base siguen
    intactos.

Las pruebas son fail-fast: una aserción fallida detiene la matriz para no
contaminar operaciones posteriores, pero mantiene el intento de rollback.

## Rollback independiente

```powershell
& 'C:\xampp\php\php.exe' `
  'tests/integration/avisos_reasignacion_multiempresa/run.php' `
  --rollback-only
```

`--rollback-only`:

- no llama a la API;
- no exige `EASYPARTE_FIXTURE_PASSWORD`;
- no exige backup ni herramientas MySQL;
- requiere entorno, confirmación y configuración DB segura;
- elimina en el orden exacto eventos, avisos, clientes, usuarios y roles de
  usuario, empleados, departamentos y empresas del fixture;
- confirma cero residuos y roles base intactos.

Debe usarse para recuperar una ejecución interrumpida. No desactiva claves
foráneas ni elimina recursos fuera de los IDs reservados.

## Códigos de salida

| Código | Significado |
|---:|---|
| `0` | Matriz correcta y limpieza completa, o preflight/rollback-only correcto. |
| `1` | Falló una prueba o una fase posterior al seed, pero el rollback quedó correcto. |
| `2` | Preflight o configuración bloqueados sin aplicar el fixture. |
| `3` | Falló el rollback, la limpieza o la liberación segura del bloqueo. |

## Riesgos pendientes

- Apache y el arnés deben apuntar a la misma base; no existe un endpoint de
  diagnóstico DB que permita demostrarlo antes del seed.
- Una interrupción forzada del proceso puede impedir que se ejecute `finally`;
  por eso existe `--rollback-only`.
- La restauración del backup sigue requiriendo una prueba separada.
- La matriz terminal automatiza ocho rechazos sobre avisos `Finalizada` o
  `Cancelada`; su ejecución sigue limitada a local/test.
- La verificación de auditoría se realiza mediante acceso DB local/test hasta
  disponer de un endpoint protegido.
- Esta matriz no acredita tenant o auditoría general del sistema.

## Estados funcionales

La automatización no cambia los siguientes estados:

- INC-0013 permanece abierta.
- PEN-0006 permanece parcial.
- PEN-0009 permanece parcial.

## Fixture y matriz automatizada

El fixture crea dos empresas, dos departamentos, cuatro empleados, dos
clientes, cinco usuarios, cinco relaciones de rol y diez avisos. Empresa A
incluye:

- Administrador `9241`;
- Tecnico `9242`, vinculado al empleado `9211`;
- Atencion al Cliente `9243`, sin empleado vinculado;
- avisos `9261` a `9268`, incluidos un `Finalizada` asignado, un
  `Finalizada` libre, un `Cancelada` asignado y un `Cancelada` libre.

Empresa B conserva los usuarios `9251` y `9252` y los avisos `9271` y `9272`
para los controles de aislamiento.

La ejecución completa produce veintinueve resultados:

- cinco autenticaciones;
- quince pruebas negativas, incluidas `FIN-01` a `FIN-08`;
- nueve pruebas positivas;
- cuatro operaciones positivas y exactamente cuatro eventos de auditoría.

Cada rechazo terminal exige HTTP 403, compara el estado completo del aviso
antes y después, usa un baseline individual, exige cero eventos y revisa que
la respuesta no exponga SQLSTATE, trazas, rutas internas ni detalles SQL.
