<?php
// comentarios.php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pelicula_id']) && isset($_POST['comentario'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $pelicula_id = intval($_POST['pelicula_id']);
    $comentario = trim($_POST['comentario']);

    if ($comentario !== "") {
        $stmt = $conn->prepare("INSERT INTO comentarios (usuario_id, pelicula_id, comentario, fecha) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $usuario_id, $pelicula_id, $comentario);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>