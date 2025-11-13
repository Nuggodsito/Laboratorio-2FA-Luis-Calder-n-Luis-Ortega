<?php
require_once __DIR__ . '/../config/database.php';

class Google2FA {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método 1: Generar secreto 2FA
    public function generarSecreto() {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secreto = '';
        for ($i = 0; $i < 16; $i++) {
            $secreto .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $secreto;
    }

    // Método 2: Guardar secreto en BD
    public function guardarSecreto($usuario_id, $secreto) {
        $query = "UPDATE " . $this->table_name . " SET secret_2fa = :secret WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":secret", $secreto);
        $stmt->bindParam(":id", $usuario_id);
        return $stmt->execute();
    }

    // Método 3: Generar URL QR
    public function generarQR($email, $secreto, $app_name = "Sistema2FA") {
        $url = "otpauth://totp/" . rawurlencode($app_name) . ":" . rawurlencode($email) . 
               "?secret=" . $secreto . "&issuer=" . rawurlencode($app_name);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($url);
    }

    // Método 4: Verificar código 2FA
    public function verificarCodigo($secreto, $codigo) {
        if (empty($secreto) || empty($codigo)) {
            return false;
        }

        $timestamp = floor(time() / 30);
        
        // Verificar código actual y 1 anterior/posterior (por desfase de tiempo)
        for ($i = -1; $i <= 1; $i++) {
            $codigo_correcto = $this->generarCodigoTOTP($secreto, $timestamp + $i);
            if (hash_equals($codigo_correcto, $codigo)) {
                return true;
            }
        }
        return false;
    }

    // Método 5: Generar código TOTP
    private function generarCodigoTOTP($secreto, $timestamp) {
        // Decodificar base32
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32charsFlipped = array_flip(str_split($base32chars));
        
        $secreto = strtoupper($secreto);
        $secreto = str_replace('=', '', $secreto);
        
        $binary = "";
        for ($i = 0; $i < strlen($secreto); $i += 8) {
            $x = "";
            for ($j = 0; $j < 8; $j++) {
                if (isset($secreto[$i + $j])) {
                    $x .= str_pad(decbin($base32charsFlipped[$secreto[$i + $j]]), 5, '0', STR_PAD_LEFT);
                }
            }
            $eightBits = str_split($x, 8);
            foreach ($eightBits as $z) {
                $binary .= chr(bindec($z));
            }
        }
        
        // Generar HMAC
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timestamp);
        $hash = hash_hmac('sha1', $time, $binary, true);
        
        // Obtener código
        $offset = ord($hash[19]) & 0xF;
        $code = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    // MÉTODO 6: Obtener secret 2FA del usuario (NUEVO)
    public function obtenerSecret2FA($usuario_id) {
        $query = "SELECT secret_2fa FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $usuario_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['secret_2fa'] ?? null;
    }
}
?>