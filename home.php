<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener películas
$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM favoritas f WHERE f.pelicula_id = p.id AND f.usuario_id = ?) as es_favorita
        FROM peliculas p ORDER BY anio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Películas</title>
    <link rel="stylesheet" href="estilos.css">
    
</head>
<body style="background:#181a1b;">
    <!-- Hamburguesa y barra lateral -->
    <div style="position:fixed;top:18px;left:18px;z-index:1100;">
        <div class="hamburger" onclick="toggleSidebar()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar" id="sidebar">
        <span class="close-btn" onclick="toggleSidebar()">&times;</span>
        <a href="favoritas.php">❤️ Mis Favoritos</a>
        <a href="mis_comentarios.php">💬 Mis Comentarios</a>
        <a href="logout.php">🚪 Cerrar Sesión</a>
    </div>
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <div class="container mt-4" style="max-width:1100px;margin:auto;">
        <h1 class="fw-bold text-center mb-4" style="color:#fff;">🎬 Catálogo de Películas</h1>
        <div class="row">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $es_favorita = $row['es_favorita'] > 0;
                    echo '<div class="col-md-4 mb-4">';
                    echo '  <div class="pelicula' . ($es_favorita ? ' favorita' : '') . '" style="background:#232526;border-radius:14px;padding:18px;">';
                    echo '      <img src="images/' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['titulo']) . '" class="img-fluid" style="width:100%;max-width:220px;height:320px;object-fit:cover;border-radius:10px;background:#111;">';
                    echo '      <h5 class="mt-2 fw-bold" style="color:#fff;">' . htmlspecialchars($row['titulo']) . '</h5>';
                    echo '      <p style="color:#bdbdbd;"><strong>ID Película:</strong> ' . $row['id'] . '</p>';
                    echo '      <p style="color:#bdbdbd;"><strong>Género:</strong> ' . htmlspecialchars($row['genero']) . '</p>';
                    echo '      <p style="color:#bdbdbd;"><strong>Año:</strong> ' . htmlspecialchars($row['anio']) . '</p>';
                    echo '      <p class="descripcion" style="color:#e0e0e0;">' . htmlspecialchars(substr($row['descripcion'], 0, 100)) . '...</p>';
                    echo '      <div class="d-flex justify-content-between mt-3">';
                    if ($es_favorita) {
                        echo '<button class="btn btn-danger btn-sm w-100" disabled>❤️ En Favoritos</button>';
                    } else {
                        echo '<form method="POST" action="agregar_favorita.php" class="w-100">';
                        echo '<input type="hidden" name="pelicula_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" class="btn btn-outline-danger btn-sm w-100">🤍 Añadir a Favoritos</button>';
                        echo '</form>';
                    }
                    echo '      </div>';

                    // Sección de comentarios
                    echo '<div class="comentarios-box">';
                    echo '<strong>Comentarios:</strong><br>';
                    $comentarios_sql = "SELECT c.comentario, c.fecha, u.usuario FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.pelicula_id = " . $row['id'] . " ORDER BY c.fecha DESC";
                    $comentarios_result = $conn->query($comentarios_sql);
                    if ($comentarios_result && $comentarios_result->num_rows > 0) {
                        while ($comentario = $comentarios_result->fetch_assoc()) {
                            echo '<div class="mb-1">';
                            echo '<span class="comentario-usuario">' . htmlspecialchars($comentario['usuario']) . ':</span> ';
                            echo '<span class="comentario-texto">' . htmlspecialchars($comentario['comentario']) . '</span> ';
                            echo '<span class="comentario-fecha">(' . $comentario['fecha'] . ')</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="text-muted mb-2">Sin comentarios aún.</div>';
                    }
                    echo '          </div>'; // Cierre de comentarios-box
                    echo '          <form method="POST" action="comentario.php" class="comentario-form">';
                    echo '              <input type="hidden" name="pelicula_id" value="' . $row['id'] . '">';
                    echo '              <input type="text" name="comentario" placeholder="Escribe un comentario..." required maxlength="255">';
                    echo '              <button type="submit">Publicar</button>';
                    echo '          </form>';

                    echo '</div>'; // Cierre de div.pelicula
                    echo '</div>'; // Cierre de div.col-md-4
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No hay películas en el catálogo.</p></div>';
            }
            ?>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>