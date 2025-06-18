<?php
session_start();
include('conexion.php');
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$es_admin = $_SESSION['es_admin'] ?? 0;
// Eliminar comentario (solo admin puede eliminar cualquier comentario)
if (isset($_POST['eliminar_comentario']) && isset($_POST['comentario_id'])) {
    $comentario_id = intval($_POST['comentario_id']);
    $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ?");
    $stmt->bind_param("i", $comentario_id);
    $stmt->execute();
    $stmt->close();
    header("Location: lista_comentarios.php");
    exit();
}

// Obtener todos los comentarios con usuario y película
$sql = "SELECT c.id, c.comentario, c.fecha, u.usuario, p.titulo, p.id as pelicula_id
        FROM comentarios c
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN peliculas p ON c.pelicula_id = p.id
        ORDER BY c.fecha DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Todos los Comentarios</title>
    <link rel="stylesheet" href="estilos.css">
    
</head>
<body style="background:#181a1b;">
<div class="container" style="max-width:800px;margin:40px auto 0 auto;">
    <button onclick="location.href='index.php'" class="btn btn-warning">⬅ Volver al Catálogo</button>
    <h1 class="fw-bold text-center mb-4" style="color:#fff;">🗒️ Todos los Comentarios</h1>
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="comentario-card">';
            echo '<div class="usuario">Usuario: ' . htmlspecialchars($row['usuario']) . '</div>';
            echo '<div class="pelicula">Película: ' . htmlspecialchars($row['titulo']) . ' (ID: ' . $row['pelicula_id'] . ')</div>';
            echo '<div class="fecha">' . $row['fecha'] . '</div>';
            echo '<div style="margin:10px 0 12px 0;">' . htmlspecialchars($row['comentario']) . '</div>';
            echo '<form method="POST" action="lista_comentarios.php" style="display:inline;">';
            echo '<input type="hidden" name="comentario_id" value="' . $row['id'] . '">';
            echo '<button type="submit" name="eliminar_comentario" class="btn btn-danger" onclick="return confirm(\'¿Eliminar este comentario?\')">Eliminar</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo '<div class="text-muted text-center">No hay comentarios publicados aún.</div>';
    }
    ?>
</div>
</body>
</html>
<?php
$conn->close();