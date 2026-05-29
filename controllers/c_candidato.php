<?php
include_once 'headers.php';
include '../Model/m_candidato.php';

$objCandidato = new ModeloCandidato();
$method = $_SERVER['REQUEST_METHOD']; 

switch ($method) {
    // CASO GET: Consultar los datos de perfil y avance del postulante
    case 'GET':
        if ($_authorization === 'Bearer candidato.get' || $_authorization === 'candidato.get') {
            $rut = isset($_GET['rut']) ? intval($_GET['rut']) : 0;
            
            if ($rut > 0) {
                $res = $objCandidato->ObtenerPerfilCandidato($rut);
                if ($res) {
                    http_response_code(200);
                    echo json_encode($res);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Candidato no encontrado"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Falta el parametro RUT o es invalido"]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Sin autorizacion para consultar perfiles"]);
        }
        break;

    // CASO POST: Para agregar sub-elementos al Cv 
    case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        
        // Evaluacion de que accion viene en el cuerpo del JSON para reutilizar el caso POST 
        $accion = isset($data->accion) ? $data->accion : '';

        if ($_authorization === 'Bearer candidato.post' || $_authorization === 'candidato.post') {

            // condicional para agg un registro de trayectoria laboral
            if ($accion === 'experiencia') {
                if (!empty($data->id_cv) && !empty($data->empresa_anonima) && !empty($data->cargo) && !empty($data->meses_experiencia)) {
                    
                    $res = $objCandidato->AgregarExperiencia($data->id_cv, $data->empresa_anonima, $data->cargo, $data->meses_experiencia, $data->descripcion_funciones ?? '');
                    
                    if ($res) {
                        http_response_code(201);
                        echo json_encode(["message" => "Experiencia laboral añadida y % de avance actualizado"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error interno al guardar la experiencia"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Faltan campos requeridos para la experiencia"]);
                }

            // Agregamos habilidad o competencia técnica
            } elseif ($accion === 'competencia') {
                if (!empty($data->id_cv) && !empty($data->nombre_competencia) && !empty($data->nivel)) {
                    
                    $res = $objCandidato->AgregarCompetencia($data->id_cv, $data->nombre_competencia, $data->nivel);
                    
                    if ($res) {
                        http_response_code(201);
                        echo json_encode(["message" => "Competencia tecnica registrada"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Error interno al guardar la competencia"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Faltan campos requeridos para la competencia"]);
                }

            } else {
                http_response_code(400);
                echo json_encode(["message" => "Acción no especificada/valida en el JSON (Elija: experiencia o competencia)"]);
            }

        } else {
            http_response_code(401);
            echo json_encode(["error" => "Token de autorizacion invalido"]);
        }
        break;
}
?>