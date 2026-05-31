<?php
require_once __DIR__ . '/../config/headers.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../Model/m_usuario.php';

switch ($_method) {

    // metodo GET para obtener todos los usuarios o por id
    case 'GET':

        if ($_authorization === 'proviemplea.get') { 
            $modelo = new modeloUsuario();

            if (isset($_GET['id_usuario'])) {
                $modelo->setId_usuario($_GET['id_usuario']);
                $resultado = $modelo->getById($modelo);

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(['type' => 'msg', 'data' => $resultado]);
                } else {
                    http_response_code(404);
                    echo json_encode(['type' => 'error', 'msg' => 'Usuario no encontrado']);
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

    // metodo POST para crear un nuevo usuario
    case 'POST':
        if ($_authorization === 'proviemplea.post') { // <-- TOKEN LIMPIO
            $body = json_decode(file_get_contents("php://input"), true);

            if (
                empty($body['rut']) ||
                empty($body['correo']) ||
                empty($body['contrasena']) ||
                empty($body['nombre']) ||
                empty($body['apellido_paterno']) ||
                empty($body['comuna']) ||
                empty($body['id_rol'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break; 
            }

            $modelo = new modeloUsuario();
            $modelo->setRut($body['rut']);
            $modelo->setCorreo($body['correo']);
            $modelo->setContrasena($body['contrasena']);
            $modelo->setId_rol($body['id_rol']);
            $modelo->setNombre($body['nombre']);
            $modelo->setApellido_paterno($body['apellido_paterno']);
            $modelo->setApellido_materno($body['apellido_materno'] ?? '');
            $modelo->setTelefono($body['telefono'] ?? '');
            $modelo->setComuna($body['comuna']);

            $respuesta = $modelo->add($modelo);

            if ($respuesta) {
                http_response_code(201);
                echo json_encode(['type' => 'msg', 'msg' => 'Usuario creado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo crear el usuario']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PUT para actualizar datos personales del usuario
    case 'PUT':
        if ($_authorization === 'proviemplea.put') { // <-- TOKEN LIMPIO
            $body = json_decode(file_get_contents("php://input"), true);

            if (
                empty($body['id_usuario']) ||
                empty($body['nombre']) ||
                empty($body['apellido_paterno']) ||
                empty($body['comuna'])
            ) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloUsuario();
            $modelo->setId_usuario($body['id_usuario']);
            $modelo->setNombre($body['nombre']);
            $modelo->setApellido_paterno($body['apellido_paterno']);
            $modelo->setApellido_materno($body['apellido_materno'] ?? '');
            $modelo->setTelefono($body['telefono'] ?? '');
            $modelo->setComuna($body['comuna']);

            $respuesta = $modelo->update($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Usuario actualizado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo actualizar el usuario']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // metodo PATCH para cambiar el estado del usuario
    case 'PATCH':
        if ($_authorization === 'proviemplea.patch') { // <-- TOKEN LIMPIO
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['id_usuario']) || empty($body['id_estado'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Faltan campos requeridos']);
                break;
            }

            $modelo = new modeloUsuario();
            $modelo->setId_usuario($body['id_usuario']);
            $modelo->setId_estado($body['id_estado']);

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

    // metodo DELETE para eliminar usuario
    case 'DELETE':
        if ($_authorization === 'proviemplea.delete') { // <-- TOKEN LIMPIO
            $body = json_decode(file_get_contents("php://input"), true);

            if (empty($body['id_usuario'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'msg' => 'Falta el id_usuario']);
                break;
            }

            $modelo = new modeloUsuario();
            $modelo->setId_usuario($body['id_usuario']);

            $respuesta = $modelo->delete($modelo);

            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['type' => 'msg', 'msg' => 'Usuario eliminado correctamente']);
            } else {
                http_response_code(422);
                echo json_encode(['type' => 'error', 'msg' => 'No se pudo eliminar el usuario']);
            }
        } else {
            http_response_code(403);
            echo json_encode(['type' => 'error', 'msg' => 'Acceso Prohibido']);
        }
        break;

    // un DEFAULT en caso de que el metodo no exista
    default:
        http_response_code(501);
        echo json_encode(['type' => 'error', 'msg' => 'Metodo no existe']);
        break;
}
?>