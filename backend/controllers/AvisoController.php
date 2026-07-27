<?php

require_once __DIR__ . '/../services/AuditLogger.php';

class AvisoController
{
    private $conn;
    private $tabla = "tarea";
    private $auditLogger;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->auditLogger = new AuditLogger($db);
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

    private function userHasRole($usuarioLogueado, array $rolesPermitidos)
    {
        $rol = isset($usuarioLogueado->rol_nombre) ? $usuarioLogueado->rol_nombre : '';
        return in_array($rol, $rolesPermitidos, true);
    }

    private function isTecnico($usuarioLogueado)
    {
        return $this->userHasRole($usuarioLogueado, ['Tecnico', 'Técnico', 'TÃ©cnico']);
    }

    private function canManageAvisos($usuarioLogueado)
    {
        return $this->userHasRole(
            $usuarioLogueado,
            ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente']
        );
    }

    private function denyPermission()
    {
        http_response_code(403);
        echo json_encode(["error" => "No tienes permisos para realizar esta accion."]);
    }

    private function rollbackIfActive()
    {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
    }

    private function activeEmployeeBelongsToEmpresa($idEmpleado, $idEmpresa)
    {
        return $this->belongsToEmpresa(
            'empleado',
            'id_empleado',
            $idEmpleado,
            $idEmpresa,
            'activo'
        );
    }

