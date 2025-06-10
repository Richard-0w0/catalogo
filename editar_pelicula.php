<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener los datos de la película a editar
if (isset($_POST['editar_pelicula'])) {
    $id_pelicula = $_POST['id_pelicula'];
    
    // Consulta para obtener los datos actuales de la película
    $sql = "SELECT * FROM peliculas WHERE id = $id_pelicula";
    $result = $conn->query($sql);
    $pelicula = $result->fetch_assoc();
}

// Procesar el formulario de actualización
if (isset($_POST['actualizar_pelicula'])) {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $genero = $_POST['genero'];
    $anio = $_POST['anio'];
    $descripcion = $_POST['descripcion'];
    
    // Manejo de la imagen (si se subió una nueva)
    if ($_FILES['imagen']['name']) {
        $imagen = $_FILES['imagen']['name'];
        $temp = $_FILES['imagen']['tmp_name'];
        move_uploaded_file($temp, "images/".$imagen);
    } else {
        $imagen = $_POST['imagen_actual'];
    }
    
    // Consulta UPDATE para actualizar los datos
    $sql = "UPDATE peliculas SET 
            titulo = '$titulo', 
            genero = '$genero', 
            anio = '$anio', 
            descripcion = '$descripcion', 
            imagen = '$imagen' 
            WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Película</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h1 class="mb-4">Editar Película</h1>
        <form method="POST" action="editar_pelicula.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $pelicula['id']; ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo $pelicula['imagen']; ?>">
            
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo $pelicula['titulo']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="genero" class="form-label">Género</label>
                <input type="text" class="form-control" id="genero" name="genero" value="<?php echo $pelicula['genero']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="anio" class="form-label">Año</label>
                <input type="number" class="form-control" id="anio" name="anio" value="<?php echo $pelicula['anio']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $pelicula['descripcion']; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen (dejar en blanco para mantener la actual)</label>
                <input type="file" class="form-control" id="imagen" name="imagen">
                <div class="mt-2">
                    <small>Imagen actual:</small>
                    <img src="images/<?php echo $pelicula['imagen']; ?>" width="100" class="d-block mt-1">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" name="actualizar_pelicula">Actualizar Película</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>