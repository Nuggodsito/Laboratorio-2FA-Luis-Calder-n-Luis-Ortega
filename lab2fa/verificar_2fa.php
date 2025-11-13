<?php
session_start();
require_once 'clases/Google2FA.php';
require_once 'config/database.php';

// Verificar que el usuario está logueado pero sin 2FA
if (!isset($_SESSION['usuario_id']) || $_SESSION['autenticado_2fa']) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$google2fa = new Google2FA($db);

$mensaje = "";

// Obtener el secreto del usuario
$secret = $google2fa->obtenerSecret2FA($_SESSION['usuario_id']);

// Verificar código
if ($_POST && isset($_POST['codigo_2fa'])) {
    $codigo = $_POST['codigo_2fa'];
    
    if ($google2fa->verificarCodigo($secret, $codigo)) {
        $_SESSION['autenticado_2fa'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = "❌ Código incorrecto. Intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verificar Código 2FA</title>
    <style>
        .error { color: red; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input { padding: 5px; width: 200px; }
    </style>
</head>
<body>
    <h2>Verificación de Dos Factores (2FA)</h2>

    <p>Hola <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>,</p>
    <p>Ingresa el código de 6 dígitos de Google Authenticator:</p>

    <?php if ($mensaje): ?>
        <div class="error"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Código de verificación:</label>
            <input type="text" name="codigo_2fa" maxlength="6" required placeholder="123456">
        </div>
        <button type="submit">Verificar</button>
    </form>

    <p><a href="logout.php">Cancelar y salir</a></p>
</body>
</html>