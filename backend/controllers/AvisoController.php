<?php

class AvisoController
{
    private $conn;
    private $tabla = "tarea";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function belongsToEmpresa($table, $idField, $id, $idEmpresa, $activeField = null)
    {
        if ($id === null || $id === '') {
            return true;
        }

        $allowedTables = ['cliente', 'empleado', 'departamento'];
        if (!in_array($table, $allowedTables, true)) {
            return false;
        }

        $query = "SELECT COUNT(*) FROM " . $table . " WHERE " . $idField . " = :id AND id_empresa = :id_empresa";
        if ($activeField !== null) {
            $query .= " AND " . $activeField . " = 1";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id' => $id,
            'id_empresa' => $idEmpresa
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    private function validateRelatedIds($idCliente, $idEmpleado, $idDepartamento, $idEmpresa)
    {
        if (!$this->belongsToEmpresa('cliente', 'id_cliente', $idCliente, $idEmpresa)) {
            return false;
        }

        if (!$this->belongsToEmpresa('empleado', 'id_empleado', $idEmpleado, $idEmpresa)) {
            return false;
        }

        if (!$this->belongsToEmpresa('departamento', 'id_departamento', $idDepartamento, $idEmpresa)) {
            return false;
        }

        return true;
    }

    private function denyForeignRelation()
    {
        http_response_code(403);
        echo json_encode(["error" => "No tienes permisos para usar uno de los recursos relacionados."]);
    }

    // CARGAR AVISOS 
    public function getAll($usuarioLogueado)
    {
        // Seguridad: Filtramos siempre por id_empresa y añadimos JOINs para ver nombres
        $query = "SELECT 
                    a.*, 
                    c.nombre as cliente_nombre,
                    CONCAT(e.nombre, ' ', e.apellido) as tecnico_nombre
                  FROM " . $this->tabla . " a
                  LEFT JOIN cliente c ON a.id_cliente = c.id_cliente AND c.id_empresa = a.id_empresa
                  LEFT JOIN empleado e ON a.id_empleado = e.id_empleado AND e.id_empresa = a.id_empresa
                  WHERE a.id_empresa = :id_empresa ";
        
        // Si es Técnico, solo ve sus avisos o los que no tienen nadie asignado
        if ($usuarioLogueado->rol_nombre === 'Técnico') {
            $query .= " AND (a.id_empleado = :id_empleado OR a.id_empleado IS NULL)";
        }

        $query .= " ORDER BY a.fecha_alta DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        
        if ($usuarioLogueado->rol_nombre === 'Técnico') {
            $stmt->bindParam(":id_empleado", $usuarioLogueado->id_empleado);
        }

        $stmt->execute();
        $avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($avisos ? $avisos : []);
    }

    // CREAR AVISO 
    public function create($data, $usuarioLogueado)
    {
        if (empty($data->descripcion) || empty($data->id_cliente)) {
            http_response_code(400);
            echo json_encode(["error" => "La descripción y el cliente son obligatorios."]);
            return;
        }

        $id_empleado = !empty($data->id_empleado) ? $data->id_empleado : null;
        $id_departamento = !empty($data->id_departamento) ? $data->id_departamento : null;

        if (!$this->validateRelatedIds($data->id_cliente, $id_empleado, $id_departamento, $usuarioLogueado->id_empresa)) {
            $this->denyForeignRelation();
            return;
        }

        $query = "INSERT INTO " . $this->tabla . " 
                  (descripcion, importancia, estado, persona_contacto, telefono_contacto, 
                   id_empleado, id_cliente, id_departamento, id_empresa, id_usuario_creador, fecha_alta) 
                  VALUES (:descripcion, :importancia, :estado, :persona_contacto, :telefono_contacto, 
                          :id_empleado, :id_cliente, :id_departamento, :id_empresa, :id_usuario_creador, NOW())";

        $stmt = $this->conn->prepare($query);

        // Valores por defecto
        $importancia = !empty($data->importancia) ? $data->importancia : 'Normal';
        $estado = !empty($data->estado) ? $data->estado : 'Pendiente';
        $persona_contacto = !empty($data->persona_contacto) ? $data->persona_contacto : null;
        $telefono_contacto = !empty($data->telefono_contacto) ? $data->telefono_contacto : null;
        // BIND DE DATOS DEL FORMULARIO
        $stmt->bindParam(":descripcion", $data->descripcion);
        $stmt->bindParam(":importancia", $importancia);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":persona_contacto", $persona_contacto);
        $stmt->bindParam(":telefono_contacto", $telefono_contacto);
        $stmt->bindParam(":id_empleado", $id_empleado);
        $stmt->bindParam(":id_cliente", $data->id_cliente);
        $stmt->bindParam(":id_departamento", $id_departamento);

        // BIND DE DATOS DEL TOKEN 
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        $stmt->bindParam(":id_usuario_creador", $usuarioLogueado->id_usuario);

        try {
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Aviso creado con éxito", "id" => $this->conn->lastInsertId()]);
            }
        } catch (PDOException $e) {
            error_log("Error al guardar aviso: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(["error" => "No se ha podido guardar el aviso."]);
        }
    }

    // ACTUALIZAR AVISOS 
    public function update($id, $data, $usuarioLogueado)
    {
        // Verificar que el aviso existe y pertenece a la empresa
        $query_check = "SELECT * FROM " . $this->tabla . " WHERE id_tarea = :id AND id_empresa = :id_empresa";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":id", $id);
        $stmt_check->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        $stmt_check->execute();

        if ($stmt_check->rowCount() == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Aviso no encontrado o no pertenece a su empresa."]);
            return;
        }

        $actual = $stmt_check->fetch(PDO::FETCH_ASSOC);

        // Lógica de combinación de datos 
        $descripcion = isset($data->descripcion) ? $data->descripcion : $actual['descripcion'];
        $importancia = isset($data->importancia) ? $data->importancia : $actual['importancia'];
        $estado = isset($data->estado) ? $data->estado : $actual['estado'];
        $persona_contacto = property_exists($data, 'persona_contacto') ? $data->persona_contacto : $actual['persona_contacto'];
        $telefono_contacto = property_exists($data, 'telefono_contacto') ? $data->telefono_contacto : $actual['telefono_contacto'];
        $id_empleado = property_exists($data, 'id_empleado') ? (!empty($data->id_empleado) ? $data->id_empleado : null) : $actual['id_empleado'];
        $id_cliente = isset($data->id_cliente) ? $data->id_cliente : $actual['id_cliente'];
        $id_departamento = property_exists($data, 'id_departamento') ? (!empty($data->id_departamento) ? $data->id_departamento : null) : $actual['id_departamento'];

        if (!$this->validateRelatedIds($id_cliente, $id_empleado, $id_departamento, $usuarioLogueado->id_empresa)) {
            $this->denyForeignRelation();
            return;
        }

        // Control de fecha de fin
        $fecha_fin = $actual['fecha_fin'];
        if (($estado === 'Finalizada' || $estado === 'Cancelada') && empty($fecha_fin)) {
            $fecha_fin = date('Y-m-d H:i:s');
        } elseif ($estado !== 'Finalizada' && $estado !== 'Cancelada') {
            $fecha_fin = null;
        }

        $query = "UPDATE " . $this->tabla . " 
                  SET descripcion=:descripcion, importancia=:importancia, estado=:estado, 
                      persona_contacto=:persona_contacto, telefono_contacto=:telefono_contacto, 
                      id_empleado=:id_empleado, id_cliente=:id_cliente, id_departamento=:id_departamento, fecha_fin=:fecha_fin 
                  WHERE id_tarea = :id AND id_empresa = :id_empresa";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":importancia", $importancia);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":persona_contacto", $persona_contacto);
        $stmt->bindParam(":telefono_contacto", $telefono_contacto);
        $stmt->bindParam(":id_empleado", $id_empleado);
        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":id_departamento", $id_departamento);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);


        try {
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Aviso actualizado"]);
            }
        } catch (PDOException $e) {
            error_log("Error al actualizar aviso: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(["error" => "No se ha podido actualizar el aviso."]);
        }
    }

    // ELIMINAR UN AVISO 
    public function delete($id, $usuarioLogueado)
    {
        $query = "DELETE FROM " . $this->tabla . " WHERE id_tarea = :id AND id_empresa = :id_empresa";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Aviso eliminado"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No se pudo eliminar el aviso."]);
        }
    }
}
