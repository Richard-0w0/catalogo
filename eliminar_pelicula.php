<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_pelicula'])) {
    $id_pelicula = $_POST['id_pelicula'];

    // Obtener la imagen asociada a la pel�cula
    $sql_img = "SELECT imagen FROM peliculas WHERE id = ?";
    $stmt_img = $conn->prepare($sql_img);
    $stmt_img->bind_param("i", $id_pelicula);
    $stmt_img->execute();
    $resultado = $stmt_img->get_result();
    $fila = $resultado->fetch_assoc();

    if ($fila) {
        // Eliminar la imagen del servidor
        $ruta_imagen = "images/" . $fila['imagen'];
        if (file_exists($ruta_imagen) && !empty($fila['imagen'])) {
            unlink($ruta_imagen);
        }

        // Eliminar la pel�cula de la base de datos
        $sql = "DELETE FROM peliculas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pelicula);

        if ($stmt->execute()) {
            $mensaje = "Pel�cula eliminada correctamente.";
        } else {
            $error = "Error al eliminar la pel�cula: " . $conn->error;
        }
    } else {
        $error = "Pel�cula no encontrada.";
    }
}
$conn->close();
header("Location: index.php"); // Redirige de vuelta al cat�logo
exit();
?>
