<?php
class Database {
    // Datos de acceso a la BD MySql
    private $host = "localhost";
    private $db_name = "ejercicio_equipos";
    private $username = "root";
    private $password = "admin123";
    public $conn;

    // Función para obtener la conexión
    public function getConnection() {
        // Creamos la conexión usando PDO
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Forzamos que la comunicación sea en UTF-8
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Si algo falla mandamos la exception
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>