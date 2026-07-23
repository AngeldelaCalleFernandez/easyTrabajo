# Datos de prueba multiempresa

Fecha de creacion: 2026-05-22
Alcance: preparar validacion manual de `AGENT/testing/tenant_minimo.md`.

Este documento define datos minimos para comprobar que la primera fase de tenant minimo no mezcla recursos entre empresas. No usar estos datos en produccion ni mezclarlos con datos reales.

## Objetivo

Preparar:

- Empresa A.
- Empresa B.
- Administrador Empresa A.
- Administrador Empresa B.
- Cliente Empresa A.
- Cliente Empresa B.
- Empleado Empresa A.
- Empleado Empresa B.
- Departamento Empresa A.
- Departamento Empresa B.
- Aviso/Tarea Empresa A.
- Aviso/Tarea Empresa B.
- Parte Empresa A.
- Parte Empresa B.

## Seed opcional

Se ha preparado el script separado:

```txt
bbdd/seed_multiempresa_pruebas.sql
```

Reglas:

- No modifica `bbdd/export_base_datos.sql`.
- Usa IDs altos reservados para pruebas.
- Elimina previamente solo esos IDs de prueba para poder re-ejecutarse.
- Incluye rollback comentado.
- No contiene passwords en claro; los usuarios usan hashes demo existentes solo para facilitar login local de prueba.

## IDs reservados

```txt
Empresa A: 9101
Empresa B: 9102

Departamento A: 9101
Departamento B: 9102

Empleado A: 9101
Empleado B: 9102

Cliente A: 9101
Cliente B: 9102

Usuario admin A: 9101
Usuario admin B: 9102

Aviso/Tarea A: 9101
Aviso/Tarea B: 9102

Parte A: 9101
Parte B: 9102
```

## Usuarios de prueba

```txt
admin.empresa-a.tenant@test.local
admin.empresa-b.tenant@test.local
```

Nota: el seed reutiliza un hash demo ya presente en el dump local. Si la clave no es conocida por la persona que prueba, puede generar un hash local con `password_hash()` y sustituirlo antes de importar el seed. No documentar passwords reales.

## Como preparar datos

1. Hacer backup de la base local si hay dudas.
2. Revisar que se trabaja en XAMPP/local, nunca produccion.
3. Importar `bbdd/seed_multiempresa_pruebas.sql`.
4. Iniciar sesion como admin Empresa A.
5. Iniciar sesion como admin Empresa B en otra sesion/navegador o copiar ambos tokens para pruebas HTTP.

## Pruebas manuales

### 1. GET /api/empleados con Empresa A

1. Iniciar sesion como `admin.empresa-a.tenant@test.local`.
2. Ejecutar `GET /api/empleados`.
3. Verificar que aparece `Empleado Tenant A`.
4. Verificar que no aparece `Empleado Tenant B`.

Resultado esperado: HTTP 200 y solo empleados de Empresa A.

### 2. GET /api/empleados con Empresa B

1. Iniciar sesion como `admin.empresa-b.tenant@test.local`.
2. Ejecutar `GET /api/empleados`.
3. Verificar que aparece `Empleado Tenant B`.
4. Verificar que no aparece `Empleado Tenant A`.

Resultado esperado: HTTP 200 y solo empleados de Empresa B.

### 3. Crear aviso con cliente de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: cliente ajeno",
  "id_cliente": 9102
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 4. Crear aviso con empleado de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: empleado ajeno",
  "id_cliente": 9101,
  "id_empleado": 9102
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 5. Crear aviso con departamento de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: departamento ajeno",
  "id_cliente": 9101,
  "id_departamento": 9102
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 6. Crear aviso valido con recursos de la misma empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: aviso valido",
  "id_cliente": 9101,
  "id_empleado": 9101,
  "id_departamento": 9101
}
```

Resultado esperado: HTTP 201.

### 7. Crear parte con cliente de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: parte cliente ajeno",
  "id_cliente": 9102
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 8. Crear parte con aviso/tarea de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: parte tarea ajena",
  "id_cliente": 9101,
  "id_tarea": 9102,
  "id_empleado": 9101
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 9. Crear parte con empleado de otra empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: parte empleado ajeno",
  "id_cliente": 9101,
  "id_empleado": 9102
}
```

Resultado esperado: HTTP 403 con mensaje generico.

### 10. Crear parte valido con recursos de la misma empresa

Con token de Empresa A:

```json
{
  "descripcion": "Prueba tenant: parte valido",
  "id_cliente": 9101,
  "id_tarea": 9101,
  "id_empleado": 9101,
  "horas": 1
}
```

Resultado esperado: HTTP 201.

## Limpieza

El propio seed incluye un bloque inicial que borra solo los IDs `9101` y `9102` de las tablas afectadas. Tambien incluye al final un rollback comentado para eliminar esos datos manualmente.

No ejecutar la limpieza en produccion.
