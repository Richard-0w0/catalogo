<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($conn->real_escape_string($_POST['usuario']));
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $check_sql = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
        $error = "El nombre de usuario ya está en uso";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $usuario, $password_hash);
        if ($stmt->execute()) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['usuario_id'] = $stmt->insert_id;
            $_SESSION['es_admin'] = 0;
            $stmt->close();
            header("Location: home.php");
            exit();
        } else {
            $error = "Error al registrar usuario";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="login-container mt-5">
    <h2>📝 Registro</h2>
    <form action="" method="POST" autocomplete="off">
        <input type="text" class="form-control" name="usuario" placeholder="Usuario" required autofocus>
        <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
        <?php if (isset($error)) echo "<div class='text-danger'>$error</div>"; ?>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
    <p class="mt-3" style="color: white;">¿Ya tienes cuenta? <a href="login.php" style="color: #17a2b8;">Inicia sesión</a></p>
</div>
</body>
</html>
<?php $conn->close(); ?>