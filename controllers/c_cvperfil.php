<?php
require_once __DIR__ . '/../config/headers.php';
require_once '/../config/conexion.php';
require_once '/../Model/m_cvperfil.php';

switch ($_method) {

    // metodo GET para obtener CVs
    case 'GET':
        if ($_authorization === 'Bearer proviemplea.get') {
            $modelo = new modeloCvPerfil();

            if (isset($_GET['id_cv'])) {
                // CV completo por id (admin)
                $modelo->setId_cv($_GET['id_cv']);
                $resultado = $modelo->getById($modelo);

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'data' => $resultado]);
                } else {
                    http_response_code(404);
                    echo json_encode(['type' => 'error', 'msg' => 'CV no encontrado']);
                }

            } elseif (isset($_GET['ciego'])) {
                // CV ciego por id (empresa)
                $modelo->setId_cv($_GET['ciego']);
                $resultado = $modelo->getCvCiego($modelo);

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'data' => $resultado]);
                } else {
                    http_response_code(404);
                    echo json_encode(['type' => 'error', 'msg' => 'CV no encontrado']);
                }

            } else {
                // Todos los CVs en vista ciega
                $lista = $modelo->getAll();
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'data' => $lista]);
            }

        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo POST para crear un nuevo CV
    case 'POST':
        if ($_authorization === 'Bearer proviemplea.post') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Validacion de campos requeridos
            if (
                empty($body['rut_persona']) ||
                empty($body['nivel_educacional']) ||
                empty($body['carrera'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloCvPerfil();
            $modelo->setRut_persona($body['rut_persona']);
            $modelo->setResumen_laboral($body['resumen_laboral'] ?? '');
            $modelo->setNivel_educacional($body['nivel_educacional']);
            $modelo->setCarrera($body['carrera']);
            $modelo->setRenta_deseada($body['renta_deseada'] ?? 0);
            $modelo->setJornada_deseada($body['jornada_deseada'] ?? '');
            $modelo->setModalidad_deseada($body['modalidad_deseada'] ?? '');

            $respuesta = $modelo->add($modelo);

            if ($respuesta) {
                http_response_code(201);
                echo json_encode(['type' => 'msg', 'msg' => 'CV creado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo crear el CV']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PUT para actualizar CV completo
    case 'PUT':
        if ($_authorization === 'Bearer proviemplea.put') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Validacion de campos requeridos
            if (
                empty($body['id_cv']) ||
                empty($body['nivel_educacional']) ||
                empty($body['carrera'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloCvPerfil();
            $modelo->setId_cv($body['id_cv']);
            $modelo->setResumen_laboral($body['resumen_laboral'] ?? '');
            $modelo->setNivel_educacional($body['nivel_educacional']);
            $modelo->setCarrera($body['carrera']);
            $modelo->setRenta_deseada($body['renta_deseada'] ?? 0);
            $modelo->setJornada_deseada($body['jornada_deseada'] ?? '');
            $modelo->setModalidad_deseada($body['modalidad_deseada'] ?? '');

            $respuesta = $modelo->update($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'CV actualizado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar el CV']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PATCH para actualizar solo resumen laboral
    case 'PATCH':
        if ($_authorization === 'Bearer proviemplea.patch') {
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['id_cv']) || empty($body['resumen_laboral'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloCvPerfil();
            $modelo->setId_cv($body['id_cv']);
            $modelo->setResumen_laboral($body['resumen_laboral']);

            $respuesta = $modelo->patch($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Resumen actualizado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar el resumen']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo DELETE para eliminar CV
    case 'DELETE':
        if ($_authorization === 'Bearer proviemplea.delete') {
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['id_cv'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Falta el id_cv']);
                break;
            }

            $modelo = new modeloCvPerfil();
            $modelo->setId_cv($body['id_cv']);

            $respuesta = $modelo->delete($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'CV eliminado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo eliminar el CV']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // un DEFAULT en caso de que el metodo no exista
    default:
        http_response_code(501);
        echo json_encode(['type' => 'error', 'msg' => 'Metodo no implementado']);
        break;
}
?>