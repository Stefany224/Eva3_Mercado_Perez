<?php

require_once __DIR__ . '/../config/conexion.php';

class modeloSolicitud {

    private $id_solicitud;
    private $rut_empresa;
    private $id_cv;
    private $id_estado_proceso;
    private $id_nota;
    private $autor_rol;
    private $comentario;

    public function __construct() {}

    // GETTERS Y SETTERS
    public function getId_solicitud() { return $this->id_solicitud; }
    public function setId_solicitud($value) { $this->id_solicitud = $value; }

    public function getRut_empresa() { return $this->rut_empresa; }
    public function setRut_empresa($value) { $this->rut_empresa = $value; }

    public function getId_cv() { return $this->id_cv; }
    public function setId_cv($value) { $this->id_cv = $value; }

    public function getId_estado_proceso() { return $this->id_estado_proceso; }
    public function setId_estado_proceso($value) { $this->id_estado_proceso = $value; }

    public function getId_nota() { return $this->id_nota; }
    public function setId_nota($value) { $this->id_nota = $value; }

    public function getAutor_rol() { return $this->autor_rol; }
    public function setAutor_rol($value) { $this->autor_rol = $value; }

    public function getComentario() { return $this->comentario; }
    public function setComentario($value) { $this->comentario = $value; }

    // Funcion GET ALL para obtener todas las solicitudes con datos de empresa y codigo ciego del CV
    public function getAll() {
        $lista = [];
        $con = new conexion();
        $query = "SELECT 
                    s.id_solicitud, s.rut_empresa, s.id_cv,
                    s.fecha_solicitud, s.id_estado_proceso,
                    e.nombre_empresa, e.rubro,
                    cv.codigo_ciego,
                    es.nombre_estado
                  FROM solicitudes_contacto s
                  INNER JOIN empresas e ON s.rut_empresa = e.rut_empresa
                  INNER JOIN cv_perfil cv ON s.id_cv = cv.id_cv
                  INNER JOIN estados es ON s.id_estado_proceso = es.id_estado";

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

    // Funcion GET BY ID para obtener solicitud por id e incluye notas de seguimiento asociadas
    public function getById(modeloSolicitud $_solicitud) {
        $con = new conexion();
        $conn = $con->getConection();

        // Obtenemos la solicitud asegurando el casteo a entero
        $id_solicitud = (int)$_solicitud->getId_solicitud();
        
        $query = "SELECT 
                    s.id_solicitud, s.rut_empresa, s.id_cv,
                    s.fecha_solicitud, s.id_estado_proceso,
                    e.nombre_empresa, e.rubro,
                    cv.codigo_ciego,
                    es.nombre_estado
                  FROM solicitudes_contacto s
                  INNER JOIN empresas e ON s.rut_empresa = e.rut_empresa
                  INNER JOIN cv_perfil cv ON s.id_cv = cv.id_cv
                  INNER JOIN estados es ON s.id_estado_proceso = es.id_estado
                  WHERE s.id_solicitud = " . $id_solicitud;

        $rs = mysqli_query($conn, $query);
        $resultado = null;

        if ($rs && mysqli_num_rows($rs) > 0) {
            $resultado = mysqli_fetch_assoc($rs);

            // Obtenemos las notas asociadas a esta solicitud
            $query_notas = "SELECT id_nota, autor_rol, comentario, fecha_nota
                            FROM notas_seguimiento
                            WHERE id_solicitud = " . $id_solicitud;

            $rs_notas = mysqli_query($conn, $query_notas);
            $notas = [];

            if ($rs_notas) {
                while ($nota = mysqli_fetch_assoc($rs_notas)) {
                    array_push($notas, $nota);
                }
                mysqli_free_result($rs_notas);
            }

            $resultado['notas'] = $notas;
            mysqli_free_result($rs);
        }

        $con->closeConnection();
        return $resultado;
    }

    // Funcion ADD para crear nueva solicitud de contacto, su estado inicial: 4 (Contactado)
    public function add(modeloSolicitud $_nuevo)
    {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "INSERT INTO solicitudes_contacto (rut_empresa, id_cv, id_estado_proceso)
                  VALUES (
                      '" . mysqli_real_escape_string($conn, $_nuevo->getRut_empresa()) . "',
                      " . (int)$_nuevo->getId_cv() . ",  4 )";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // Funcion PATCH para actualizar estado del proceso
    public function patch(modeloSolicitud $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE solicitudes_contacto SET
                    id_estado_proceso = " . (int)$_nuevo->getId_estado_proceso() . "
                  WHERE id_solicitud = " . (int)$_nuevo->getId_solicitud();

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // Funcion ADD NOTA para agregar una nota de seguimiento
    public function addNota(modeloSolicitud $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "INSERT INTO notas_seguimiento (id_solicitud, autor_rol, comentario)
                  VALUES (
                      " . (int)$_nuevo->getId_solicitud() . ",
                      '" . mysqli_real_escape_string($conn, $_nuevo->getAutor_rol()) . "',
                      '" . mysqli_real_escape_string($conn, $_nuevo->getComentario()) . "'
                  )";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // Funcion DELETE NOTA para eliminar nota de seguimiento
    public function deleteNota(modeloSolicitud $_nuevo) {
        $con = new conexion();
        $query = "DELETE FROM notas_seguimiento
                  WHERE id_nota = " . (int)$_nuevo->getId_nota();

        $rs = mysqli_query($con->getConection(), $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // Funcion DELETE para eliminar solicitud
    public function delete(modeloSolicitud $_nuevo) {
        $con = new conexion();
        $query = "DELETE FROM solicitudes_contacto
                  WHERE id_solicitud = " . (int)$_nuevo->getId_solicitud();

        $rs = mysqli_query($con->getConection(), $query);
        $con->closeConnection();

        return $rs ? true : false;
    }
}
?>