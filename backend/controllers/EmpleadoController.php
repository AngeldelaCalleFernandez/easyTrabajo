<?php
class EmpleadoController
{
    private $conn;
    private $tabla = "empleado";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // OBTENER TODOS LOS EMPLEADOS
    public function getAll($usuarioLogueado)
    {
        $query = "SELECT * FROM " . $this->tabla . " WHERE activo = 1 AND id_empresa = :id_empresa ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_empresa", $usuarioLogueado->id_empresa);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $empleados_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($empleados_arr, $row);
            }
            http_response_code(200);
            echo json_encode($empleados_arr);
        } else {
            http_response_code(200);
            echo json_encode([]);
        }
    }

    // CREAR UN EMPLEADO
    public function create($data, $usuarioLogueado)
    {
        if (empty($data->nombre) || empty($data->apellido)) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre y el primer apellido son obligatorios."]);
            return;
        }

        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, apellido, apellido_2, extension_tel, prefijo, movil, nif, id_departamento, id_empresa, activo) 
                  VALUES (:nombre, :apellido, :apellido_2, :extension_tel, :prefijo, :movil, :nif, :id_departamento, :id_empresa, 1)";

        $stmt = $this->conn->prepare($query);

        $apellido_2 = property_exists($data, 'apellido_2') ? $data->apellido_2 : null;
        $extension_tel = property_exists($data, 'extension_tel') ? $data->extension_tel : null;
        $prefijo = !empty($data->prefijo) ? $data->prefijo : '+34';
        $movil = property_exists($data, 'movil') ? $data->movil : null;
        $nif = !empty($data->nif) ? $data->nif : null;
        $id_departamento = !empty($data->id_departamento) ? $data->id_departamento : null;
        $id_empresa = $usuarioLogueado->id_empresa;

        $stmt->bindParam(":nombre", $data->nombre);
        $stmt->bindParam(":apellido", $data->apellido);
        $stmt->bindParam(":apellido_2", $apellido_2);
        $stmt->bindParam(":extension_tel", $extension_tel);
        $stmt->bindParam(":prefijo", $prefijo);
        $stmt->bindParam(":movil", $movil);
        $stmt->bindParam(":nif", $nif);
        $stmt->bindParam(":id_departamento", $id_departamento);
        $stmt->bindParam(":id_empresa", $id_empresa);

        try {
            if ($stmt->execute()) {
                $id_insertado = $this->conn->lastInsertId();

                $query_get = "SELECT * FROM " . $this->tabla . " WHERE id_empleado = :id AND id_empresa = :id_empresa";
                $stmt_get = $this->conn->prepare($query_get);
                $stmt_get->bindParam(":id", $id_insertado);
                $stmt_get->bindParam(":id_empresa", $id_empresa);
                $stmt_get->execute();

                http_response_code(201);
                echo json_encode([
                    "mensaje" => "Empleado creado con éxito",
                    "empleado" => $stmt_get->fetch(PDO::FETCH_ASSOC)
                ]);
            }
        } catch (PDOException $e) {
            error_log("Error al guardar empleado: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(["error" => "No se ha podido guardar el empleado."]);
        }
    }
    // ACTUALIZAR EMPLEADO
    public function update($id, $datos, $usuarioLogueado)
    {
        try {
            $query = "UPDATE empleado 
                      SET nombre = :nombre, apellido = :apellido, apellido_2 = :apellido_2, nif = :nif, movil = :movil 
                      WHERE id_empleado = :id AND id_empresa = :id_empresa";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'nombre' => $datos->nombre,
                'apellido' => $datos->apellido,
                'apellido_2' => $datos->apellido_2 ?? null,
                'nif' => !empty($datos->nif) ? $datos->nif : null,
                'movil' => $datos->movil ?? null,
                'id' => $id,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);

            http_response_code(200);
            echo json_encode(["mensaje" => "Empleado actualizado correctamente"]);
        } catch (Exception $e) {
            error_log("Error al actualizar empleado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido actualizar el empleado."]);
        }
    }
    // BORRAR EMPLEADO 
    public function delete($id, $usuarioLogueado)
    {
        try {
            
            $query = "UPDATE empleado SET activo = 0 WHERE id_empleado = :id AND id_empresa = :id_empresa";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'id' => $id,
                'id_empresa' => $usuarioLogueado->id_empresa
            ]);

            http_response_code(200);
            echo json_encode(["mensaje" => "Empleado dado de baja correctamente"]);
        } catch (Exception $e) {
            error_log("Error al dar de baja empleado: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "No se ha podido dar de baja al empleado."]);
        }
    }
}
