<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['titulo'], $_POST['genero'], $_POST['anio'], $_POST['descripcion']) &&
        isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK
    ) {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $genero = $conn->real_escape_string($_POST['genero']);
        $anio = intval($_POST['anio']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);

        $directorio = "images/";
        $nombre_imagen = uniqid() . "_" . basename($_FILES["imagen"]["name"]);
        $ruta_imagen = $directorio . $nombre_imagen;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_imagen)) {
            $sql = "INSERT INTO peliculas (titulo, genero, anio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiss", $titulo, $genero, $anio, $descripcion, $nombre_imagen);
            if ($stmt->execute()) {
                echo "<script>
                        alert('Película agregada con éxito');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                echo "Error al insertar datos: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "Por favor, complete todos los campos correctamente.";
    }
    $conn->close();
    exit();
} else {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Agregar Película</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: black;
                color: white;
            }
            .container {
                max-width: 600px;
                background: rgba(255, 255, 255, 0.1);
                padding: 20px;
                border-radius: 10px;
                margin-top: 40px;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1 class="mb-4">Agregar Película</h1>
        <form method="POST" action="agregar.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="mb-3">
                <label for="genero" class="form-label">Género</label>
                <input type="text" class="form-control" id="genero" name="genero" required>
            </div>
            <div class="mb-3">
                <label for="anio" class="form-label">Año</label>
                <input type="number" class="form-control" id="anio" name="anio" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-success" name="añadir_pelicula">Agregar Película</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
$conn->close();
?>
