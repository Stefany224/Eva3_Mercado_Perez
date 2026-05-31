<?php

$_method = $_SERVER['REQUEST_METHOD'];
$_uri = $_SERVER['REQUEST_URI'];
$_carpetas = explode('/', $_uri);

// Headers de acceso
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// Manejo de preflight OPTIONS (Swagger lo necesita)
if ($_method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Capturamos el token de autorizacion
$_authorization = null;
try {
    if (isset(getallheaders()['Authorization'])) {
        $_authorization = getallheaders()['Authorization'];
    } else {
        http_response_code(401);
        echo json_encode(['type' => 'error', 'msg' => 'Sin Autorizacion']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['type' => 'error', 'msg' => 'Error interno: ' . $e->getMessage()]);
    exit;
}
?>