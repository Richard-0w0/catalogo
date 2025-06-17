<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['es_admin']) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Películas Favoritas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="container mt-4">
    <h1 class="fw-bold text-center mb-4">❤️ Mis Películas Favoritas</h1>
    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">← Volver al catálogo</a>
    </div>
    
    <?php
    $sql = "SELECT p.* FROM peliculas p
            JOIN favoritas f ON p.id = f.pelicula_id
            WHERE f.usuario_id = " . $_SESSION['usuario_id'] . "
            ORDER BY p.anio DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="pelicula favorita">
                        <img src="images/<?= $row['imagen'] ?>" alt="<?= $row['titulo'] ?>">
                        <h5 class="mt-2 fw-bold"><?= $row['titulo'] ?></h5>
                        <p><strong>Género:</strong> <?= $row['genero'] ?></p>
                        <p><strong>Año:</strong> <?= $row['anio'] ?></p>
                        <p class="descripcion"><?= substr($row['descripcion'], 0, 100) ?>...</p>
                        <form method="POST" action="quitar_favorita.php" class="mt-3">
                            <input type="hidden" name="pelicula_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm btn-favorito w-100">❌ Quitar de Favoritas</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="py-5">
                <h4 class="text-muted">No tienes películas favoritas aún</h4>
                <p class="text-muted">Agrega algunas películas a tus favoritos desde el catálogo</p>
                <a href="index.php" class="btn btn-primary mt-3">Explorar Catálogo</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>