<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Películas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: white;
        }
        .container {
            max-width: 900px;
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
        }
        .pelicula {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .pelicula img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1 class="fw-bold">🎬 Catálogo de Películas</h1>
    <div class="mb-3">
        <a href="agregar.php" class="btn btn-success">➕ Añadir Película</a>
        <a href="logout.php" class="btn btn-warning">Cerrar Sesión</a>
    </div>
    <div class="row">
        <?php
        $sql = "SELECT * FROM peliculas ORDER BY anio DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4">';
                echo '  <div class="pelicula">';
                echo '      <img src="images/' . $row['imagen'] . '" alt="' . $row['titulo'] . '">';
                echo '      <h5 class="mt-2 fw-bold">' . $row['titulo'] . '</h5>';
                echo '      <p><strong>Género:</strong> ' . $row['genero'] . '</p>';
                echo '      <p><strong>Año:</strong> ' . $row['anio'] . '</p>';
                echo '      <p>' . $row['descripcion'] . '</p>';
                // Botón para eliminar la película
                echo '      <form method="POST" action="eliminar_pelicula.php" style="margin-top: 10px;">';
                echo '          <input type="hidden" name="id_pelicula" value="' . $row['id'] . '">';
                echo '          <button type="submit" class="btn btn-danger btn-sm" name="eliminar_pelicula" onclick="return confirm(\'¿Estás seguro de que deseas eliminar esta película?\');">🗑 Eliminar</button>';
                echo '      </form>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center text-muted">No hay películas en el catálogo.</p>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
