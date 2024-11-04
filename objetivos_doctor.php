<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

$pid = '';  // Variable para almacenar el PID ingresado.
$result = null;

if (isset($_POST['agregar_objetivo'])) {
    $pid = $_POST['pid'];
    $objetivo = $_POST['objetivo'];
    $fecha_inicio = $_POST['fecha'];
    $fecha_fin = $_POST['fecha_fin'];

    $query = "INSERT INTO objetivo_logrado (pid, objetivo, estado, fecha, fecha_fin) 
              VALUES ('$pid', '$objetivo', 'Pendiente', '$fecha_inicio', '$fecha_fin')";

    if (mysqli_query($con, $query)) {
        echo "<div class='alert alert-success' role='alert'>¡Objetivo asignado con éxito!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error al asignar el objetivo.</div>";
    }
}

if (isset($_POST['buscar_pid'])) {
    $pid = $_POST['buscar_pid'];
    $query = "SELECT * FROM objetivo_logrado 
              INNER JOIN paciente ON objetivo_logrado.pid = paciente.pid 
              WHERE objetivo_logrado.pid = '$pid'";
    $result = mysqli_query($con, $query);
} else {
    $query = "SELECT * FROM objetivo_logrado 
              INNER JOIN paciente ON objetivo_logrado.pid = paciente.pid";
    $result = mysqli_query($con, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Objetivos - Doctor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/objetivos_logrados_doctor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Encabezado -->
<div class="header d-flex justify-content-between align-items-center p-3">
    <div class="d-flex align-items-center">
    <a></i>CLINICA CM</a>
        <a href="doctor-panel.php" class="btn btn-link text-white">← Regresar al Panel Principal</a>
    </div>
    <a href="logout.php" class="btn btn-link text-white">Logout</a>
</div>

<div class="container mt-4">
    <h2>Asignar Objetivo a Paciente</h2>

    <form method="POST" action="objetivos_doctor.php">
        <div class="form-group">
            <label for="pid">ID del Paciente:</label>
            <input type="number" class="form-control" id="pid" name="pid" required>
        </div>
        <div class="form-group">
            <label for="objetivo">Descripción del Objetivo:</label>
            <input type="text" class="form-control" id="objetivo" name="objetivo" required>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha de Inicio:</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha Límite:</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
        </div>
        <button type="submit" name="agregar_objetivo" class="btn btn-success btn-block">Asignar Objetivo</button>
    </form>

    <hr>

    <h2>Buscar Progreso de un Paciente</h2>
    <form method="POST" action="objetivos_doctor.php" class="form-inline justify-content-center mb-4">
        <input type="number" class="form-control mr-2" name="buscar_pid" placeholder="Ingrese ID del Paciente" required>
        <button type="submit" class="btn btn-info">Buscar</button>
    </form>

    <h2>Progreso de Objetivos</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Paciente</th>
                <th>Nombre del Paciente</th>
                <th>Objetivo</th>
                <th>Estado</th>
                <th>Fecha Inicio</th>
                <th>Fecha Límite</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['pid'] ?></td>
                        <td><?= $row['fname'] . " " . $row['lname'] ?></td>
                        <td><?= $row['objetivo'] ?></td>
                        <td><?= $row['estado'] ?></td>
                        <td><?= $row['fecha'] ?></td>
                        <td><?= $row['fecha_fin'] ?? 'N/A' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron objetivos para este paciente.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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
