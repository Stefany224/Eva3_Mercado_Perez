<?php

require_once __DIR__ . '/../config/conexion.php';

class modeloUsuario {
    
    private $id_usuario;
    private $correo;
    private $contrasena;
    private $id_rol;
    private $id_estado;
    private $rut;
    private $nombre;
    private $apellido_paterno;
    private $apellido_materno;
    private $telefono;
    private $comuna;

    public function __construct() {}

    // GETTERS Y SETTERS
    public function getId_usuario() { return $this->id_usuario; }
    public function setId_usuario($value) { $this->id_usuario = $value; }

    public function getCorreo() { return $this->correo; }
    public function setCorreo($value) { $this->correo = $value; }

    public function getContrasena() { return $this->contrasena; }
    public function setContrasena($value) { $this->contrasena = $value; }

    public function getId_rol() { return $this->id_rol; }
    public function setId_rol($value) { $this->id_rol = $value; }

    public function getId_estado() { return $this->id_estado; }
    public function setId_estado($value) { $this->id_estado = $value; }

    public function getRut() { return $this->rut; }
    public function setRut($value) { $this->rut = $value; }

    public function getNombre() { return $this->nombre; }
    public function setNombre($value) { $this->nombre = $value; }

    public function getApellido_paterno() { return $this->apellido_paterno; }
    public function setApellido_paterno($value) { $this->apellido_paterno = $value; }

    public function getApellido_materno() { return $this->apellido_materno; }
    public function setApellido_materno($value) { $this->apellido_materno = $value; }

    public function getTelefono() { return $this->telefono; }
    public function setTelefono($value) { $this->telefono = $value; }

    public function getComuna() { return $this->comuna; }
    public function setComuna($value) { $this->comuna = $value; }

    // funcion GET ALL para obtener todos los usuarios
    public function getAll() {
        $lista = [];
        $con = new conexion();
        $query = "SELECT 
                    l.id_usuario, l.correo, l.id_rol, l.id_estado, l.fecha_registro,
                    u.rut, u.nombre, u.apellido_paterno, u.apellido_materno, 
                    u.telefono, u.comuna
                  FROM login_usuario l
                  INNER JOIN usuario u ON l.id_usuario = u.id_usuario";

        $rs = mysqli_query($con->getConection(), $query);

        if ($rs) {
            while ($registro = mysqli_fetch_assoc($rs)) {
                array_push($lista, $registro);
            }
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $lista;
    }

    // funcion GET BY ID para obtener el usuario por id
    public function getById(modeloUsuario $_usuario) {
        $con = new conexion();
        $query = "SELECT 
                    l.id_usuario, l.correo, l.id_rol, l.id_estado, l.fecha_registro,
                    u.rut, u.nombre, u.apellido_paterno, u.apellido_materno,
                    u.telefono, u.comuna
                  FROM login_usuario l
                  INNER JOIN usuario u ON l.id_usuario = u.id_usuario
                  WHERE l.id_usuario = " . (int)$_usuario->getId_usuario();

        $rs = mysqli_query($con->getConection(), $query);
        $resultado = null;

        if ($rs && mysqli_num_rows($rs) > 0) {
            $resultado = mysqli_fetch_assoc($rs);
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $resultado;
    }

    // funcion ADD para crear un nuevo usuario 
    public function add(modeloUsuario $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $hash = password_hash($_nuevo->getContrasena(), PASSWORD_BCRYPT);

        // Insercion en login_usuario
        $query1 = "INSERT INTO login_usuario (correo, contrasena, id_rol, id_estado) 
                   VALUES (
                       '" . mysqli_real_escape_string($conn, $_nuevo->getCorreo()) . "',
                       '" . $hash . "',
                       " . (int)$_nuevo->getId_rol() . ",  3 )";

        $rs1 = mysqli_query($conn, $query1);

        if (!$rs1) {
            $con->closeConnection();
            return false;
        }

        $id_nuevo = mysqli_insert_id($conn);

        // Insercion en usuario (Se usa mysqli_real_escape_string para el RUT si es que viene formateado como string, o se limpia)
        $query2 = "INSERT INTO usuario (rut, id_usuario, nombre, apellido_paterno, apellido_materno, telefono, comuna)
                   VALUES (
                       '" . mysqli_real_escape_string($conn, $_nuevo->getRut()) . "',
                       " . (int)$id_nuevo . ",
                       '" . mysqli_real_escape_string($conn, $_nuevo->getNombre()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getApellido_paterno()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getApellido_materno()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getTelefono()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getComuna()) . "')";

        $rs2 = mysqli_query($conn, $query2);
        $con->closeConnection();

        return $rs2 ? true : false;
    }

    // funcion UPDATE para actualizar datos personales
    public function update(modeloUsuario $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE usuario SET
                    nombre = '" . mysqli_real_escape_string($conn, $_nuevo->getNombre()) . "',
                    apellido_paterno = '" . mysqli_real_escape_string($conn, $_nuevo->getApellido_paterno()) . "',
                    apellido_materno = '" . mysqli_real_escape_string($conn, $_nuevo->getApellido_materno()) . "',
                    telefono = '" . mysqli_real_escape_string($conn, $_nuevo->getTelefono()) . "',
                    comuna = '" . mysqli_real_escape_string($conn, $_nuevo->getComuna()) . "'
                  WHERE id_usuario = " . (int)$_nuevo->getId_usuario();

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion PATCH para actualizar solo el estado del usuario
    public function patch(modeloUsuario $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE login_usuario SET
                    id_estado = " . (int)$_nuevo->getId_estado() . "
                  WHERE id_usuario = " . (int)$_nuevo->getId_usuario();

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion DELETE para eliminar usuario
    public function delete(modeloUsuario $_nuevo) {
        $con = new conexion();
        $query = "DELETE FROM login_usuario 
                  WHERE id_usuario = " . (int)$_nuevo->getId_usuario();

        $rs = mysqli_query($con->getConection(), $query);
        $con->closeConnection();

        return $rs ? true : false;
    }
}
?>