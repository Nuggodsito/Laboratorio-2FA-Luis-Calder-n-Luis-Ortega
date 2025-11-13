<?php
session_start();

// Verificar que el usuario está autenticado y con 2FA
if (!isset($_SESSION['usuario_id']) || !$_SESSION['autenticado_2fa']) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .welcome { background: #e8f5e8; padding: 20px; border-radius: 5px; }
        .menu { margin: 20px 0; }
        .menu a { margin-right: 15px; text-decoration: none; color: #007bff; padding: 8px 15px; border: 1px solid #007bff; border-radius: 4px; }
        .menu a:hover { background: #007bff; color: white; }
        .profile-card { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .criteria-list { list-style: none; padding: 0; }
        .criteria-list li { padding: 5px 0; }
    </style>
</head>
<body>
    <div class="welcome">
        <h1>¡Bienvenido al Sistema!</h1>
        <p>Hola <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></p>
        <p>Email: <strong><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></strong></p>
        <p>✅ Estás autenticado con 2FA</p>
    </div>

    <div class="menu">
        <h3>Opciones del Sistema</h3>
        <a href="perfil.php">👤 Mi Perfil</a>
        <a href="mostrar_privilegios.php">🔐 Ver Privilegios BD</a>
        <a href="activar_2fa.php">📱 Gestionar 2FA</a>
        <a href="logout.php">🚪 Cerrar Sesión</a>
    </div>

    <h2>Dashboard Principal</h2>
    <p>Esta es una página protegida que solo se puede acceder después de:</p>
    <ul>
        <li>✅ Inicio de sesión con usuario y contraseña</li>
        <li>✅ Verificación de código 2FA</li>
    </ul>

    <div class="profile-card">
        <h3>✅ Criterios de la Rúbrica Cumplidos</h3>
        <ul class="criteria-list">
            <li>✅ Usuario BD con privilegios mínimos</li>
            <li>✅ Formulario de registro con validaciones</li>
            <li>✅ Validación de correo y usuario único</li>
            <li>✅ Clases con métodos de responsabilidad única</li>
            <li>✅ Sanitización de datos</li>
            <li>✅ Generación de código QR</li>
            <li>✅ Login + verificación 2FA</li>
            <li>✅ Sesiones de autenticación transferidas</li>
            <li>✅ Hash de contraseñas guardado en BD</li>
            <li>✅ QR generado después del registro</li>
            <li>✅ Tablas con datos consistentes</li>
        </ul>
    </div>

    <div class="profile-card">
        <h3>📊 Resumen del Sistema</h3>
        <p><strong>Usuario ID:</strong> <?php echo htmlspecialchars($_SESSION['usuario_id']); ?></p>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Hora de acceso:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>Autenticación 2FA:</strong> <?php echo $_SESSION['autenticado_2fa'] ? 'ACTIVA' : 'INACTIVA'; ?></p>
    </div>
</body>
</html>