<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Eliminar comentario
if (isset($_POST['eliminar_comentario']) && isset($_POST['comentario_id'])) {
    $comentario_id = intval($_POST['comentario_id']);
    $stmt = $conn->prepare("DELETE FROM comentarios WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $comentario_id, $usuario_id);
    $stmt->execute();
    $stmt->close();
    header("Location: mis_comentarios.php");
    exit();
}

// Editar comentario
if (isset($_POST['editar_comentario']) && isset($_POST['comentario_id']) && isset($_POST['nuevo_texto'])) {
    $comentario_id = intval($_POST['comentario_id']);
    $nuevo_texto = trim($_POST['nuevo_texto']);
    if ($nuevo_texto !== "") {
        $stmt = $conn->prepare("UPDATE comentarios SET comentario = ? WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("sii", $nuevo_texto, $comentario_id, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: mis_comentarios.php");
    exit();
}

// Obtener comentarios del usuario
$sql = "SELECT c.id, c.comentario, c.fecha, p.titulo, p.id as pelicula_id
        FROM comentarios c
        JOIN peliculas p ON c.pelicula_id = p.id
        WHERE c.usuario_id = ?
        ORDER BY c.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Comentarios</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#181a1b;">
<div class="container" style="max-width:700px;margin:40px auto 0 auto;">
    <a href="home.php" class="btn btn-warning">⬅ Volver al Catálogo</a>
    <h1 class="fw-bold text-center mb-4" style="color:#fff;">💬 Mis Comentarios</h1>
    <?php
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="comentario-card">';
            echo '<h5>Pelicula: ' . htmlspecialchars($row['titulo']) . ' (ID: ' . $row['pelicula_id'] . ')</h5>';
            echo '<div class="fecha">' . $row['fecha'] . '</div>';
            // Si se está editando este comentario
            if (isset($_GET['editar']) && $_GET['editar'] == $row['id']) {
                echo '<form method="POST" action="mis_comentarios.php">';
                echo '<input type="hidden" name="comentario_id" value="' . $row['id'] . '">';
                echo '<input type="text" name="nuevo_texto" value="' . htmlspecialchars($row['comentario']) . '" required maxlength="255">';
                echo '<button type="submit" name="editar_comentario" class="btn btn-primary">Guardar</button>';
                echo '</form>';
                echo '<a href="mis_comentarios.php" class="btn btn-danger">Cancelar</a>';
            } else {
                echo '<div style="margin:10px 0 12px 0;">' . htmlspecialchars($row['comentario']) . '</div>';
                echo '<form method="GET" action="mis_comentarios.php" style="display:inline;">';
                echo '<input type="hidden" name="editar" value="' . $row['id'] . '">';
                echo '<button type="submit" class="btn btn-primary">Editar</button>';
                echo '</form>';
                echo '<form method="POST" action="mis_comentarios.php" style="display:inline;">';
                echo '<input type="hidden" name="comentario_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="eliminar_comentario" class="btn btn-danger" onclick="return confirm(\'¿Eliminar este comentario?\')">Eliminar</button>';
                echo '</form>';
            }
            echo '</div>';
        }
    } else {
        echo '<div class="text-muted text-center">No has publicado comentarios aún.</div>';
    }
    ?>
</div>
</body>
</html>
<?php
$stmt->close();
?>