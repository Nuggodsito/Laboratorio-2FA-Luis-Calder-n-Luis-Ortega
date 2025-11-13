<?php
class Sanitizador {
    
    // Sanitizar string básico
    public static function sanitizarString($dato) {
        $dato = trim($dato);
        $dato = stripslashes($dato);
        $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
        return $dato;
    }
    
    // Sanitizar email
    public static function sanitizarEmail($email) {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $email;
    }
    
    // Sanitizar para SQL (prevención básica)
    public static function sanitizarSQL($dato) {
        $dato = trim($dato);
        $dato = stripslashes($dato);
        return $dato;
    }
    
    // Validar email
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validar sexo
    public static function validarSexo($sexo) {
        return in_array($sexo, ['M', 'F']);
    }
}
?>