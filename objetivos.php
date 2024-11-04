<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// Verificar si la sesión tiene el `pid` del paciente.
if (!isset($_SESSION['pid'])) {
    echo "<script>alert('Por favor, inicie sesión.');</script>";
    header("Location: login.php");
    exit();
}

$pid = $_SESSION['pid'];

// Actualizar el progreso del objetivo
if (isset($_POST['actualizar_estado'])) {
    if (isset($_POST['objetivos'])) {
        foreach ($_POST['objetivos'] as $id => $estado) {
            $query = "UPDATE objetivo_logrado SET estado = '$estado' WHERE id = '$id'";
            mysqli_query($con, $query);
        }
        echo "<script>alert('Progreso actualizado con éxito.');</script>";
    } else {
        echo "<script>alert('No se seleccionaron objetivos.');</script>";
    }
}

// Obtener los objetivos asignados al paciente
$query = "SELECT * FROM objetivo_logrado WHERE pid = '$pid'";
$result = mysqli_query($con, $query);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Calcular el progreso general
$total = count($data);
$completados = count(array_filter($data, fn($o) => $o['estado'] === 'Completado'));
$progreso = $total > 0 ? ($completados / $total) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mis Objetivos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/objetivos_logrados_paciente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h2 class="text-center">Mis Objetivos</h2>

    <form method="POST" action="objetivos.php">
        <ul class="list-group">
            <?php foreach ($data as $row): 
                $dias_restantes = (strtotime($row['fecha_fin']) - strtotime(date('Y-m-d'))) / 86400;
            ?>
                <li class="list-group-item">
                    <strong><?= $row['objetivo'] ?></strong>
                    <span class="badge badge-info float-end">
                        <?= $dias_restantes > 0 ? "$dias_restantes días restantes" : "Fecha límite alcanzada" ?>
                    </span>

                    <select name="objetivos[<?= $row['id'] ?>]" class="form-control mt-2">
                        <option value="Pendiente" <?= $row['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En progreso" <?= $row['estado'] == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                        <option value="Completado" <?= $row['estado'] == 'Completado' ? 'selected' : '' ?>>Completado</option>
                    </select>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="submit" name="actualizar_estado" class="btn btn-primary btn-block mt-3">Guardar Progreso</button>
    </form>

    <h3 class="text-center mt-5">Progreso General</h3>
    <div class="progress mb-3">
        <div class="progress-bar" role="progressbar" style="width: <?= $progreso ?>%;" 
             aria-valuenow="<?= $progreso ?>" aria-valuemin="0" aria-valuemax="100">
            <?= round($progreso) ?>%
        </div>
    </div>
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

<?php mysqli_close($con); ?>
