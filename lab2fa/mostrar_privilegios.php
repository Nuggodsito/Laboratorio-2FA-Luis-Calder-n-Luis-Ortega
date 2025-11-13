<?php
session_start();
// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || !$_SESSION['autenticado_2fa']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Privilegios de Base de Datos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .privileges { background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .menu { margin: 20px 0; }
        .menu a { margin-right: 15px; text-decoration: none; color: #007bff; }
        code { background: #f8f9fa; padding: 10px; display: block; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Privilegios de Base de Datos</h1>
    
    <div class="menu">
        <a href="dashboard.php">← Volver al Dashboard</a>
        <a href="perfil.php">Mi Perfil</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="privileges">
        <h3>✅ Usuario de Base de Datos Configurado</h3>
        <p><strong>Usuario:</strong> usuario_2fa</p>
        <p><strong>Base de datos:</strong> sistema_2fa</p>
        
        <h4>Privilegios Concedidos (Mínimos Necesarios):</h4>
        <ul>
            <li>✅ SELECT - Para consultar datos</li>
            <li>✅ INSERT - Para insertar nuevos registros</li>
            <li>✅ UPDATE - Para actualizar registros</li>
            <li>❌ DELETE - No concedido (seguridad)</li>
            <li>❌ CREATE - No concedido (seguridad)</li>
            <li>❌ DROP - No concedido (seguridad)</li>
        </ul>

        <h4>Comando para ver privilegios:</h4>
        <code>SHOW GRANTS FOR 'usuario_2fa'@'localhost';</code>

        <h4>Estructura de la tabla usuarios:</h4>
        <code>DESCRIBE usuarios;</code>
    </div>

    <div class="privileges">
        <h3>🔒 Consideraciones de Seguridad</h3>
        <p>El usuario de base de datos tiene privilegios mínimos necesarios para:</p>
        <ul>
            <li>Registrar nuevos usuarios</li>
            <li>Verificar credenciales de login</li>
            <li>Gestionar secretos 2FA</li>
            <li>No puede eliminar datos o modificar estructura</li>
        </ul>
    </div>
</body>
</html>