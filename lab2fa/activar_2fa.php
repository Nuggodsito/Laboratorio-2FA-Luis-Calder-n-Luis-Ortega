<?php
session_start();
require_once 'clases/Google2FA.php';
require_once 'config/database.php';

// Verificar que el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$google2fa = new Google2FA($db);

$mensaje = "";

// Generar secreto y QR si no existe
if (!isset($_SESSION['secret_temp'])) {
    $_SESSION['secret_temp'] = $google2fa->generarSecreto();
}

$secret = $_SESSION['secret_temp'];
$qr_url = $google2fa->generarQR($_SESSION['usuario_email'], $secret, "Sistema2FA");

// Procesar activación de 2FA
if ($_POST && isset($_POST['codigo_2fa'])) {
    $codigo = $_POST['codigo_2fa'];
    
    if ($google2fa->verificarCodigo($secret, $codigo)) {
        // Código correcto, guardar en BD
        if ($google2fa->guardarSecreto($_SESSION['usuario_id'], $secret)) {
            $_SESSION['autenticado_2fa'] = true;
            $mensaje = "✅ 2FA activado correctamente";
            unset($_SESSION['secret_temp']); // Limpiar secreto temporal
        } else {
            $mensaje = "❌ Error al guardar la configuración 2FA";
        }
    } else {
        $mensaje = "❌ Código incorrecto. Intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activar Autenticación 2FA</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input { padding: 5px; width: 200px; }
        .qr-container { margin: 20px 0; }
    </style>
</head>
<body>
    <h2>Activar Autenticación de Dos Factores (2FA)</h2>

    <?php if ($mensaje): ?>
        <div class="<?php echo strpos($mensaje, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($_POST['codigo_2fa']) || strpos($mensaje, '❌') !== false): ?>
        <div class="qr-container">
            <p><strong>Paso 1:</strong> Escanea este código QR con Google Authenticator</p>
            <img src="<?php echo $qr_url; ?>" alt="Código QR para 2FA">
            <p><strong>Secreto:</strong> <?php echo $secret; ?></p>
        </div>

        <div class="form-container">
            <p><strong>Paso 2:</strong> Ingresa el código de 6 dígitos de la app</p>
            <form method="post">
                <div class="form-group">
                    <label>Código de verificación:</label>
                    <input type="text" name="codigo_2fa" maxlength="6" required placeholder="123456">
                </div>
                <button type="submit">Activar 2FA</button>
            </form>
        </div>
    <?php else: ?>
        <p><a href="dashboard.php">Ir al Dashboard</a></p>
    <?php endif; ?>

    <p><a href="logout.php">Cerrar Sesión</a></p>
</body>
</html>