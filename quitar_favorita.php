<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pelicula_id'])) {
    $pelicula_id = $conn->real_escape_string($_POST['pelicula_id']);
    $usuario_id = $_SESSION['usuario_id'];
    
    $sql = "DELETE FROM favoritas WHERE usuario_id = $usuario_id AND pelicula_id = $pelicula_id";
    $conn->query($sql);
    
    header("Location: index.php");
    exit();
}

$conn->close();
?>