    // CARGAR EMPLEADOS ACTIVOS DISPONIBLES PARA ASIGNAR AVISOS
    public function getAssignableEmployees($usuarioLogueado)
    {
        if (!$this->canManageAvisos($usuarioLogueado) && !$this->isTecnico($usuarioLogueado)) {
            $this->denyPermission();
            return;
        }

        $idEmpresa = isset($usuarioLogueado->id_empresa)
            ? filter_var($usuarioLogueado->id_empresa, FILTER_VALIDATE_INT)
            : false;

        if ($idEmpresa === false || $idEmpresa <= 0) {
            $this->denyPermission();
            return;
        }

        $idEmpleadoTecnico = null;
        if ($this->isTecnico($usuarioLogueado)) {
            $idEmpleadoTecnico = isset($usuarioLogueado->id_empleado)
                ? filter_var($usuarioLogueado->id_empleado, FILTER_VALIDATE_INT)
                : false;

            if ($idEmpleadoTecnico === false || $idEmpleadoTecnico <= 0) {
                $this->denyPermission();
                return;
            }
        }

        try {
            if (
                $idEmpleadoTecnico !== null
                && !$this->activeEmployeeBelongsToEmpresa($idEmpleadoTecnico, $idEmpresa)
            ) {
                $this->denyPermission();
                return;
            }

            $query = "SELECT
                        id_empleado,
                        nombre,
                        TRIM(CONCAT_WS(' ', apellido, NULLIF(apellido_2, ''))) AS apellidos
                      FROM empleado
                      WHERE id_empresa = :id_empresa
                        AND activo = 1";

            $params = ['id_empresa' => $idEmpresa];

            if ($idEmpleadoTecnico !== null) {
                $query .= " AND id_empleado <> :id_empleado_actual";
                $params['id_empleado_actual'] = $idEmpleadoTecnico;
            }

            $query .= " ORDER BY nombre ASC, apellido ASC, apellido_2 ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode($empleados ? $empleados : []);
        } catch (Throwable $e) {
            error_log("[EasyParte][AVISOS_ASSIGNABLE_EMPLOYEES] " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se han podido cargar los empleados asignables."]);
        }
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
        
        // Si es Tecnico, solo ve sus avisos o los que no tienen nadie asignado
        if ($this->isTecnico($usuarioLogueado)) {
            $query .= " AND (a.id_empleado = :id_empleado OR a.id_empleado IS NULL)";
        }

        $query .= " ORDER BY a.fecha_alta DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        
        if ($this->isTecnico($usuarioLogueado)) {
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
        if (!is_object($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Datos de aviso no validos."]);
            return;
        }

        if (empty($data->descripcion) || empty($data->id_cliente)) {
            http_response_code(400);
            echo json_encode(["error" => "La descripción y el cliente son obligatorios."]);
            return;
        }

        $id_empleado = !empty($data->id_empleado) ? $data->id_empleado : null;
        $id_departamento = !empty($data->id_departamento) ? $data->id_departamento : null;

        if ($this->isTecnico($usuarioLogueado)) {
            if ($id_empleado !== null && empty($usuarioLogueado->id_empleado)) {
                $this->denyPermission();
                return;
            }

            if ($id_empleado !== null && (int)$id_empleado !== (int)$usuarioLogueado->id_empleado) {
                $this->denyPermission();
                return;
            }

            if ($id_empleado !== null) {
                $id_empleado = $usuarioLogueado->id_empleado;
            }
        }

        if (!$this->validateRelatedIds($data->id_cliente, $id_empleado, $id_departamento, $usuarioLogueado->id_empresa)) {
            $this->denyForeignRelation();
            return;
        }

        if ($id_empleado !== null && !$this->activeEmployeeBelongsToEmpresa($id_empleado, $usuarioLogueado->id_empresa)) {
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
            $this->conn->beginTransaction();
            $stmt->execute();
            $idAviso = (int)$this->conn->lastInsertId();

            $this->auditLogger->registrar(
                $usuarioLogueado->id_empresa,
                $usuarioLogueado->id_usuario,
                'aviso',
                $idAviso,
                'aviso_creado',
                null,
                [
                    'id_cliente' => (int)$data->id_cliente,
                    'id_empleado' => $id_empleado !== null ? (int)$id_empleado : null,
                    'id_departamento' => $id_departamento !== null ? (int)$id_departamento : null,
                    'importancia' => $importancia,
                    'estado' => $estado
                ]
            );

            if ($id_empleado !== null) {
                $accionAsignacion = $this->isTecnico($usuarioLogueado)
                    ? 'aviso_autoasignado'
                    : 'aviso_asignado';

                $this->auditLogger->registrar(
                    $usuarioLogueado->id_empresa,
                    $usuarioLogueado->id_usuario,
                    'aviso',
                    $idAviso,
                    $accionAsignacion,
                    ['id_empleado' => null],
                    ['id_empleado' => (int)$id_empleado]
                );
            }

            $this->conn->commit();
            http_response_code(201);
            echo json_encode(["mensaje" => "Aviso creado con éxito", "id" => $idAviso]);
        } catch (Throwable $e) {
            $this->rollbackIfActive();
            error_log("[EasyParte][AVISO_CREATE] " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido guardar el aviso."]);
        }
    }

    // ACTUALIZAR AVISOS 
    public function update($id, $data, $usuarioLogueado)
    {
        if (!is_object($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Datos de aviso no validos."]);
            return;
        }

        if (property_exists($data, 'id_empleado')) {
            http_response_code(400);
            echo json_encode(["error" => "La asignacion debe realizarse desde el endpoint especifico."]);
            return;
        }

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

        if ($this->isTecnico($usuarioLogueado)) {
            $idEmpleadoTecnico = isset($usuarioLogueado->id_empleado)
                ? (int)$usuarioLogueado->id_empleado
                : 0;
            $idEmpleadoActual = $actual['id_empleado'] !== null
                ? (int)$actual['id_empleado']
                : null;

            if ($idEmpleadoTecnico <= 0 || ($idEmpleadoActual !== null && $idEmpleadoActual !== $idEmpleadoTecnico)) {
                $this->denyPermission();
                return;
            }
        }

        // Lógica de combinación de datos 
        $descripcion = isset($data->descripcion) ? $data->descripcion : $actual['descripcion'];
        $importancia = isset($data->importancia) ? $data->importancia : $actual['importancia'];
        $estado = isset($data->estado) ? $data->estado : $actual['estado'];
        $persona_contacto = property_exists($data, 'persona_contacto') ? $data->persona_contacto : $actual['persona_contacto'];
        $telefono_contacto = property_exists($data, 'telefono_contacto') ? $data->telefono_contacto : $actual['telefono_contacto'];
        $id_empleado = $actual['id_empleado'];
        $id_cliente = isset($data->id_cliente) ? $data->id_cliente : $actual['id_cliente'];
        $id_departamento = property_exists($data, 'id_departamento') ? (!empty($data->id_departamento) ? $data->id_departamento : null) : $actual['id_departamento'];

        if (!$this->validateRelatedIds($id_cliente, $id_empleado, $id_departamento, $usuarioLogueado->id_empresa)) {
            $this->denyForeignRelation();
            return;
        }

        if (strtolower((string)$estado) === 'cancelada' && strtolower((string)$actual['estado']) !== 'cancelada') {
            $this->cancel($id, $usuarioLogueado);
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
                      id_cliente=:id_cliente, id_departamento=:id_departamento, fecha_fin=:fecha_fin
                  WHERE id_tarea = :id AND id_empresa = :id_empresa";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":importancia", $importancia);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":persona_contacto", $persona_contacto);
        $stmt->bindParam(":telefono_contacto", $telefono_contacto);
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

    // ASIGNAR O REASIGNAR UN AVISO SIN MODIFICAR OTROS CAMPOS
    public function assign($id, $data, $usuarioLogueado)
    {
        $idAviso = filter_var($id, FILTER_VALIDATE_INT);
        if ($idAviso === false || $idAviso <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El aviso indicado no es valido."]);
            return;
        }

        if (!is_object($data) || !property_exists($data, 'id_empleado')) {
            http_response_code(400);
            echo json_encode(["error" => "El empleado destino es obligatorio."]);
            return;
        }

        $idEmpleadoDestino = filter_var($data->id_empleado, FILTER_VALIDATE_INT);
        if ($idEmpleadoDestino === false || $idEmpleadoDestino <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El empleado destino no es valido."]);
            return;
        }

        if (!$this->canManageAvisos($usuarioLogueado) && !$this->isTecnico($usuarioLogueado)) {
            $this->denyPermission();
            return;
        }

        try {
            $this->conn->beginTransaction();

            $queryAviso = "SELECT id_tarea, id_empleado, estado
                           FROM " . $this->tabla . "
                           WHERE id_tarea = :id AND id_empresa = :id_empresa
                           FOR UPDATE";
            $stmtAviso = $this->conn->prepare($queryAviso);
            $stmtAviso->execute([
                'id' => $idAviso,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);
            $aviso = $stmtAviso->fetch(PDO::FETCH_ASSOC);

            if (!$aviso) {
                $this->rollbackIfActive();
                http_response_code(404);
                echo json_encode(["error" => "Aviso no encontrado."]);
                return;
            }

            if (!$this->activeEmployeeBelongsToEmpresa($idEmpleadoDestino, $usuarioLogueado->id_empresa)) {
                $this->rollbackIfActive();
                $this->denyForeignRelation();
                return;
            }

            $idEmpleadoOrigen = $aviso['id_empleado'] !== null
                ? (int)$aviso['id_empleado']
                : null;

            if ($this->isTecnico($usuarioLogueado)) {
                $idEmpleadoTecnico = isset($usuarioLogueado->id_empleado)
                    ? (int)$usuarioLogueado->id_empleado
                    : 0;

                if (
                    $idEmpleadoTecnico <= 0
                    || $idEmpleadoOrigen === null
                    || $idEmpleadoOrigen !== $idEmpleadoTecnico
                    || strtolower((string)$aviso['estado']) === 'cancelada'
                    || (int)$idEmpleadoDestino === $idEmpleadoTecnico
                ) {
                    $this->rollbackIfActive();
                    $this->denyPermission();
                    return;
                }
            }

            if ($idEmpleadoOrigen !== null && $idEmpleadoOrigen === (int)$idEmpleadoDestino) {
                $this->rollbackIfActive();
                http_response_code(400);
                echo json_encode(["error" => "El aviso ya esta asignado al empleado indicado."]);
                return;
            }

            $queryUpdate = "UPDATE " . $this->tabla . "
                            SET id_empleado = :id_empleado
                            WHERE id_tarea = :id AND id_empresa = :id_empresa";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([
                'id_empleado' => $idEmpleadoDestino,
                'id' => $idAviso,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);

            $accion = $idEmpleadoOrigen === null
                ? 'aviso_asignado'
                : 'aviso_reasignado';

            $this->auditLogger->registrar(
                $usuarioLogueado->id_empresa,
                $usuarioLogueado->id_usuario,
                'aviso',
                $idAviso,
                $accion,
                ['id_empleado' => $idEmpleadoOrigen],
                ['id_empleado' => (int)$idEmpleadoDestino]
            );

            $this->conn->commit();
            http_response_code(200);
            echo json_encode(["mensaje" => "Asignacion de aviso actualizada"]);
        } catch (Throwable $e) {
            $this->rollbackIfActive();
            error_log("[EasyParte][AVISO_ASSIGN] " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido actualizar la asignacion del aviso."]);
        }
    }

    // COGER UN AVISO LIBRE COMO TECNICO
    public function takeFree($id, $usuarioLogueado)
    {
        $idAviso = filter_var($id, FILTER_VALIDATE_INT);
        if ($idAviso === false || $idAviso <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "El aviso indicado no es valido."]);
            return;
        }

        if (!$this->isTecnico($usuarioLogueado) || empty($usuarioLogueado->id_empleado)) {
            $this->denyPermission();
            return;
        }

        $idEmpleadoTecnico = (int)$usuarioLogueado->id_empleado;

        try {
            $this->conn->beginTransaction();

            $queryAviso = "SELECT id_tarea, id_empleado, estado
                           FROM " . $this->tabla . "
                           WHERE id_tarea = :id AND id_empresa = :id_empresa
                           FOR UPDATE";
            $stmtAviso = $this->conn->prepare($queryAviso);
            $stmtAviso->execute([
                'id' => $idAviso,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);
            $aviso = $stmtAviso->fetch(PDO::FETCH_ASSOC);

            if (!$aviso) {
                $this->rollbackIfActive();
                http_response_code(404);
                echo json_encode(["error" => "Aviso no encontrado."]);
                return;
            }

            if (
                $aviso['id_empleado'] !== null
                || strtolower((string)$aviso['estado']) === 'cancelada'
                || !$this->activeEmployeeBelongsToEmpresa($idEmpleadoTecnico, $usuarioLogueado->id_empresa)
            ) {
                $this->rollbackIfActive();
                $this->denyPermission();
                return;
            }

            $queryUpdate = "UPDATE " . $this->tabla . "
                            SET id_empleado = :id_empleado
                            WHERE id_tarea = :id AND id_empresa = :id_empresa";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->execute([
                'id_empleado' => $idEmpleadoTecnico,
                'id' => $idAviso,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);

            $this->auditLogger->registrar(
                $usuarioLogueado->id_empresa,
                $usuarioLogueado->id_usuario,
                'aviso',
                $idAviso,
                'aviso_autoasignado',
                ['id_empleado' => null],
                ['id_empleado' => $idEmpleadoTecnico]
            );

            $this->conn->commit();
            http_response_code(200);
            echo json_encode(["mensaje" => "Aviso asignado al tecnico"]);
        } catch (Throwable $e) {
            $this->rollbackIfActive();
            error_log("[EasyParte][AVISO_TAKE_FREE] " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido coger el aviso."]);
        }
    }

    // CANCELAR AVISO SIN BORRADO FISICO
    public function cancel($id, $usuarioLogueado)
    {
        $query_check = "SELECT id_tarea, id_empleado FROM " . $this->tabla . " WHERE id_tarea = :id AND id_empresa = :id_empresa";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(":id", $id);
        $stmt_check->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        $stmt_check->execute();

        if ($stmt_check->rowCount() == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Aviso no encontrado."]);
            return;
        }

        $aviso = $stmt_check->fetch(PDO::FETCH_ASSOC);
        $rolesGestion = ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente'];
        $rolesTecnico = ['Tecnico', 'Técnico', 'TÃ©cnico'];

        if (!$this->userHasRole($usuarioLogueado, $rolesGestion)) {
            if (!$this->userHasRole($usuarioLogueado, $rolesTecnico)) {
                $this->denyPermission();
                return;
            }

            if (empty($aviso['id_empleado']) || (int)$aviso['id_empleado'] !== (int)$usuarioLogueado->id_empleado) {
                $this->denyPermission();
                return;
            }
        }

        $estado = 'Cancelada';
        $fecha_fin = date('Y-m-d H:i:s');

        $query = "UPDATE " . $this->tabla . "
                  SET estado = :estado, fecha_fin = :fecha_fin
                  WHERE id_tarea = :id AND id_empresa = :id_empresa";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":fecha_fin", $fecha_fin);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);

        try {
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Aviso cancelado"]);
            }
        } catch (PDOException $e) {
            error_log("Error al cancelar aviso: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(["error" => "No se ha podido cancelar el aviso."]);
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
