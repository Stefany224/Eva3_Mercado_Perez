<?php

require_once __DIR__ . '/../config/headers.php';
require_once  '/../config/conexion.php';
require_once  '/../Model/modeloSolicitud.php';

switch ($_method) {

    // metodo GET para obtener solicitudes
    // ?id_solicitud=X → una solicitud con sus notas
    // sin parametros  → todas las solicitudes
    case 'GET':
        if ($_authorization === 'Bearer proviemplea.get') {
            $modelo = new modeloSolicitud();

            if (isset($_GET['id_solicitud'])) {
                $modelo->setId_solicitud($_GET['id_solicitud']);
                $resultado = $modelo->getById($modelo);

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'data' => $resultado]);
                } else {
                    http_response_code(404);
                    echo json_encode(['type' => 'error', 'msg' => 'Solicitud no encontrada']);
                }
            } else {
                $lista = $modelo->getAll();
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'data' => $lista]);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo POST para crear solicitud o agregar nota
    // ?nota=1 → agrega nota a solicitud existente
    // sin parametro → crea nueva solicitud
    case 'POST':
        if ($_authorization === 'Bearer proviemplea.post') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Agregar nota a solicitud existente
            if (isset($_GET['nota'])) {
                if (
                    empty($body['id_solicitud']) ||
                    empty($body['autor_rol']) ||
                    empty($body['comentario'])
                ) {
                    http_response_code(400);
                    echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                    break;
                }

                $modelo = new modeloSolicitud();
                $modelo->setId_solicitud($body['id_solicitud']);
                $modelo->setAutor_rol($body['autor_rol']);
                $modelo->setComentario($body['comentario']);

                $respuesta = $modelo->addNota($modelo);

                if ($respuesta) {
                    http_response_code(201);
                    echo json_encode(['type' => 'msg', 'msg' => 'Nota agregada correctamente']);
                } else {
                    http_response_code(422);
                    echo json_encode(['type' => 'error', 'msg' => 'No se pudo agregar la nota']);
                }
                break;
            }

            // Crear nueva solicitud de contacto
            if (
                empty($body['rut_empresa']) ||
                empty($body['id_cv'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloSolicitud();
            $modelo->setRut_empresa($body['rut_empresa']);
            $modelo->setId_cv($body['id_cv']);

            $respuesta = $modelo->add($modelo);

            if ($respuesta) {
                http_response_code(201);
                echo json_encode(['type' => 'msg', 'msg' => 'Solicitud creada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo crear la solicitud']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PATCH para actualizar estado del proceso
    // Estados validos:
    // 4=Contactado, 5=Entrevista,
    // 6=Seleccionado, 7=No seleccionado
    case 'PATCH':
        if ($_authorization === 'Bearer proviemplea.patch') {
            $body = json_decode(file_get_contents("php://input"), true);

            if (
                empty($body['id_solicitud']) ||
                empty($body['id_estado_proceso'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            // Validar que el estado sea valido para procesos (4 al 7)
            $estados_validos = [4, 5, 6, 7];
            if (!in_array($body['id_estado_proceso'], $estados_validos)) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Estado de proceso no valido. Use: 4=Contactado, 5=Entrevista, 6=Seleccionado, 7=No seleccionado']);
                break;
            }

            $modelo = new modeloSolicitud();
            $modelo->setId_solicitud($body['id_solicitud']);
            $modelo->setId_estado_proceso($body['id_estado_proceso']);

            $respuesta = $modelo->patch($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Estado actualizado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar el estado']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo DELETE para eliminar solicitud o nota
    // ?nota=1 → elimina nota por id_nota
    // sin parametro → elimina solicitud completa
    case 'DELETE':
        if ($_authorization === 'Bearer proviemplea.delete') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Eliminar nota especifica
            if (isset($_GET['nota'])) {
                if (empty($body['id_nota'])) {
                    http_response_code(400);
                    echo json_encode(['type' => 'error', 'msg' => 'Falta el id_nota']);
                    break;
                }

                $modelo = new modeloSolicitud();
                $modelo->setId_nota($body['id_nota']);

                $respuesta = $modelo->deleteNota($modelo);

                if ($respuesta) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'msg' => 'Nota eliminada correctamente']);
                } else {
                    http_response_code(422);
                    echo json_encode(['type' => 'error', 'msg' => 'No se pudo eliminar la nota']);
                }
                break;
            }

            // Eliminar solicitud completa
            if (empty($body['id_solicitud'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Falta el id_solicitud']);
                break;
            }

            $modelo = new modeloSolicitud();
            $modelo->setId_solicitud($body['id_solicitud']);

            $respuesta = $modelo->delete($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Solicitud eliminada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo eliminar la solicitud']);
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