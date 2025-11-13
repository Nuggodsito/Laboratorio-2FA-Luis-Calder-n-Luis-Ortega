<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'sistema_2fa';
    private $username = 'usuario_2fa';
    private $password = 'password_segura_123';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Error de conexión: " . $exception->getMessage());
        }
        return $this->conn;
    }

    // Método para verificar si tabla existe
    public function tablaExiste($tabla) {
        try {
            $query = "SELECT 1 FROM " . $tabla . " LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>