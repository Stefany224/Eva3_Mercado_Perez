<?php

$_method = $_SERVER['REQUEST_METHOD'];
$_uri = $_SERVER['REQUEST_URI'];
$_carpetas = explode('/', $_uri);

// Headers de acceso
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// Manejo de preflight OPTIONS 
if ($_method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Captura del token 
$_authorization = null;

try {
    // Intento 1: Apache con getallheaders()
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $_authorization = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $_authorization = $headers['authorization'];
        }
    }

    if (empty($_authorization) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $_authorization = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (empty($_authorization) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $_authorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (empty($_authorization)) {
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