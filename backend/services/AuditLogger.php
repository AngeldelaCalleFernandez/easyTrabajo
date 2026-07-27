<?php

class AuditLogger
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function registrar(
        $idEmpresa,
        $idUsuario,
        $entidad,
        $entidadId,
        $accion,
        $valoresAnteriores,
        $valoresNuevos
    ) {
        $idEmpresa = (int)$idEmpresa;
        $idUsuario = (int)$idUsuario;
        $entidadId = (int)$entidadId;
        $entidad = trim((string)$entidad);
        $accion = trim((string)$accion);

        if ($idEmpresa <= 0 || $idUsuario <= 0 || $entidadId <= 0 || $entidad === '' || $accion === '') {
            throw new InvalidArgumentException('Datos de auditoria no validos.');
        }

        if (strlen($entidad) > 64 || strlen($accion) > 64) {
            throw new InvalidArgumentException('Datos de auditoria fuera de rango.');
        }

        $anterioresJson = $this->encodeJson($valoresAnteriores);
        $nuevosJson = $this->encodeJson($valoresNuevos);

        $query = "INSERT INTO auditoria_evento
                    (id_empresa, id_usuario, entidad, entidad_id, accion, valores_anteriores, valores_nuevos)
                  VALUES
                    (:id_empresa, :id_usuario, :entidad, :entidad_id, :accion, :valores_anteriores, :valores_nuevos)";

        $stmt = $this->conn->prepare($query);
        $registered = $stmt->execute([
            'id_empresa' => $idEmpresa,
            'id_usuario' => $idUsuario,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'accion' => $accion,
            'valores_anteriores' => $anterioresJson,
            'valores_nuevos' => $nuevosJson
        ]);

        if (!$registered) {
            throw new RuntimeException('No se ha podido registrar el evento de auditoria.');
        }

        return true;
    }

    private function encodeJson($value)
    {
        $sanitized = $this->removeSensitiveValues($value);
        $json = json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('No se han podido codificar los datos de auditoria.');
        }

        return $json;
    }

    private function removeSensitiveValues($value)
    {
        if (is_object($value)) {
            $value = get_object_vars($value);
        }

        if (!is_array($value)) {
            return $value;
        }

        $result = [];
        foreach ($value as $key => $item) {
            if (is_string($key) && $this->isSensitiveKey($key)) {
                continue;
            }

            $result[$key] = $this->removeSensitiveValues($item);
        }

        return $result;
    }

    private function isSensitiveKey($key)
    {
        $normalizedKey = strtolower((string)$key);
        $sensitiveFragments = [
            'password',
            'contrasena',
            'contraseña',
            'password_hash',
            'token',
            'authorization',
            'jwt',
            'secret',
            'secreto',
            'cookie'
        ];

        foreach ($sensitiveFragments as $fragment) {
            if (strpos($normalizedKey, $fragment) !== false) {
                return true;
            }
        }

        return false;
    }
}
