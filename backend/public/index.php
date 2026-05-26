<?php
//  Usamos __DIR__ para dar la ruta absoluta exacta y evitar fallos
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/cors.php';
//  Cabecera JSON
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
//  Requerimos la base de datos con ruta absoluta

require_once __DIR__ . '/../config/database.php';
// Instanciamos 
$db = new Database();
$conexion = $db->getConnection();
//  Capturamos la URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];
//  Cargamos las rutas
require_once __DIR__ . '/../routes/api.php';
