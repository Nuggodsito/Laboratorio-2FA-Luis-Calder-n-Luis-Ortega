<?php
session_start();
require_once 'clases/Login.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$login = new Login($db);

$mensaje = "";

if ($_POST) {
    $login->email = $_POST['email'] ?? '';
    $login->password = $_POST['password'] ?? '';
    
    if ($login->verificarCredenciales()) {
        // Credenciales correctas
        $_SESSION['usuario_id'] = $login->usuario_id;
        $_SESSION['usuario_nombre'] = $login->nombre;
        $_SESSION['usuario_email'] = $login->email;
        $_SESSION['autenticado_2fa'] = false; // Aún no pasa 2FA
        
        // Verificar si tiene 2FA activado
        if ($login->tiene2FAActivado()) {
            // Redirigir a verificación 2FA
            header("Location: verificar_2fa.php");
            exit;
        } else {
            // No tiene 2FA, redirigir a activar
            header("Location: activar_2fa.php");
            exit;
        }
    } else {
        $mensaje = "Email o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        .error { color: red; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input { padding: 5px; width: 200px; }
    </style>
</head>
<body>
    <h2>Iniciar Sesión</h2>

    <?php if ($mensaje): ?>
        <div class="error"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <p><a href="registro.php">¿No tienes cuenta? Regístrate</a></p>
</body>
</html>