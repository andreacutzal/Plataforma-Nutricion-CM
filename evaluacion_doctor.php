<?php
session_start();

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

// Verificar la conexión
if (mysqli_connect_errno()) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Función para limpiar y validar entrada
function limpiar_entrada($entrada) {
    global $con;
    return mysqli_real_escape_string($con, htmlspecialchars(trim($entrada)));
}

$evaluacion_paciente = [];
$progreso_doctor = [];
$mensaje = '';

// Buscar paciente y cargar información
if (isset($_POST['buscar_paciente'])) {
    $pid = limpiar_entrada($_POST['pid']);

    // Obtener la evaluación inicial del paciente
    $query_evaluacion = "SELECT * FROM evaluacion_paciente WHERE pid = ?";
    $stmt_evaluacion = $con->prepare($query_evaluacion);
    $stmt_evaluacion->bind_param("i", $pid);
    $stmt_evaluacion->execute();
    $result_evaluacion = $stmt_evaluacion->get_result();
    $evaluacion_paciente = $result_evaluacion->fetch_assoc();

    // Obtener el historial del progreso del paciente
    $query_progreso = "SELECT peso_actual, notas, fecha_registro FROM evaluacion_doctor WHERE pid = ?";
    $stmt_progreso = $con->prepare($query_progreso);
    $stmt_progreso->bind_param("i", $pid);
    $stmt_progreso->execute();
    $result_progreso = $stmt_progreso->get_result();

    if ($result_progreso->num_rows > 0) {
        $progreso_doctor = $result_progreso->fetch_all(MYSQLI_ASSOC);
    }
}

// Registrar nuevo progreso
if (isset($_POST['registrar_progreso'])) {
    $pid = limpiar_entrada($_POST['pid']);
    $peso_actual = limpiar_entrada($_POST['peso_actual']);
    $notas = limpiar_entrada($_POST['notas']);
    $fecha = date('Y-m-d');

    $query_insertar = "INSERT INTO evaluacion_doctor (pid, peso_actual, notas, fecha_registro) 
                       VALUES (?, ?, ?, ?)";
    $stmt_insertar = $con->prepare($query_insertar);
    $stmt_insertar->bind_param("idss", $pid, $peso_actual, $notas, $fecha);

    if ($stmt_insertar->execute()) {
        $mensaje = "<div class='alert alert-success'>Progreso registrado correctamente.</div>";
        header("Refresh:0");
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al registrar el progreso: " . $stmt_insertar->error . "</div>";
    }
}

// Registrar análisis y diagnóstico inicial
if (isset($_POST['registrar_analisis'])) {
    $pid = limpiar_entrada($_POST['pid']);
    $analisis_diagnostico = limpiar_entrada($_POST['analisis_diagnostico']);

    $query_actualizar = "UPDATE evaluacion_paciente SET analisis_diagnostico = ? WHERE pid = ?";
    $stmt_actualizar = $con->prepare($query_actualizar);
    $stmt_actualizar->bind_param("si", $analisis_diagnostico, $pid);

    if ($stmt_actualizar->execute()) {
        $mensaje = "<div class='alert alert-success'>Análisis y diagnóstico inicial registrado correctamente.</div>";
        header("Refresh:0");
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al registrar el análisis y diagnóstico inicial: " . $stmt_actualizar->error . "</div>";
    }
}

// Cerrar conexión
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluación del Paciente</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/evaluacion_d.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <h2 class="text-center">Evaluación del Paciente</h2>

    <form method="POST" class="mb-4">
        <input type="number" name="pid" class="form-control mb-2" placeholder="ID del Paciente" required>
        <button type="submit" name="buscar_paciente" class="btn btn-primary btn-block">Buscar Paciente</button>
    </form>

    <?php if (!empty($evaluacion_paciente)): ?>
        <h4>Evaluación Inicial del Paciente</h4>
        <!-- Mostrar la foto del paciente -->
        <?php if (!empty($evaluacion_paciente['foto'])): ?>
            <img src="<?= htmlspecialchars($evaluacion_paciente['foto']) ?>" alt="Foto del Paciente" class="img-fluid mb-3" width="200">
        <?php else: ?>
            <p>No se ha cargado una foto del paciente.</p>
        <?php endif; ?>
        
        <p><strong>Motivo:</strong> <?= htmlspecialchars($evaluacion_paciente['motivo_consulta']) ?></p>
        <p><strong>Objetivo Principal:</strong> <?= htmlspecialchars($evaluacion_paciente['objetivo_principal']) ?></p>
        <p><strong>Peso Inicial:</strong> <?= htmlspecialchars($evaluacion_paciente['peso_inicial']) ?> kg</p>
    <?php endif; ?>

    <h4 class="mt-4">Análisis y Diagnóstico Inicial</h4>
    <form method="POST">
        <input type="hidden" name="pid" value="<?= isset($evaluacion_paciente['pid']) ? $evaluacion_paciente['pid'] : '' ?>">
        <textarea name="analisis_diagnostico" class="form-control mb-2" placeholder="Añadir análisis y diagnóstico inicial"></textarea>
        <button type="submit" name="registrar_analisis" class="btn btn-success btn-block">Registrar Análisis</button>
    </form>

    <?php echo $mensaje; ?>

    <?php if (!empty($progreso_doctor)): ?>
        <h4 class="mt-4">Seguimiento y Progreso</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Peso Actual (kg)</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progreso_doctor as $registro): ?>
                    <tr>
                        <td><?= htmlspecialchars($registro['fecha_registro']) ?></td>
                        <td><?= htmlspecialchars($registro['peso_actual']) ?></td>
                        <td><?= htmlspecialchars($registro['notas']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <canvas id="progresoChart" width="400" height="200"></canvas>

        <script>
            const fechas = <?= json_encode(array_column($progreso_doctor, 'fecha_registro')) ?>;
            const pesos = <?= json_encode(array_column($progreso_doctor, 'peso_actual')) ?>;

            new Chart(document.getElementById('progresoChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: [{
                        label: 'Peso (kg)',
                        data: pesos,
                        borderColor: 'blue',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Fecha' } },
                        y: { title: { display: true, text: 'Peso (kg)' } }
                    }
                }
            });
        </script>
    <?php endif; ?>
</div>
<footer class="footer">
    <p>&copy; 2024 CLINICA CM - Todos los derechos reservados</p>
    <a href="https://wa.me/50254184347" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp|
            <a href="mailto:med12beagonzales@gmail.com.com">
            <i class="fas fa-envelope"></i> Correo electrónico
</footer>

</body>
</html>
