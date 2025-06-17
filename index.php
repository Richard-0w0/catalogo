<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$es_admin = $_SESSION['es_admin'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nueva_pelicula'])) {
    $titulo = $_POST['titulo'];
    $genero = $_POST['genero'];
    $anio = $_POST['anio'];
    $descripcion = $_POST['descripcion'];

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
        $error_subida = "❌ Error al subir la imagen.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Películas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="container mt-4">
    <h1 class="fw-bold text-center mb-4">🎬 Catálogo de Películas</h1>
    <div class="d-flex justify-content-between mb-4">
        <div>
            <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#formulario-rapido" aria-expanded="false" aria-controls="formulario-rapido">
                ➕ Añadir Película
            </button>
        </div>
        <div>
            <?php if (!$es_admin): ?>
                <a href="favoritas.php" class="btn btn-info">❤️ Mis Favoritas</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-warning">🚪 Cerrar Sesión</a>
        </div>
    </div>

    <div class="collapse mt-5" id="formulario-rapido">
        <h3>📥 Añadir Nueva Película</h3>
        <?php if (isset($error_subida)) echo "<p class='text-danger'>$error_subida</p>"; ?>
        <form action="index.php" method="POST" enctype="multipart/form-data" class="row g-3 mt-2">
            <input type="hidden" name="nueva_pelicula" value="1">
            <div class="col-md-6">
                <input type="text" name="titulo" class="form-control" placeholder="Título" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="genero" class="form-control" placeholder="Género" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="anio" class="form-control" placeholder="Año" required>
            </div>
            <div class="col-md-8">
                <input type="file" name="imagen" class="form-control" accept="image/*" required>
            </div>
            <div class="col-12">
                <textarea name="descripcion" class="form-control" placeholder="Descripción" rows="3" required></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success w-100">📤 Guardar Película</button>
            </div>
        </form>
    </div>

    <div class="row">
        <?php
        if ($usuario_id) {
            $sql = "SELECT p.*, 
                    (SELECT COUNT(*) FROM favoritas f WHERE f.pelicula_id = p.id AND f.usuario_id = $usuario_id) as es_favorita
                    FROM peliculas p ORDER BY anio DESC";
        } else {
            $sql = "SELECT p.* FROM peliculas p ORDER BY anio DESC";
        }

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $es_favorita = isset($row['es_favorita']) && $row['es_favorita'] > 0;
                echo '<div class="col-md-4 mb-4">';
                echo '  <div class="pelicula' . ($es_favorita ? ' favorita' : '') . '">';
                echo '      <img src="images/' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['titulo']) . '" class="img-fluid">';
                echo '      <h5 class="mt-2 fw-bold">' . htmlspecialchars($row['titulo']) . '</h5>';
                echo '      <p><strong>Género:</strong> ' . htmlspecialchars($row['genero']) . '</p>';
                echo '      <p><strong>Año:</strong> ' . htmlspecialchars($row['anio']) . '</p>';
                echo '      <p class="descripcion">' . htmlspecialchars(substr($row['descripcion'], 0, 100)) . '...</p>';
                echo '      <div class="d-flex justify-content-between mt-3">';

                if (!$es_admin) {
                    if ($es_favorita) {
                        echo '<form method="POST" action="quitar_favorita.php" class="w-100">';
                        echo '<input type="hidden" name="pelicula_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn btn-danger btn-sm w-100">❤️ Quitar Favorita</button>';
                        echo '</form>';
                    } else {
                        echo '<form method="POST" action="agregar_favorita.php" class="w-100">';
                        echo '<input type="hidden" name="pelicula_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn btn-outline-danger btn-sm w-100">🤍 Añadir Favorita</button>';
                        echo '</form>';
                    }
                }

                if ($es_admin) {
                    echo '<form method="POST" action="editar_pelicula.php" class="me-2">';
                    echo '<input type="hidden" name="id_pelicula" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="btn btn-primary btn-sm" name="editar_pelicula">✏️ Editar</button>';
                    echo '</form>';

                    echo '<form method="POST" action="eliminar_pelicula.php">';
                    echo '<input type="hidden" name="id_pelicula" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="btn btn-danger btn-sm" name="eliminar_pelicula" onclick="return confirm(\'¿Estás seguro?\')">🗑 Eliminar</button>';
                    echo '</form>';
                }

                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<div class="col-12"><p class="text-center text-muted">No hay películas en el catálogo.</p></div>';
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>