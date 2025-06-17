<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usuario']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash de la contraseña
    $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');

    // Verificar si el usuario ya existe
    $check_sql = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $error = "El nombre de usuario ya está en uso";
    } else {
        // Insertar nuevo usuario (no admin por defecto)
        $sql = "INSERT INTO usuarios (usuario, password, nombre, es_admin) VALUES ('$usuario', '$password', '$nombre', FALSE)";

        if ($conn->query($sql)) {
            $_SESSION['usuario'] = $usuarios;
            $_SESSION['es_admin'] = false;
            header("Location: index.php");
            exit();
        } else {
            $error = "Error al registrar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: white;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container mt-5">
    <h2>🎬 Registro de Usuario</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form action="registro.php" method="POST">
        <div class="mb-3">
            <input type="text" class="form-control" name="nombre" placeholder="Nombre completo" required>
        </div>
        <div class="mb-3">
            <input type="text" class="form-control" name="usuario" placeholder="Nombre de usuario" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
    <p class="mt-3">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>