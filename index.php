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

if ($usuario_id) {
    $sql = "SELECT p.*
            FROM peliculas p
            ORDER BY anio DESC";
} else {
    $sql = "SELECT p.* FROM peliculas p ORDER BY anio DESC";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Minimalista</title>
    <link rel="stylesheet" href="estilos.css">
    
    <script>
        function toggleForm() {
            var form = document.getElementById('formulario-rapido');
            form.classList.toggle('active');
        }
    </script>
</head>
<body>
<div class="container-minimal">
    <div class="header-minimal">
        <h1>🎬 Catálogo Minimalista</h1>
        <div style="margin-left:auto;">
            <div class="topbar-btns" style="justify-content: flex-end;">
                <button class="plus-btn" onclick="toggleForm()" title="Agregar Película">+</button>
                <a href="administrar.php" class="btn-admin">Administrar</a>
                <a href="logout.php" class="btn btn-warning">🚪 Cerrar Sesión</a>
            </div>
        </div>
    </div>

    <div id="formulario-rapido" class="formulario-rapido">
        <h3 style="color:#fff;">📥 Añadir Nueva Película</h3>
        <?php if (isset($error_subida)) echo "<p class='text-danger'>$error_subida</p>"; ?>
        <form action="index.php" method="POST" enctype="multipart/form-data" class="row g-3 mt-2">
            <input type="hidden" name="nueva_pelicula" value="1">
            <div style="margin-bottom:12px;">
                <input type="text" name="titulo" class="form-control" placeholder="Título" required style="width:100%;padding:10px;border-radius:8px;border:none;">
            </div>
            <div style="margin-bottom:12px;">
                <input type="text" name="genero" class="form-control" placeholder="Género" required style="width:100%;padding:10px;border-radius:8px;border:none;">
            </div>
            <div style="margin-bottom:12px;">
                <input type="number" name="anio" class="form-control" placeholder="Año" required style="width:100%;padding:10px;border-radius:8px;border:none;">
            </div>
            <div style="margin-bottom:12px;">
                <input type="file" name="imagen" class="form-control" accept="image/*" required style="width:100%;padding:10px;border-radius:8px;border:none;">
            </div>
            <div style="margin-bottom:12px;">
                <textarea name="descripcion" class="form-control" placeholder="Descripción" rows="3" required style="width:100%;padding:10px;border-radius:8px;border:none;"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-info" style="width:100%;">📤 Guardar Película</button>
            </div>
        </form>
    </div>

    <div class="peliculas-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="pelicula-card">';
                echo '  <img src="images/' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['titulo']) . '">';
                echo '  <h5>' . htmlspecialchars($row['titulo']) . '</h5>';
                echo '  <p><strong>Género:</strong> ' . htmlspecialchars($row['genero']) . '</p>';
                echo '  <p><strong>Año:</strong> ' . htmlspecialchars($row['anio']) . '</p>';
                echo '  <p class="descripcion">' . htmlspecialchars(substr($row['descripcion'], 0, 100)) . '...</p>';
                echo '  <div class="d-flex justify-content-between mt-3">';

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