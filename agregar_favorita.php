<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pelicula_id'])) {
    $pelicula_id = intval($_POST['pelicula_id']);
    $usuario_id = intval($_SESSION['usuario_id']);

    $sql = "INSERT IGNORE INTO favoritas (usuario_id, pelicula_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $pelicula_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

$conn->close();
?>