<?php
class conexion {
    private $connection;
    private $host = '127.0.0.1'; 
    private $port = '3306';
    private $db = 'db_proviemplea'; // Nombre de la BD del proyecto
    private $username = 'root';
    private $password = '';

    public function getConection() {
        try {
            $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->db, $this->port);
            mysqli_set_charset($this->connection, 'utf8mb4');
            if (!$this->connection) {
                throw new Exception("Error de conexion a la Base de Datos");
            }
            return $this->connection;
        } catch (Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(["error" => "Fallo de conexion al servidor de datos"]);
            exit;
        }
    }

    public function closeConnection() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}
?>