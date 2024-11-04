<?php
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

$id = $_GET['id'] ?? '';

$query = "SELECT * FROM receta_catalogo WHERE id = '$id'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Receta no encontrada.";
    exit;
}

$receta = mysqli_fetch_assoc($result);
?>

<h3><?= $receta['nombre'] ?></h3>
<p><strong>Descripción:</strong> <?= $receta['descripcion'] ?></p>
<p><strong>Tipo de comida:</strong> <?= $receta['tipo_comida'] ?></p>
<p><strong>Porciones:</strong> <?= $receta['porciones'] ?></p>
<p><strong>Tiempo de preparación:</strong> <?= $receta['tiempo_preparacion'] ?></p>
<p><strong>Tiempo de cocción:</strong> <?= $receta['tiempo_coccion'] ?></p>

<?php if (!empty($receta['imagen'])): ?>
    <img src="images/<?= $receta['imagen'] ?>" class="img-fluid mt-3" alt="<?= $receta['nombre'] ?>">
<?php endif; ?>

<!-- Botón para ver video si está disponible -->
<?php if (!empty($receta['video_url'])): ?>
    <p class="mt-3">
        <a href="<?= $receta['video_url'] ?>" target="_blank" class="btn btn-info">Ver Video</a>
    </p>
<?php endif; ?>

<!-- Enlace a recursos adicionales -->
<?php if (!empty($receta['recurso_url'])): ?>
    <p class="mt-3">
        <a href="<?= $receta['recurso_url'] ?>" target="_blank" class="btn btn-secondary">Ver Recurso Adicional</a>
    </p>
<?php endif; ?>

<!-- Botón para descargar receta como PDF -->
<a href="descargar_pdf.php?tipo=receta&id=<?= $receta['id'] ?>" class="btn btn-success mt-3">Descargar PDF</a>

<?php mysqli_close($con); ?>
