<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

$pid = $_SESSION['pid'];  // ID del paciente desde la sesión

// Verificar conexión
if (mysqli_connect_errno()) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener las recetas asignadas al paciente
$query = "SELECT r.id, r.nombre, r.tipo_comida, r.objetivo, r.ingredientes, r.instrucciones, r.multimedia 
          FROM planes_alimenticios p 
          INNER JOIN recetas r ON p.receta_id = r.id 
          WHERE p.pid = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mi Plan Alimenticio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/recetas_comidas_paciente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Encabezado -->
<div class="header d-flex justify-content-between align-items-center p-3">
    <div class="d-flex align-items-center">
    <a></i>CLINICA CM</a>
        <a href="admin-panel.php" class="btn btn-link text-white">← Regresar al Panel Principal</a>
    </div>
    <a href="logout.php" class="btn btn-link text-white">Logout</a>
</div>
<div class="container mt-4">
    <h2 class="text-center">Mi Plan Alimenticio</h2>

    <?php if ($result->num_rows > 0): ?>
        <ul class="list-group mb-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <h5><?= htmlspecialchars($row['nombre']) ?> (<?= htmlspecialchars($row['tipo_comida']) ?>)</h5>
                    <p><strong>Objetivo:</strong> <?= htmlspecialchars($row['objetivo']) ?></p>
                    <p><strong>Ingredientes:</strong> <?= nl2br(htmlspecialchars($row['ingredientes'])) ?></p>
                    <p><strong>Instrucciones:</strong> <?= nl2br(htmlspecialchars($row['instrucciones'])) ?></p>

                    <?php if (!empty($row['multimedia'])): ?>
                        <a href="<?= htmlspecialchars($row['multimedia']) ?>" target="_blank" class="btn btn-info btn-sm mt-2">Ver Multimedia</a>
                    <?php endif; ?>

                    <a href="descargar_pdf.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm mt-2">Descargar en PDF</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-center">No tienes recetas asignadas por el momento.</p>
    <?php endif; ?>
</div>

<!-- Pie de página -->
<footer class="footer mt-5 py-3 text-white text-center">
    <p>&copy; 2024 CLINICA CM - Todos los derechos reservados</p>
    <a href="https://wa.me/50254184347" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp|
            <a href="mailto:med12beagonzales@gmail.com.com">
            <i class="fas fa-envelope"></i> Correo electrónico
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>
