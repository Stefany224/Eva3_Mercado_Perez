<?php
include_once '../config/conexion.php';

class ModeloCandidato {

    // Obtenemos los datos del Perfil del Candidato
    public function ObtenerPerfilCandidato($rut) {
        $obj_con = new conexion();
        $con = $obj_con->getConection();
        $rut = intval($rut);

        // Consulta relacional para traer los datos personales del usuario y el estado de su cuenta
        $query = "SELECT u.rut, u.nombre, u.apellido_paterno, u.apellido_materno, u.telefono, u.comuna, u.porcentaje_avance, e.nombre_estado
                  FROM usuario u
                  INNER JOIN estados e ON u.id_estado_perfil = e.id_estado
                  WHERE u.rut = $rut";
        
        $res = mysqli_query($con, $query);
        $data = mysqli_fetch_assoc($res);
        
        $obj_con->closeConnection();
        return $data;
    }

    // Agregamos Experiencia Laboral al cv 
    public function AgregarExperiencia($id_cv, $empresa, $cargo, $meses, $descripcion) {
        $obj_con = new conexion();
        $con = $obj_con->getConection();

        $id_cv = intval($id_cv);
        $meses = intval($meses);
        
        $empresa = mysqli_real_escape_string($con, $empresa);
        $cargo = mysqli_real_escape_string($con, $cargo);
        $descripcion = mysqli_real_escape_string($con, $descripcion);

        $query = "INSERT INTO cv_experiencias (id_cv, empresa_anonima, cargo, meses_experiencia, descripcion_funciones) 
                  VALUES ($id_cv, '$empresa', '$cargo', $meses, '$descripcion')";
        
        $res = mysqli_query($con, $query);
        
        // Cada vez que se agrega experiencia actualizamos automaticamente el avance del perfil 
        if ($res) {
            mysqli_query($con, "UPDATE usuario u 
                                INNER JOIN cv_perfil cv ON u.rut = cv.rut_persona 
                                SET u.porcentaje_avance = u.porcentaje_avance + 20 
                                WHERE cv.id_cv = $id_cv AND u.porcentaje_avance < 100");
        }

        $obj_con->closeConnection();
        return $res;
    }

    // Agregamos Competencia Tecnica al Curriculum 
    public function AgregarCompetencia($id_cv, $nombre_competencia, $nivel) {
        $obj_con = new conexion();
        $con = $obj_con->getConection();

        $id_cv = intval($id_cv);
        $nombre_competencia = mysqli_real_escape_string($con, $nombre_competencia);
        $nivel = mysqli_real_escape_string($con, $nivel);

        $query = "INSERT INTO cv_competencias_tecnicas (id_cv, nombre_competencia, nivel) 
                  VALUES ($id_cv, '$nombre_competencia', '$nivel')";
        
        $res = mysqli_query($con, $query);
        
        // Incrementamos otro porcentaje de avance en la ficha del postulante
        if ($res) {
            mysqli_query($con, "UPDATE usuario u 
                                INNER JOIN cv_perfil cv ON u.rut = cv.rut_persona 
                                SET u.porcentaje_avance = u.porcentaje_avance + 15 
                                WHERE cv.id_cv = $id_cv AND u.porcentaje_avance < 100");
        }

        $obj_con->closeConnection();
        return $res;
    }
}
?>