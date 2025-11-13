<?php
session_start();
require_once 'clases/Google2FA.php';
require_once 'config/database.php';

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || !$_SESSION['autenticado_2fa']) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$google2fa = new Google2FA($db);

// Obtener información del usuario
$secret = $google2fa->obtenerSecret2FA($_SESSION['usuario_id']);
$tiene_2fa = !empty($secret);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mi Perfil</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .profile-card { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .status-2fa { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .active { background: #d4edda; color: #155724; }
        .inactive { background: #f8d7da; color: #721c24; }
        .menu { margin: 20px 0; }
        .menu a { margin-right: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <h1>Mi Perfil</h1>
    
    <div class="menu">
        <a href="dashboard.php">← Volver al Dashboard</a>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="profile-card">
        <h3>Información Personal</h3>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
        <p><strong>ID de Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_id']); ?></p>
    </div>

    <div class="profile-card">
        <h3>Estado de Autenticación 2FA</h3>
        <div class="status-2fa <?php echo $tiene_2fa ? 'active' : 'inactive'; ?>">
            <?php if ($tiene_2fa): ?>
                ✅ <strong>2FA ACTIVADO</strong>
                <p>Tu cuenta está protegida con autenticación de dos factores.</p>
            <?php else: ?>
                ❌ <strong>2FA NO ACTIVADO</strong>
                <p>Tu cuenta no tiene autenticación de dos factores activada.</p>
                <a href="activar_2fa.php">Activar 2FA ahora</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-card">
        <h3>Seguridad de la Sesión</h3>
        <p><strong>ID de Sesión:</strong> <?php echo session_id(); ?></p>
        <p><strong>Autenticado con 2FA:</strong> <?php echo $_SESSION['autenticado_2fa'] ? 'Sí' : 'No'; ?></p>
        <p><strong>Hora de inicio:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>