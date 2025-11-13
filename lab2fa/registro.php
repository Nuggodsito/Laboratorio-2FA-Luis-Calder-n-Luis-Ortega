<?php
session_start();
require_once 'clases/Registro.php';
require_once 'config/database.php'; // Ruta corregida

$database = new Database();
$db = $database->getConnection();
$registro = new Registro($db);

$mensaje = "";
$tipo_mensaje = "";

if ($_POST) {
    // Asignar datos del formulario
    $registro->nombre = $_POST['nombre'] ?? '';
    $registro->apellido = $_POST['apellido'] ?? '';
    $registro->email = $_POST['email'] ?? '';
    $registro->password = $_POST['password'] ?? '';
    $registro->sexo = $_POST['sexo'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validar que las contraseñas coincidan
    if ($registro->password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden";
        $tipo_mensaje = "error";
    } else {
        // Intentar registrar
        $resultado = $registro->registrar();
        
        if ($resultado['success']) {
            $mensaje = $resultado['message'];
            $tipo_mensaje = "success";
            // Limpiar formulario
            $registro->nombre = $registro->apellido = $registro->email = $registro->sexo = '';
        } else {
            $mensaje = implode("<br>", $resultado['errors']);
            $tipo_mensaje = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; }
        input, select { padding: 5px; width: 200px; }
    </style>
</head>
<body>
    <h2>Registro de Usuario</h2>

    <?php if ($mensaje): ?>
        <div class="<?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($registro->nombre ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($registro->apellido ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($registro->email ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required minlength="6">
        </div>

        <div class="form-group">
            <label>Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" required>
        </div>

        <div class="form-group">
            <label>Sexo:</label>
            <select name="sexo" required>
                <option value="">Seleccionar</option>
                <option value="M" <?php echo (($registro->sexo ?? '') == 'M') ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo (($registro->sexo ?? '') == 'F') ? 'selected' : ''; ?>>Femenino</option>
            </select>
        </div>

        <button type="submit">Registrarse</button>
    </form>

    <p><a href="login.php">¿Ya tienes cuenta? Inicia sesión</a></p>
</body>
</html>