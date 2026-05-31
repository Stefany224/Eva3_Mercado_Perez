<?php

require_once __DIR__ . '/../config/conexion.php';

class modeloCvPerfil {
   
    private $id_cv;
    private $rut_persona;
    private $codigo_ciego;
    private $resumen_laboral;
    private $nivel_educacional;
    private $carrera;
    private $renta_deseada;
    private $jornada_deseada;
    private $modalidad_deseada;

    public function __construct() {}

    // GETTERS Y SETTERS
    public function getId_cv() { return $this->id_cv; }
    public function setId_cv($value) { $this->id_cv = $value; }

    public function getRut_persona() { return $this->rut_persona; }
    public function setRut_persona($value) { $this->rut_persona = $value; }

    public function getCodigo_ciego() { return $this->codigo_ciego; }
    public function setCodigo_ciego($value) { $this->codigo_ciego = $value; }

    public function getResumen_laboral() { return $this->resumen_laboral; }
    public function setResumen_laboral($value) { $this->resumen_laboral = $value; }

    public function getNivel_educacional() { return $this->nivel_educacional; }
    public function setNivel_educacional($value) { $this->nivel_educacional = $value; }

    public function getCarrera() { return $this->carrera; }
    public function setCarrera($value) { $this->carrera = $value; }

    public function getRenta_deseada() { return $this->renta_deseada; }
    public function setRenta_deseada($value) { $this->renta_deseada = $value; }

    public function getJornada_deseada() { return $this->jornada_deseada; }
    public function setJornada_deseada($value) { $this->jornada_deseada = $value; }

    public function getModalidad_deseada() { return $this->modalidad_deseada; }
    public function setModalidad_deseada($value) { $this->modalidad_deseada = $value; }

    // funcion GET ALL para obtener todos los CV (vista ciega) y solo muestra datos que puede ver la empresa
    public function getAll() {
        $lista = [];
        $con = new conexion();
        $query = "SELECT 
                    cv.id_cv, cv.codigo_ciego, cv.resumen_laboral,
                    cv.nivel_educacional, cv.carrera,
                    cv.renta_deseada, cv.jornada_deseada, cv.modalidad_deseada
                  FROM cv_perfil cv
                  INNER JOIN usuario u ON cv.rut_persona = u.rut";

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

    // funcion GET BY ID para obtener CV completo por id_cv y la vista completa para administrador
    public function getById(modeloCvPerfil $_cv) {
        $con = new conexion();
        $query = "SELECT 
                    cv.id_cv, cv.rut_persona, cv.codigo_ciego, cv.resumen_laboral,
                    cv.nivel_educacional, cv.carrera, cv.renta_deseada,
                    cv.jornada_deseada, cv.modalidad_deseada,
                    u.nombre, u.apellido_paterno, u.apellido_materno,
                    u.telefono, u.comuna
                  FROM cv_perfil cv
                  INNER JOIN usuario u ON cv.rut_persona = u.rut
                  WHERE cv.id_cv = " . $_cv->getId_cv();

        $rs = mysqli_query($con->getConection(), $query);
        $resultado = null;

        if ($rs && mysqli_num_rows($rs) > 0) {
            $resultado = mysqli_fetch_assoc($rs);
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $resultado;
    }

    // funcion GET CV CIEGO para la vista que ve la empresa sin datos personales del candidato
    public function getCvCiego(modeloCvPerfil $_cv)
    {
        $con = new conexion();
        $query = "SELECT 
                    cv.id_cv, cv.codigo_ciego, cv.resumen_laboral,
                    cv.nivel_educacional, cv.carrera,
                    cv.renta_deseada, cv.jornada_deseada, cv.modalidad_deseada
                  FROM cv_perfil cv
                  WHERE cv.id_cv = " . $_cv->getId_cv();

        $rs = mysqli_query($con->getConection(), $query);
        $resultado = null;

        if ($rs && mysqli_num_rows($rs) > 0) {
            $resultado = mysqli_fetch_assoc($rs);
            mysqli_free_result($rs);
        }
        $con->closeConnection();
        return $resultado;
    }

    // =============================================
    // funcion ADD para crear perfil CV, genera codigo_ciego automaticamente
    public function add(modeloCvPerfil $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        // Generamos codigo ciego unico PE + RUT + timestamp
        $codigo = 'PE' . $_nuevo->getRut_persona() . time();

        $query = "INSERT INTO cv_perfil 
                    (rut_persona, codigo_ciego, resumen_laboral, nivel_educacional, 
                     carrera, renta_deseada, jornada_deseada, modalidad_deseada)
                  VALUES (
                    " . $_nuevo->getRut_persona() . ",
                    '" . $codigo . "',
                    '" . mysqli_real_escape_string($conn, $_nuevo->getResumen_laboral()) . "',
                    '" . mysqli_real_escape_string($conn, $_nuevo->getNivel_educacional()) . "',
                    '" . mysqli_real_escape_string($conn, $_nuevo->getCarrera()) . "',
                    " . (int)$_nuevo->getRenta_deseada() . ",
                    '" . mysqli_real_escape_string($conn, $_nuevo->getJornada_deseada()) . "',
                    '" . mysqli_real_escape_string($conn, $_nuevo->getModalidad_deseada()) . "'
                  )";

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion UPDATE para actualizar CV completo
    public function update(modeloCvPerfil $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE cv_perfil SET
                    resumen_laboral = '" . mysqli_real_escape_string($conn, $_nuevo->getResumen_laboral()) . "',
                    nivel_educacional = '" . mysqli_real_escape_string($conn, $_nuevo->getNivel_educacional()) . "',
                    carrera = '" . mysqli_real_escape_string($conn, $_nuevo->getCarrera()) . "',
                    renta_deseada = " . (int)$_nuevo->getRenta_deseada() . ",
                    jornada_deseada = '" . mysqli_real_escape_string($conn, $_nuevo->getJornada_deseada()) . "',
                    modalidad_deseada = '" . mysqli_real_escape_string($conn, $_nuevo->getModalidad_deseada()) . "'
                  WHERE id_cv = " . $_nuevo->getId_cv();

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion PATCHpara actualizar solo resumen laboral
    public function patch(modeloCvPerfil $_nuevo) {
        $con = new conexion();
        $conn = $con->getConection();

        $query = "UPDATE cv_perfil SET
                    resumen_laboral = '" . mysqli_real_escape_string($conn, $_nuevo->getResumen_laboral()) . "'
                  WHERE id_cv = " . $_nuevo->getId_cv();

        $rs = mysqli_query($conn, $query);
        $con->closeConnection();

        return $rs ? true : false;
    }

    // funcion DELETE para eliminar CV y en CASCADE elimina experiencias y competencias
    public function delete(modeloCvPerfil $_nuevo) {
        $con = new conexion();
        $query = "DELETE FROM cv_perfil 
                  WHERE id_cv = " . $_nuevo->getId_cv();

        $rs = mysqli_query($con->getConection(), $query);
        $con->closeConnection();

        return $rs ? true : false;
    }
}
?>