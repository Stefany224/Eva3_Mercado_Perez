<?php

require_once __DIR__ . '/../config/conexion.php';

class modeloEmpresa {
 
    private $rut_empresa;
    private $correo;
    private $id_usuario;
    private $nombre_empresa;
    private $rubro;
    private $tipo_empresa;
    private $presentacion;
    private $beneficios;
    private $logo;
    private $nombre_contacto;
    private $telefono_contacto;

    public function __construct() {}

    // GETTERS Y SETTERS
    public function getRut_empresa() { return $this->rut_empresa; }
    public function setRut_empresa($value) { $this->rut_empresa = $value; }

    public function getCorreo() { return $this->correo; }
    public function setCorreo($value) { $this->correo = $value; }

    public function getId_usuario() { return $this->id_usuario; }
    public function setId_usuario($value) { $this->id_usuario = $value; }

    public function getNombre_empresa() { return $this->nombre_empresa; }
    public function setNombre_empresa($value) { $this->nombre_empresa = $value; }

    public function getRubro() { return $this->rubro; }
    public function setRubro($value) { $this->rubro = $value; }

    public function getTipo_empresa() { return $this->tipo_empresa; }
    public function setTipo_empresa($value) { $this->tipo_empresa = $value; }

    public function getPresentacion() { return $this->presentacion; }
    public function setPresentacion($value) { $this->presentacion = $value; }

    public function getBeneficios() { return $this->beneficios; }
    public function setBeneficios($value) { $this->beneficios = $value; }

    public function getLogo() { return $this->logo; }
    public function setLogo($value) { $this->logo = $value; }

    public function getNombre_contacto() { return $this->nombre_contacto; }
    public function setNombre_contacto($value) { $this->nombre_contacto = $value; }

    public function getTelefono_contacto() { return $this->telefono_contacto; }
    public function setTelefono_contacto($value) { $this->telefono_contacto = $value; }

    // GET ALL para obtener todas las empresas
    public function getAll() {
        $lista = [];
        $con = new conexion();
        $query = "SELECT 
                    e.rut_empresa, e.nombre_empresa, e.rubro, e.tipo_empresa,
                    e.presentacion, e.beneficios, e.logo,
                    e.nombre_contacto, e.telefono_contacto,
                    l.correo, l.id_estado, l.fecha_registro
                  FROM empresas e
                  INNER JOIN login_usuario l ON e.id_usuario = l.id_usuario";

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

    // funcion GET BY RUT para obtener empresa por rut
    public function getByRut(modeloEmpresa $_empresa) {
        $con = new conexion();
        $conn = $con->getConection();
        $query = "SELECT 
                    e.rut_empresa, e.nombre_empresa, e.rubro, e.tipo_empresa,
                    e.presentacion, e.beneficios, e.logo,
                    e.nombre_contacto, e.telefono_contacto,
                    l.correo, l.id_estado, l.fecha_registro
                  FROM empresas e
                  INNER JOIN login_usuario l ON e.id_usuario = l.id_usuario
                  WHERE e.rut_empresa = '" . mysqli_real_escape_string($conn, $_empresa->getRut_empresa()) . "'";

        $rs = mysqli_query($conn, $query);
        $resultado = null;

        if ($rs && mysqli_num_rows($rs) > 0) {
            $resultado = mysqli_fetch_assoc($rs);
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $resultado;
    }

    // funcion ADD para crear una empresa 
    public function add(modeloEmpresa $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        // Primero creamos el login con rol empresa (id_rol = 3)
        $hash = password_hash($this->getRut_empresa(), PASSWORD_BCRYPT);

        $query1 = "INSERT INTO login_usuario (correo, contrasena, id_rol, id_estado)
                   VALUES (
                       '" . mysqli_real_escape_string($conn, $_nuevo->getCorreo()) . "',
                       '" . $hash . "', 3, 3 )";

        $rs1 = mysqli_query($conn, $query1);

        if (!$rs1) {
            $con->closeConnection();
            return false;
        }

        $id_nuevo = mysqli_insert_id($conn);

        // Luego creamos el perfil de empresa
        $query2 = "INSERT INTO empresas 
                    (rut_empresa, id_usuario, nombre_empresa, rubro, tipo_empresa,
                     presentacion, beneficios, logo, nombre_contacto, telefono_contacto)
                   VALUES (
                       '" . mysqli_real_escape_string($conn, $_nuevo->getRut_empresa()) . "',
                       " . $id_nuevo . ",
                       '" . mysqli_real_escape_string($conn, $_nuevo->getNombre_empresa()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getRubro()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getTipo_empresa()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getPresentacion()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getBeneficios()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getLogo()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getNombre_contacto()) . "',
                       '" . mysqli_real_escape_string($conn, $_nuevo->getTelefono_contacto()) . "'
                   )";

        $rs2 = mysqli_query($conn, $query2);
        $con->closeConnection();

        return $rs2 ? true : false;
    }

    // funcion UPDATE para actualizar datos de empresa
    public function update(modeloEmpresa $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE empresas SET
                    nombre_empresa = '" . mysqli_real_escape_string($conn, $_nuevo->getNombre_empresa()) . "',
                    rubro = '" . mysqli_real_escape_string($conn, $_nuevo->getRubro()) . "',
                    tipo_empresa = '" . mysqli_real_escape_string($conn, $_nuevo->getTipo_empresa()) . "',
                    presentacion = '" . mysqli_real_escape_string($conn, $_nuevo->getPresentacion()) . "',
                    beneficios = '" . mysqli_real_escape_string($conn, $_nuevo->getBeneficios()) . "',
                    logo = '" . mysqli_real_escape_string($conn, $_nuevo->getLogo()) . "',
                    nombre_contacto = '" . mysqli_real_escape_string($conn, $_nuevo->getNombre_contacto()) . "',
                    telefono_contacto = '" . mysqli_real_escape_string($conn, $_nuevo->getTelefono_contacto()) . "'
                  WHERE rut_empresa = '" . mysqli_real_escape_string($conn, $_nuevo->getRut_empresa()) . "'";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion PATCH para actualizar solo presentacion empresa
    public function patch(modeloEmpresa $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE empresas SET
                    presentacion = '" . mysqli_real_escape_string($conn, $_nuevo->getPresentacion()) . "'
                  WHERE rut_empresa = '" . mysqli_real_escape_string($conn, $_nuevo->getRut_empresa()) . "'";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion DELETE para eliminar una empresa en CASCADE y el login_usuario asociado
    public function delete(modeloEmpresa $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "DELETE FROM login_usuario 
                  WHERE id_usuario = (
                      SELECT id_usuario FROM empresas 
                      WHERE rut_empresa = '" . mysqli_real_escape_string($conn, $_nuevo->getRut_empresa()) . "'
                  )";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }
}
?>