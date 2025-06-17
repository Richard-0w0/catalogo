<?php 
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['usuario'] = $usuario;
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
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
    <h2>🎬 Iniciar Sesión</h2>
    <form action="" method="POST">
        <div class="mb-3">
            <input type="text" class="form-control" name="usuario" placeholder="Usuario" required>
        </div>
        <div class="mb-3">
            <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
        <a href="registro.php" class="btn btn-success ms-2">Registro</a>
    </form>
    <?php if (isset($error)) echo "<p class='text-danger mt-2'>$error</p>"; ?>
</div>

</body>
</html>
