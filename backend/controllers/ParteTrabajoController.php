<?php

class ParteTrabajoController
{
    private $conn;
    private $tabla = "parte_trabajo";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function belongsToEmpresa($table, $idField, $id, $idEmpresa, $activeField = null)
    {
        if ($id === null || $id === '') {
            return true;
        }

        $allowedTables = ['cliente', 'empleado', 'tarea'];
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

    private function validateRelatedIds($idCliente, $idTarea, $idEmpleado, $idEmpresa)
    {
        if (!$this->belongsToEmpresa('cliente', 'id_cliente', $idCliente, $idEmpresa)) {
            return false;
        }

        if (!$this->belongsToEmpresa('tarea', 'id_tarea', $idTarea, $idEmpresa)) {
            return false;
        }

        if (!$this->belongsToEmpresa('empleado', 'id_empleado', $idEmpleado, $idEmpresa)) {
            return false;
        }

        return true;
    }

    private function denyForeignRelation()
    {
        http_response_code(403);
        echo json_encode(["error" => "No tienes permisos para usar uno de los recursos relacionados."]);
    }

    private function userHasRole($usuarioLogueado, array $rolesPermitidos)
    {
        $rol = isset($usuarioLogueado->rol_nombre) ? $usuarioLogueado->rol_nombre : '';
        return in_array($rol, $rolesPermitidos, true);
    }

    private function isTecnico($usuarioLogueado)
    {
        $rol = isset($usuarioLogueado->rol_nombre) ? trim((string)$usuarioLogueado->rol_nombre) : '';
        $rolNormalizado = strtolower(strtr($rol, ['É' => 'e', 'é' => 'e']));

        return $rolNormalizado === 'tecnico';
    }

    private function denyPermission($message = "No tienes permisos para realizar esta accion.")
    {
        http_response_code(403);
        echo json_encode(["error" => $message]);
    }

    private function tareaAsignadaAlTecnico($idTarea, $idEmpleado, $idEmpresa)
    {
        if ($idTarea === null || $idTarea === '') {
            return true;
        }

        $query = "SELECT COUNT(*) FROM tarea
                  WHERE id_tarea = :id_tarea
                    AND id_empresa = :id_empresa
                    AND id_empleado = :id_empleado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id_tarea' => $idTarea,
            'id_empresa' => $idEmpresa,
            'id_empleado' => $idEmpleado
        ]);

