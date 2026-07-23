-- Seed de prueba multiempresa para validar tenant minimo.
-- NO ejecutar en produccion.
-- NO sustituye a bbdd/export_base_datos.sql.
-- Es no destructivo sobre datos reales: solo elimina/crea IDs reservados 9101 y 9102.

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM parte_trabajo WHERE id_parte_trabajo IN (9101, 9102);
DELETE FROM tarea WHERE id_tarea IN (9101, 9102);
DELETE FROM usuario_rol WHERE id_usuario IN (9101, 9102);
DELETE FROM usuario WHERE id_usuario IN (9101, 9102);
DELETE FROM empleado WHERE id_empleado IN (9101, 9102);
DELETE FROM cliente WHERE id_cliente IN (9101, 9102);
DELETE FROM departamento WHERE id_departamento IN (9101, 9102);
DELETE FROM empresa WHERE id_empresa IN (9101, 9102);

SET FOREIGN_KEY_CHECKS = 1;

-- Empresas de prueba.
INSERT INTO empresa
  (id_empresa, nif, nombre, prefijo, telefono, email, pais, poblacion, direccion, activo)
VALUES
  (9101, 'T9101001A', 'Tenant Prueba Empresa A', '+34', '910100001', 'empresa-a.tenant@test.local', 'Espana', 'Madrid', 'Calle Tenant A 1', 1),
  (9102, 'T9102001B', 'Tenant Prueba Empresa B', '+34', '910200001', 'empresa-b.tenant@test.local', 'Espana', 'Madrid', 'Calle Tenant B 1', 1);

-- Departamentos de prueba.
INSERT INTO departamento
  (id_departamento, nombre, id_empresa)
VALUES
  (9101, 'Departamento Tenant A', 9101),
  (9102, 'Departamento Tenant B', 9102);

-- Empleados de prueba.
INSERT INTO empleado
  (id_empleado, nombre, apellido, apellido_2, extension_tel, prefijo, movil, nif, activo, id_departamento, id_empresa)
VALUES
  (9101, 'Empleado', 'Tenant A', NULL, NULL, '+34', '600910101', 'T910101A', 1, 9101, 9101),
  (9102, 'Empleado', 'Tenant B', NULL, NULL, '+34', '600910201', 'T910201B', 1, 9102, 9102);

-- Clientes de prueba.
INSERT INTO cliente
  (id_cliente, nif, nombre, poblacion, direccion, prefijo, contacto, email, id_empresa, cuota, activo)
VALUES
  (9101, 'T910102A', 'Cliente Tenant A', 'Madrid', 'Calle Cliente A 1', '+34', '600910102', 'cliente-a.tenant@test.local', 9101, 0, 1),
  (9102, 'T910202B', 'Cliente Tenant B', 'Madrid', 'Calle Cliente B 1', '+34', '600910202', 'cliente-b.tenant@test.local', 9102, 0, 1);

-- Usuarios administradores de prueba.
-- Hash demo reutilizado del dump local. Si se desconoce la clave, sustituir por un hash local generado con password_hash().
INSERT INTO usuario
  (id_usuario, nombre, email, password_hash, activo, id_empresa, id_empleado, created_at)
VALUES
  (9101, 'Admin Tenant A', 'admin.empresa-a.tenant@test.local', '$2y$10$lS3nUlgTuDzKEcHsTkHt8uBU1wX8CicGr0GwXoFOqM5b6POtxHXye', 1, 9101, NULL, NOW()),
  (9102, 'Admin Tenant B', 'admin.empresa-b.tenant@test.local', '$2y$10$lS3nUlgTuDzKEcHsTkHt8uBU1wX8CicGr0GwXoFOqM5b6POtxHXye', 1, 9102, NULL, NOW());

INSERT INTO usuario_rol
  (id_usuario, id_rol)
VALUES
  (9101, 1),
  (9102, 1);

-- Avisos/tareas de prueba.
INSERT INTO tarea
  (id_tarea, descripcion, fecha_alta, fecha_fin, importancia, estado, persona_contacto, telefono_contacto, id_empleado, id_cliente, id_departamento, id_empresa, id_usuario_creador)
VALUES
  (9101, 'Aviso Tenant A', NOW(), NULL, 'Normal', 'Pendiente', 'Contacto A', '600910103', 9101, 9101, 9101, 9101, 9101),
  (9102, 'Aviso Tenant B', NOW(), NULL, 'Normal', 'Pendiente', 'Contacto B', '600910203', 9102, 9102, 9102, 9102, 9102);

-- Partes de prueba.
INSERT INTO parte_trabajo
  (id_parte_trabajo, id_empresa, descripcion, fecha_inicio, fecha_fin, estado, id_cliente, id_tarea, id_empleado, horas, material, observaciones, firma_cliente, activo)
VALUES
  (9101, 9101, 'Parte Tenant A', NOW(), NULL, 'En curso', 9101, 9101, 9101, 1.00, NULL, 'Parte de prueba Empresa A', NULL, 1),
  (9102, 9102, 'Parte Tenant B', NOW(), NULL, 'En curso', 9102, 9102, 9102, 1.00, NULL, 'Parte de prueba Empresa B', NULL, 1);

-- Rollback manual opcional:
-- SET FOREIGN_KEY_CHECKS = 0;
-- DELETE FROM parte_trabajo WHERE id_parte_trabajo IN (9101, 9102);
-- DELETE FROM tarea WHERE id_tarea IN (9101, 9102);
-- DELETE FROM usuario_rol WHERE id_usuario IN (9101, 9102);
-- DELETE FROM usuario WHERE id_usuario IN (9101, 9102);
-- DELETE FROM empleado WHERE id_empleado IN (9101, 9102);
-- DELETE FROM cliente WHERE id_cliente IN (9101, 9102);
-- DELETE FROM departamento WHERE id_departamento IN (9101, 9102);
-- DELETE FROM empresa WHERE id_empresa IN (9101, 9102);
-- SET FOREIGN_KEY_CHECKS = 1;
