<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['es_admin']) && $_SESSION['es_admin']) {
    $titulo = $_POST['titulo'];
    $genero = $_POST['genero'];
    $anio = $_POST['anio'];
    $descripcion = $_POST['descripcion'];
    
    // Subida de imagen
    $imagen_nombre = $_FILES['imagen']['name'];
    $imagen_temp = $_FILES['imagen']['tmp_name'];
    $ruta_destino = "images/" . basename($imagen_nombre);

    if (move_uploaded_file($imagen_temp, $ruta_destino)) {
        $stmt = $conn->prepare("INSERT INTO peliculas (titulo, genero, anio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $titulo, $genero, $anio, $descripcion, $imagen_nombre);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        echo "Error al subir la imagen.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
