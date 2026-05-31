<?php

require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../Model/m_empresa.php';

switch ($_method) {

    // metodo GET para obtener todas las empresas / una por rut
    case 'GET':
        if ($_authorization === 'proviemplea.get') {
            $modelo = new modeloEmpresa();

            if (isset($_GET['rut_empresa'])) {
                $modelo->setRut_empresa($_GET['rut_empresa']);
                $resultado = $modelo->getByRut($modelo);

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'data' => $resultado]);
                } else {
                    http_response_code(404);
                    echo json_encode(['type' => 'error', 'msg' => 'Empresa no encontrada']);
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

    // metodo POST para crear nueva empresa
    case 'POST':
        if ($_authorization === 'proviemplea.post') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Validacion de campos requeridos
            if (
                empty($body['rut_empresa']) ||
                empty($body['correo']) ||
                empty($body['nombre_empresa']) ||
                empty($body['rubro']) ||
                empty($body['tipo_empresa'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloEmpresa();
            $modelo->setRut_empresa($body['rut_empresa']);
            $modelo->setCorreo($body['correo']);
            $modelo->setNombre_empresa($body['nombre_empresa']);
            $modelo->setRubro($body['rubro']);
            $modelo->setTipo_empresa($body['tipo_empresa']);
            $modelo->setPresentacion($body['presentacion'] ?? '');
            $modelo->setBeneficios($body['beneficios'] ?? '');
            $modelo->setLogo($body['logo'] ?? '');
            $modelo->setNombre_contacto($body['nombre_contacto'] ?? '');
            $modelo->setTelefono_contacto($body['telefono_contacto'] ?? '');

            $respuesta = $modelo->add($modelo);

            if ($respuesta) {
                http_response_code(201);
                echo json_encode(['type' => 'msg', 'msg' => 'Empresa creada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo crear la empresa']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PUT para actualizar datos completos de empresa
    case 'PUT':
        if ($_authorization === 'proviemplea.put') {
            $body = json_decode(file_get_contents("php://input"), true);

            // Validacion de campos requeridos
            if (
                empty($body['rut_empresa']) ||
                empty($body['nombre_empresa']) ||
                empty($body['rubro']) ||
                empty($body['tipo_empresa'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloEmpresa();
            $modelo->setRut_empresa($body['rut_empresa']);
            $modelo->setNombre_empresa($body['nombre_empresa']);
            $modelo->setRubro($body['rubro']);
            $modelo->setTipo_empresa($body['tipo_empresa']);
            $modelo->setPresentacion($body['presentacion'] ?? '');
            $modelo->setBeneficios($body['beneficios'] ?? '');
            $modelo->setLogo($body['logo'] ?? '');
            $modelo->setNombre_contacto($body['nombre_contacto'] ?? '');
            $modelo->setTelefono_contacto($body['telefono_contacto'] ?? '');

            $respuesta = $modelo->update($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Empresa actualizada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar la empresa']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PATCH para actualizar solo presentacion
    case 'PATCH':
        if ($_authorization === 'proviemplea.patch') {
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['rut_empresa']) || empty($body['presentacion'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloEmpresa();
            $modelo->setRut_empresa($body['rut_empresa']);
            $modelo->setPresentacion($body['presentacion']);

            $respuesta = $modelo->patch($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Presentacion actualizada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar la presentacion']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo DELETE para eliminar empresas
    case 'DELETE':
        if ($_authorization === 'proviemplea.delete') {
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['rut_empresa'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Falta el rut_empresa']);
                break;
            }

            $modelo = new modeloEmpresa();
            $modelo->setRut_empresa($body['rut_empresa']);

            $respuesta = $modelo->delete($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Empresa eliminada correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo eliminar la empresa']);
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