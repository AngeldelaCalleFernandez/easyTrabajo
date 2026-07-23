-- Migración aditiva: crea el registro persistente de eventos de auditoría.
-- Aplicación manual:
--   1. Revisar el esquema, los índices y las restricciones en un entorno de prueba.
--   2. Realizar una copia de seguridad antes de aplicar la migración.
--   3. Ejecutar este archivo una sola vez contra la base de datos objetivo.
--
-- Compatibilidad JSON:
-- Se usa LONGTEXT para mantener un comportamiento homogéneo entre MariaDB y
-- MySQL. MariaDB implementa JSON como alias de LONGTEXT, mientras que MySQL
-- dispone de un tipo JSON nativo. Las restricciones JSON_VALID impiden guardar
-- documentos no válidos; la aplicación también debe codificar y validar JSON.

CREATE TABLE `auditoria_evento` (
  `id_auditoria_evento` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `entidad` varchar(64) NOT NULL,
  `entidad_id` bigint unsigned NOT NULL,
  `accion` varchar(64) NOT NULL,
  `valores_anteriores` longtext DEFAULT NULL,
  `valores_nuevos` longtext DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria_evento`),
  KEY `idx_auditoria_empresa_fecha` (`id_empresa`, `fecha`),
  KEY `idx_auditoria_entidad_registro_fecha` (`entidad`, `entidad_id`, `fecha`),
  KEY `idx_auditoria_entidad_id` (`entidad_id`),
  KEY `idx_auditoria_fecha` (`fecha`),
  KEY `idx_auditoria_usuario` (`id_usuario`),
  CONSTRAINT `chk_auditoria_valores_anteriores_json`
    CHECK (`valores_anteriores` IS NULL OR JSON_VALID(`valores_anteriores`)),
  CONSTRAINT `chk_auditoria_valores_nuevos_json`
    CHECK (`valores_nuevos` IS NULL OR JSON_VALID(`valores_nuevos`)),
  CONSTRAINT `fk_auditoria_evento_empresa`
    FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`)
    ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT `fk_auditoria_evento_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
    ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Reversión manual:
-- ATENCIÓN: esta operación elimina todos los eventos de auditoría almacenados.
-- Ejecutar únicamente tras copia de seguridad y aprobación explícita.
-- DROP TABLE `auditoria_evento`;
