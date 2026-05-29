<?php
include_once '../config/conexion.php';

class ModelEmpresa {
    
    // Busqueda Inversa con Filtros Profesionales y Cv ciego
    public function BuscarPostulantesCiegos($comuna, $nivel_educacional, $carrera) {
        $obj_con = new conexion();
        $con = $obj_con->getConection();

        // Traemos el perfil y sus datos laborales
        $query = "SELECT cv.id_cv, cv.codigo_ciego, cv.resumen_laboral, cv.nivel_educacional, 
                         cv.carrera, cv.renta_deseada, cv.jornada_deseada, cv.modalidad_deseada, u.comuna
                  FROM cv_perfil cv
                  INNER JOIN usuario u ON cv.rut_persona = u.rut
                  WHERE u.id_estado_perfil = 3"; // Estado 3 = 'Aprobado Vitrina' (Solo candidatos validados por el municipio)

        // Inyeccion dinamica de filtros 
        if (!empty($comuna)) {
            $comuna = mysqli_real_escape_string($con, $comuna);
            $query .= " AND u.comuna LIKE '%$comuna%'";
        }
        if (!empty($nivel_educacional)) {
            $nivel_educacional = mysqli_real_escape_string($con, $nivel_educacional);
            $query .= " AND cv.nivel_educacional LIKE '%$nivel_educacional%'";
        }
        if (!empty($carrera)) {
            $carrera = mysqli_real_escape_string($con, $carrera);
            $query .= " AND cv.carrera LIKE '%$carrera%'";
        }

        $res = mysqli_query($con, $query);
        $lista_perfiles = mysqli_fetch_all($res, MYSQLI_ASSOC);

        // Para cada perfil ciego encontrado se adjunta sus experiencias y competencias
        foreach ($lista_perfiles as $key => $perfil) {
            $id_cv = $perfil['id_cv'];

            // Traemos las experiencias 
            $res_exp = mysqli_query($con, "SELECT cargo, meses_experiencia, empresa_anonima, descripcion_functions FROM cv_experiencias WHERE id_cv = $id_cv");
            $lista_perfiles[$key]['experiencias'] = mysqli_fetch_all($res_exp, MYSQLI_ASSOC);

            // Traemos las competencias tecnicas
            $res_comp = mysqli_query($con, "SELECT nombre_competencia, nivel FROM cv_competencias_tecnicas WHERE id_cv = $id_cv");
            $lista_perfiles[$key]['competencias'] = mysqli_fetch_all($res_comp, MYSQLI_ASSOC);
        }

        $obj_con->closeConnection();
        // Retornamos el arbol de datos ciego listo para JSON
        return $lista_perfiles;
    }
}
?>