<?php
// Requerimos el middleware de autenticación
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/response.php';

function easyparte_user_has_role($usuarioLogueado, array $rolesPermitidos)
{
    $rol = isset($usuarioLogueado->rol_nombre) ? $usuarioLogueado->rol_nombre : '';
    return in_array($rol, $rolesPermitidos, true);
}

function easyparte_normalize_role($rol)
{
    $rol = strtolower(trim((string)$rol));
    $rol = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'ÃƒÂ¡', 'ÃƒÂ©', 'ÃƒÂ­', 'ÃƒÂ³', 'ÃƒÂº'],
        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
        $rol
    );
    return preg_replace('/\s+/', ' ', $rol);
}

function easyparte_user_has_normalized_role($usuarioLogueado, array $rolesPermitidos)
{
    $rol = easyparte_normalize_role(isset($usuarioLogueado->rol_nombre) ? $usuarioLogueado->rol_nombre : '');
    $roles = array_map('easyparte_normalize_role', $rolesPermitidos);
    return in_array($rol, $roles, true);
}

function easyparte_forbid()
{
    Response::error("No tienes permisos para realizar esta accion.", 403);
}

$partes_ruta = explode('/', $uri);
$indice_api = array_search('api', $partes_ruta);

if ($indice_api !== false && isset($partes_ruta[$indice_api + 1])) {
    $endpoint = $partes_ruta[$indice_api + 1];

    switch ($endpoint) {
        case 'login':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $datos = json_decode(file_get_contents("php://input"));
            $auth = new AuthController($conexion);
            $auth->login($datos);
            break;

        case 'clientes':
            $usuarioLogueado = AuthMiddleware::checkToken();

            require_once __DIR__ . '/../controllers/ClienteController.php';
            $clienteController = new ClienteController($conexion);
            $id = isset($partes_ruta[$indice_api + 2]) ? $partes_ruta[$indice_api + 2] : null;
            $rolesLecturaCliente = ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente', 'Tecnico', 'Técnico', 'TÃ©cnico'];
            $rolesEscrituraCliente = ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente'];
            $rolesBajaCliente = ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente'];

            if ($metodo === 'GET') {
                if (!easyparte_user_has_role($usuarioLogueado, $rolesLecturaCliente)) {
                    easyparte_forbid();
                }
                $clienteController->getAll($usuarioLogueado);
            } elseif ($metodo === 'POST') {
                if (!easyparte_user_has_role($usuarioLogueado, $rolesEscrituraCliente)) {
                    easyparte_forbid();
                }
                $datos = json_decode(file_get_contents("php://input"));
                $clienteController->create($datos, $usuarioLogueado);
            } elseif ($metodo === 'PUT' && $id) {
                if (!easyparte_user_has_role($usuarioLogueado, $rolesEscrituraCliente)) {
                    easyparte_forbid();
                }
                $datos = json_decode(file_get_contents("php://input"));
                $clienteController->update($id, $datos, $usuarioLogueado);
            } elseif ($metodo === 'DELETE' && $id) {
                if (!easyparte_user_has_role($usuarioLogueado, $rolesBajaCliente)) {
                    easyparte_forbid();
                }
                $clienteController->delete($id, $usuarioLogueado);
            }
            break;
            
        case 'dashboard':
            $usuarioLogueado = AuthMiddleware::checkToken();
            require_once __DIR__ . '/../controllers/DashboardController.php';
            $dashboardController = new DashboardController($conexion);

            if ($metodo === 'GET') {
                $dashboardController->getResumen($usuarioLogueado);
            }
            break;

        case 'avisos':
            $usuarioLogueado = AuthMiddleware::checkToken();

            require_once __DIR__ . '/../controllers/AvisoController.php';
            $avisoController = new AvisoController($conexion);
            $id = isset($partes_ruta[$indice_api + 2]) ? $partes_ruta[$indice_api + 2] : null;
            $accion = isset($partes_ruta[$indice_api + 3]) ? $partes_ruta[$indice_api + 3] : null;
            $rolesCrearAviso = ['Administrador', 'Atencion al Cliente', 'Atención al Cliente', 'AtenciÃ³n al Cliente', 'Tecnico', 'Técnico', 'TÃ©cnico'];

            if ($metodo === 'GET') {

                $avisoController->getAll($usuarioLogueado);
            } elseif ($metodo === 'POST') {
                if (!easyparte_user_has_role($usuarioLogueado, $rolesCrearAviso)) {
                    easyparte_forbid();
                }
                $datos = json_decode(file_get_contents("php://input"));
                $avisoController->create($datos, $usuarioLogueado);
            } elseif ($metodo === 'PUT' && $id && $accion === 'cancelar') {
                $avisoController->cancel($id, $usuarioLogueado);
            } elseif ($metodo === 'PUT' && $id) {
                $datos = json_decode(file_get_contents("php://input"));
                $avisoController->update($id, $datos, $usuarioLogueado);
            } elseif ($metodo === 'DELETE' && $id) {
                easyparte_forbid();
            }
            break;

        case 'partes':
            $usuarioLogueado = AuthMiddleware::checkToken();
            require_once __DIR__ . '/../controllers/ParteTrabajoController.php';
            $parteController = new ParteTrabajoController($conexion);
            $id = isset($partes_ruta[$indice_api + 2]) ? $partes_ruta[$indice_api + 2] : null;
            $rolesEscrituraPartes = ['Administrador', 'Tecnico'];

            if ($metodo === 'GET') {
                $parteController->getAll($usuarioLogueado);
            } elseif ($metodo === 'POST') {
                if (!easyparte_user_has_normalized_role($usuarioLogueado, $rolesEscrituraPartes)) {
                    easyparte_forbid();
                }
                $datos = json_decode(file_get_contents("php://input"));
                $parteController->create($datos, $usuarioLogueado);
            } elseif ($metodo === 'PUT' && $id) {  
                if (!easyparte_user_has_normalized_role($usuarioLogueado, $rolesEscrituraPartes)) {
                    easyparte_forbid();
                }
                $datos = json_decode(file_get_contents("php://input"));
                $parteController->update($id, $datos, $usuarioLogueado);
            }
            break;

        //Mismo bloque
        case 'empleados':
        case 'usuarios':
        case 'roles':
            $usuarioLogueado = AuthMiddleware::checkToken();

            // Sacamos el ID aquí para que esté disponible en empleados y usuarios
            $id = isset($partes_ruta[$indice_api + 2]) ? $partes_ruta[$indice_api + 2] : null;

            if ($endpoint === 'empleados') {
                require_once __DIR__ . '/../controllers/EmpleadoController.php';
                $controller = new EmpleadoController($conexion);
                $rolesLecturaEmpleados = ['Administrador', 'Atencion al Cliente'];
                if ($metodo === 'GET') {
                    if (!easyparte_user_has_normalized_role($usuarioLogueado, $rolesLecturaEmpleados)) {
                        easyparte_forbid();
                    }
                    $controller->getAll($usuarioLogueado);
                }
                elseif ($metodo === 'POST') {
                    if ($usuarioLogueado->rol_nombre !== 'Administrador') {
                        easyparte_forbid();
                    }
                    $datos = json_decode(file_get_contents("php://input"));
                    $controller->create($datos, $usuarioLogueado);
                } elseif ($metodo === 'PUT' && $id) {
                    if ($usuarioLogueado->rol_nombre !== 'Administrador') {
                        easyparte_forbid();
                    }
                    $datos = json_decode(file_get_contents("php://input"));
                    $controller->update($id, $datos, $usuarioLogueado);
                } elseif ($metodo === 'DELETE' && $id) {
                    if ($usuarioLogueado->rol_nombre !== 'Administrador') {
                        easyparte_forbid();
                    }
                    $controller->delete($id, $usuarioLogueado);
                }
            } elseif ($endpoint === 'usuarios') {
                if ($usuarioLogueado->rol_nombre !== 'Administrador') {
                    easyparte_forbid();
                }
                require_once __DIR__ . '/../controllers/UsuarioController.php';
                $controller = new UsuarioController($conexion);
                if ($metodo === 'GET') $controller->getAll($usuarioLogueado);
                elseif ($metodo === 'POST') {
                    $datos = json_decode(file_get_contents("php://input"));
                    $controller->create($datos, $usuarioLogueado);
                } elseif ($metodo === 'PUT' && $id) {
                    $datos = json_decode(file_get_contents("php://input"));
                    $controller->update($id, $datos, $usuarioLogueado);
                } elseif ($metodo === 'DELETE' && $id) {
                    $controller->delete($id, $usuarioLogueado);
                }
            } elseif ($endpoint === 'roles') {
                if ($usuarioLogueado->rol_nombre !== 'Administrador') {
                    easyparte_forbid();
                }
                require_once __DIR__ . '/../controllers/RolController.php';
                $controller = new RolController($conexion);
                if ($metodo === 'GET') $controller->getAll();
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "El endpoint '/$endpoint' no existe."]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "No se ha especificado un endpoint válido en la API."]);
}
