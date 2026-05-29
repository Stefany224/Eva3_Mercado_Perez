<?php
$_uri = $_SERVER['REQUEST_URI']; 
$_carpetas = explode('/', $_uri); 

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE'); 
header('Content-Type: application/json; charset=UTF-8'); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Cache-Control: public, max-age=3600'); 
} else {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
}

header('X-RateLimit-Limit: 60');        // Indica el máximo de peticiones permitidas por minuto
header('X-RateLimit-Remaining: 59');   // Indica cuántas peticiones le quedan disponibles al cliente

// Inicializa la variable de autorización en vacio
$_authorization = null;

try {
    if (isset(getallheaders()['Authorization'])) { 
        $_authorization = getallheaders()['Authorization']; 
    } else {
        http_response_code(401); 
        echo json_encode(['error' => 'Sin Autorizacion']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error sin control: ' . $e->getMessage()]);
    exit;
}
?>