        return (int)$stmt->fetchColumn() > 0;
    }

    // OBTENER TODOS LOS PARTES
    public function getAll($usuarioLogueado)
    {
        $query = "SELECT 
                    p.*, 
                    c.nombre as cliente_nombre,
                    CONCAT(e.nombre, ' ', e.apellido) as tecnico_nombre
                  FROM " . $this->tabla . " p
                  LEFT JOIN cliente c ON p.id_cliente = c.id_cliente AND c.id_empresa = p.id_empresa
                  LEFT JOIN empleado e ON p.id_empleado = e.id_empleado AND e.id_empresa = p.id_empresa
                  WHERE p.id_empresa = :id_empresa AND p.activo = 1";

        if ($this->isTecnico($usuarioLogueado)) {
            $query .= " AND p.id_empleado = :id_empleado";
        }

        $query .= " ORDER BY p.fecha_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);

        if ($this->isTecnico($usuarioLogueado)) {
            $stmt->bindParam(":id_empleado", $usuarioLogueado->id_empleado);
        }

        $stmt->execute();

        http_response_code(200);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // CREAR UN PARTE DE TRABAJO
    public function create($data, $usuarioLogueado)
    {
        if (empty($data->descripcion) || empty($data->id_cliente)) {
            http_response_code(400);
            echo json_encode(["error" => "La descripción y el cliente son obligatorios."]);
            return;
        }

        
        $query = "INSERT INTO " . $this->tabla . " 
                  (id_empresa, descripcion, estado, id_cliente, id_tarea, id_empleado, horas, material, observaciones) 
                  VALUES (:id_empresa, :descripcion, 'En curso', :id_cliente, :id_tarea, :id_empleado, :horas, :material, :observaciones)";

        $stmt = $this->conn->prepare($query);

        $id_tarea = !empty($data->id_tarea) ? $data->id_tarea : null;

        if ($this->isTecnico($usuarioLogueado)) {
            if (empty($usuarioLogueado->id_empleado)) {
                $this->denyPermission("No tienes permisos para crear este parte.");
                return;
            }

            if (!empty($data->id_empleado) && (int)$data->id_empleado !== (int)$usuarioLogueado->id_empleado) {
                $this->denyPermission("No tienes permisos para crear este parte.");
                return;
            }

            if (!$this->tareaAsignadaAlTecnico($id_tarea, $usuarioLogueado->id_empleado, $usuarioLogueado->id_empresa)) {
                $this->denyPermission("No tienes permisos para crear este parte.");
                return;
            }

            $id_empleado = $usuarioLogueado->id_empleado;
        } else {
            $id_empleado = !empty($data->id_empleado) ? $data->id_empleado : $usuarioLogueado->id_empleado;
        }

        $horas = !empty($data->horas) ? $data->horas : 0;
        $material = !empty($data->material) ? $data->material : null;
        $observaciones = !empty($data->observaciones) ? $data->observaciones : null;

        if (!$this->validateRelatedIds($data->id_cliente, $id_tarea, $id_empleado, $usuarioLogueado->id_empresa)) {
            $this->denyForeignRelation();
            return;
        }

        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        $stmt->bindParam(":descripcion", $data->descripcion);
        $stmt->bindParam(":id_cliente", $data->id_cliente);
        $stmt->bindParam(":id_tarea", $id_tarea);
        $stmt->bindParam(":id_empleado", $id_empleado);
        $stmt->bindParam(":horas", $horas);
        $stmt->bindParam(":material", $material);
        $stmt->bindParam(":observaciones", $observaciones);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Parte de trabajo guardado (En curso)", "id" => $this->conn->lastInsertId()]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al guardar el parte."]);
        }
    }

    // ACTUALIZAR EDITAR
    public function update($id, $data, $usuarioLogueado)
    {
        try {
            $query_check = "SELECT * FROM " . $this->tabla . "
                            WHERE id_parte_trabajo = :id AND id_empresa = :id_empresa AND activo = 1";
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(":id", $id);
            $stmt_check->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
            $stmt_check->execute();

            if ($stmt_check->rowCount() == 0) {
                http_response_code(404);
                echo json_encode(["error" => "Parte de trabajo no encontrado."]);
                return;
            }

            $parteActual = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($this->isTecnico($usuarioLogueado) && $parteActual['id_empleado'] != $usuarioLogueado->id_empleado) {
                $this->denyPermission("No tienes permisos para actualizar este parte.");
                return;
            }

            $descripcion = isset($data->descripcion) ? $data->descripcion : $parteActual['descripcion'];
            $horas = isset($data->horas) ? $data->horas : $parteActual['horas'];
            $material = property_exists($data, 'material') ? $data->material : $parteActual['material'];
            $observaciones = property_exists($data, 'observaciones') ? $data->observaciones : $parteActual['observaciones'];
            $estado = isset($data->estado) ? $data->estado : $parteActual['estado'];
            $id_tarea = $parteActual['id_tarea'];

            $id_cliente_validar = property_exists($data, 'id_cliente') ? $data->id_cliente : $parteActual['id_cliente'];
            $id_tarea_validar = property_exists($data, 'id_tarea') ? $data->id_tarea : $parteActual['id_tarea'];
            $id_empleado_validar = property_exists($data, 'id_empleado') ? $data->id_empleado : $parteActual['id_empleado'];

            if ($this->isTecnico($usuarioLogueado) && (int)$id_empleado_validar !== (int)$usuarioLogueado->id_empleado) {
                $this->denyPermission("No tienes permisos para actualizar este parte.");
                return;
            }

            if (!$this->validateRelatedIds($id_cliente_validar, $id_tarea_validar, $id_empleado_validar, $usuarioLogueado->id_empresa)) {
                $this->denyForeignRelation();
                return;
            }

            $this->conn->beginTransaction();
            
            $query = "UPDATE " . $this->tabla . " 
                      SET descripcion=:descripcion, horas=:horas, material=:material, 
                          observaciones=:observaciones, estado=:estado ";
            
            // Si cerramos el parte se pone fecha fin
            if ($estado === 'Cerrado') {
                $query .= ", fecha_fin=NOW() ";
            }
            
            $query .= " WHERE id_parte_trabajo = :id AND id_empresa = :id_empresa AND activo = 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":horas", $horas);
            $stmt->bindParam(":material", $material);
            $stmt->bindParam(":observaciones", $observaciones);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
            $stmt->execute();

            // Si cerramos el parte y tiene un aviso asociado, damos por teminado el aviso
            if ($estado === 'Cerrado' && !empty($id_tarea)) {
                $qTarea = "UPDATE tarea SET estado = 'Finalizada', fecha_fin = NOW() WHERE id_tarea = :id_tarea AND id_empresa = :id_empresa";
                $stTarea = $this->conn->prepare($qTarea);
                $stTarea->bindParam(":id_tarea", $id_tarea);
                $stTarea->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
                $stTarea->execute();
            }

            $this->conn->commit();
            http_response_code(200);
            echo json_encode(["mensaje" => "Parte actualizado correctamente"]);

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error al actualizar parte de trabajo: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido actualizar el parte de trabajo."]);
        }
    }
}
