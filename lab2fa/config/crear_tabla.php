<?php
// Conectar como ROOT para crear la tabla (solo una vez)
try {
    $root_conn = new PDO("mysql:host=localhost", "root", "");
    $root_conn->exec("USE sistema_2fa");

    // Verificar si la tabla ya existe
    $check_table = $root_conn->query("SHOW TABLES LIKE 'usuarios'");
    if ($check_table->rowCount() > 0) {
        echo "✅ La tabla 'usuarios' ya existe";
    } else {
        // Crear tabla de usuarios - SIN comentarios en SQL
        $query = "CREATE TABLE usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            apellido VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            hash_password VARCHAR(255) NOT NULL,
            sexo ENUM('M', 'F') NOT NULL,
            secret_2fa VARCHAR(255) NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $root_conn->exec($query);
        echo "✅ Tabla 'usuarios' creada correctamente";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>