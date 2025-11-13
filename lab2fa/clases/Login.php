<?php
require_once 'Sanitizador.php';
require_once __DIR__ . '/../config/database.php';

class Login {
    private $conn;
    private $table_name = "usuarios";

    public $email;
    public $password;
    public $usuario_id;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método 1: Verificar credenciales
    public function verificarCredenciales() {
        // Sanitizar email
        $this->email = Sanitizador::sanitizarEmail($this->email);
        
        if (!Sanitizador::validarEmail($this->email)) {
            return false;
        }

        // Buscar usuario por email
        $query = "SELECT id, nombre, hash_password, secret_2fa FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if (password_verify($this->password, $row['hash_password'])) {
                $this->usuario_id = $row['id'];
                $this->nombre = $row['nombre'];
                return true;
            }
        }
        return false;
    }

    // Método 2: Obtener secret 2FA del usuario
    public function obtenerSecret2FA() {
        $query = "SELECT secret_2fa FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->usuario_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['secret_2fa'] ?? null;
    }

    // Método 3: Verificar si usuario tiene 2FA activado
    public function tiene2FAActivado() {
        $secret = $this->obtenerSecret2FA();
        return !empty($secret);
    }
}
?>