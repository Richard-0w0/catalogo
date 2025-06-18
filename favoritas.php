<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener las películas favoritas del usuario
$sql = "SELECT p.*
        FROM peliculas p
        INNER JOIN favoritas f ON f.pelicula_id = p.id
        WHERE f.usuario_id = ?
        ORDER BY p.anio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Películas Favoritas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#181a1b;">
<div class="favoritas-container">
    <div class="topbar-btns">
        <a href="home.php" class="btn btn-warning">⬅ Volver al Catálogo</a>
    </div>
    <h1 class="fw-bold text-center mb-4" style="color:#fff;">❤️ Mis Películas Favoritas</h1>
    <div class="favoritas-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="pelicula-card">';
                echo '  <img src="images/' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['titulo']) . '">';
                echo '  <h5>' . htmlspecialchars($row['titulo']) . '</h5>';
                echo '  <p><strong>Género:</strong> ' . htmlspecialchars($row['genero']) . '</p>';
                echo '  <p><strong>Año:</strong> ' . htmlspecialchars($row['anio']) . '</p>';
                echo '  <p class="descripcion">' . htmlspecialchars(substr($row['descripcion'], 0, 100)) . '...</p>';
                echo '  <form method="POST" action="quitar_favorita.php" style="width:100%;">';
                echo '      <input type="hidden" name="pelicula_id" value="' . htmlspecialchars($row['id']) . '">';
                echo '      <button type="submit" class="btn-danger" style="width:100%;">❌ Eliminar de Favoritas</button>';
                echo '  </form>';
                echo '</div>';
            }
        } else {
            echo '<p style="color:#fff; text-align:center;">No tienes películas favoritas aún.</p>';
        }
        ?>
    </div>
</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>