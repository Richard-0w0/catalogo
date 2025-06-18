<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Eliminar películas creadas por el usuario
// Primero, elimina las relaciones en favoritas y comentarios para evitar errores de clave foránea
$sql_favoritas = "DELETE FROM favoritas WHERE pelicula_id IN (SELECT id FROM peliculas WHERE usuario_id = ?)";
$stmt_fav = $conn->prepare($sql_favoritas);
$stmt_fav->bind_param("i", $usuario_id);
$stmt_fav->execute();
$stmt_fav->close();

$sql_comentarios = "DELETE FROM comentarios WHERE pelicula_id IN (SELECT id FROM peliculas WHERE usuario_id = ?)";
$stmt_com = $conn->prepare($sql_comentarios);
$stmt_com->bind_param("i", $usuario_id);
$stmt_com->execute();
$stmt_com->close();

// Ahora elimina las películas creadas por el usuario
$sql_peliculas = "DELETE FROM peliculas WHERE usuario_id = ?";
$stmt_pel = $conn->prepare($sql_peliculas);
$stmt_pel->bind_param("i", $usuario_id);
$stmt_pel->execute();
$stmt_pel->close();

header("Location: favoritas.php");
exit();
?>