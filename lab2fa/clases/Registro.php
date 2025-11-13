<?php
require_once 'Sanitizador.php';
require_once __DIR__ . '/../config/database.php'; // Ruta corregida

class Registro {
    private $conn;
    private $table_name = "usuarios";

    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $sexo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método 1: Validar datos del formulario
    public function validarDatos() {
        $errores = [];

        // Validar nombre
        if (empty($this->nombre)) {
            $errores[] = "El nombre es requerido";
        } else {
            $this->nombre = Sanitizador::sanitizarString($this->nombre);
            if (strlen($this->nombre) < 2) {
                $errores[] = "El nombre debe tener al menos 2 caracteres";
            }
        }

        // Validar apellido
        if (empty($this->apellido)) {
            $errores[] = "El apellido es requerido";
        } else {
            $this->apellido = Sanitizador::sanitizarString($this->apellido);
        }

        // Validar email
        if (empty($this->email)) {
            $errores[] = "El email es requerido";
        } else {
            $this->email = Sanitizador::sanitizarEmail($this->email);
            if (!Sanitizador::validarEmail($this->email)) {
                $errores[] = "El formato del email no es válido";
            } elseif ($this->emailExiste()) {
                $errores[] = "Este email ya está registrado";
            }
        }

        // Validar password
        if (empty($this->password)) {
            $errores[] = "La contraseña es requerida";
        } elseif (strlen($this->password) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        // Validar sexo
        if (empty($this->sexo)) {
            $errores[] = "El sexo es requerido";
        } else {
            if (!Sanitizador::validarSexo($this->sexo)) {
                $errores[] = "El sexo debe ser M o F";
            }
        }

        return $errores;
    }

    // Método 2: Verificar si email existe
    private function emailExiste() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Método 3: Generar hash de contraseña
    public function generarHashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Método 4: Registrar usuario
    public function registrar() {
        // Validar datos primero
        $errores = $this->validarDatos();
        if (!empty($errores)) {
            return ["success" => false, "errors" => $errores];
        }

        // Insertar en la base de datos
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, apellido, email, hash_password, sexo) 
                 VALUES (:nombre, :apellido, :email, :hash_password, :sexo)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar y bindear parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":sexo", $this->sexo);

        // Hash de la contraseña
        $hash_password = $this->generarHashPassword($this->password);
        $stmt->bindParam(":hash_password", $hash_password);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Usuario registrado correctamente"];
        } else {
            return ["success" => false, "errors" => ["Error al registrar el usuario"]];
        }
    }
}
?>