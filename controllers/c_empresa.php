<?php
include_once 'headers.php';
include '../Model/m_empresa.php'; 

$objOferta = new ModelEmpresa();
$method = $_SERVER['REQUEST_METHOD']; 

switch ($method) {

    // Busqueda inversa de candidatos usando filtros
    case 'GET':
        // Validacion de token 
        if ($_authorization === 'Bearer reclutador.get' || $_authorization === 'reclutador.get') {
            
            // Capturamos los filtros opcionales desde la URL en Swagger
            $comuna = isset($_GET['comuna']) ? trim($_GET['comuna']) : '';
            $nivel = isset($_GET['nivel_educacional']) ? trim($_GET['nivel_educacional']) : '';
            $carrera = isset($_GET['carrera']) ? trim($_GET['carrera']) : '';

            // Llamamos al modelo para ejecutar la consulta con filtros
            $res = $objOferta->BuscarPostulantesCiegos($comuna, $nivel, $carrera);

            // Respuesta exitosa con el JSON anonimizado
            http_response_code(200);
            echo json_encode($res);

        } else {
            http_response_code(401);
            echo json_encode(["error" => "Token invalido o sin autorizacion"]);
        }
        break;

    default:
        // POST, PUT o DELETE para el controlador corporativo, avisamos que ya no se permite publicar ofertas
        http_response_code(405);
        echo json_encode(["message" => "Metodo no permitido"]);
        break;
}
?>