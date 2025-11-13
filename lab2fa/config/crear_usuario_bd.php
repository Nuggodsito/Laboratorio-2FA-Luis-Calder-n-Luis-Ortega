<?php
// Conectar como superusuario para crear el usuario con privilegios mínimos
try {
    $root_conn = new PDO("mysql:host=localhost", "root", "");
    
    // Crear base de datos
    $root_conn->exec("CREATE DATABASE IF NOT EXISTS sistema_2fa");
    
    // Crear usuario con privilegios mínimos (AGREGAMOS CREATE TEMPORARY TABLES)
    $root_conn->exec("CREATE USER IF NOT EXISTS 'usuario_2fa'@'localhost' IDENTIFIED BY 'password_segura_123'");
    $root_conn->exec("GRANT SELECT, INSERT, UPDATE, CREATE TEMPORARY TABLES ON sistema_2fa.* TO 'usuario_2fa'@'localhost'");
    $root_conn->exec("FLUSH PRIVILEGES");
    
    echo "✅ Usuario creado con privilegios mínimos<br>";
    
    // Mostrar privilegios
    $stmt = $root_conn->query("SHOW GRANTS FOR 'usuario_2fa'@'localhost'");
    $privileges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 Privilegios del usuario:<br>";
    foreach($privileges as $privilege) {
        echo "- " . $privilege['Grants for usuario_2fa@localhost'] . "<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>