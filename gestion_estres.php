<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

$pid = $_SESSION['pid'];

// Consulta para obtener todas las recomendaciones acumuladas con video
$query = "SELECT nivel_estres, recomendaciones, video_url, fecha 
          FROM gestion_estres WHERE pid = '$pid' ORDER BY fecha ASC";
$result = mysqli_query($con, $query);

$fechas = [];
$niveles = [];
$recomendaciones = [];

while ($row = mysqli_fetch_assoc($result)) {
    $fechas[] = $row['fecha'];
    $niveles[] = $row['nivel_estres'];
    $recomendaciones[] = [
        'fecha' => $row['fecha'],
        'nivel' => $row['nivel_estres'],
        'recomendacion' => $row['recomendaciones'],
        'video_url' => $row['video_url']
    ];
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión del Estrés</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/gestion_estres_paciente.css">
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
    <h2 class="text-center">Gestión del Estrés</h2>

    <!-- Gráfica de evolución del estrés -->
    <div class="card mb-4">
        <div class="card-body">
            <canvas id="graficaEstres"></canvas>
        </div>
    </div>

    <!-- Recomendaciones y videos -->
    <h4 class="mt-4">Recomendaciones Acumuladas</h4>
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th>Nivel de Estrés</th>
                <th>Recomendaciones</th>
                <th>Video</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recomendaciones as $rec): ?>
                <tr>
                    <td><?= $rec['fecha'] ?></td>
                    <td><?= $rec['nivel'] ?></td>
                    <td><?= $rec['recomendacion'] ?></td>
                    <td>
                        <?php if (!empty($rec['video_url'])): ?>
                            <a href="<?= $rec['video_url'] ?>" target="_blank" class="btn btn-info">Ver Video</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Ejercicio de respiración guiada -->
    <div class="mt-4">
        <h4>Ejercicio de Respiración Guiada</h4>
        <button class="btn btn-info" onclick="iniciarRespiracion()">Comenzar</button>
        <p id="indicadorRespiracion" class="mt-3"></p>
    </div>
</div>

<!-- Pie de página -->
<footer class="footer mt-5 py-3 bg-primary text-white text-center">
        <p>&copy; 2024 CLINICA CM - Todos los derechos reservados</p>
        <a href="https://wa.me/50254184347" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp|
            <a href="mailto:med12beagonzales@gmail.com.com">
            <i class="fas fa-envelope"></i> Correo electrónico
</footer>

<script>
    const ctx = document.getElementById('graficaEstres').getContext('2d');
    const graficaEstres = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($fechas) ?>,
            datasets: [{
                label: 'Nivel de Estrés',
                data: <?= json_encode($niveles) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { title: { display: true, text: 'Fecha' } },
                y: { title: { display: true, text: 'Nivel de Estrés' } }
            }
        }
    });

    function iniciarRespiracion() {
        const indicador = document.getElementById('indicadorRespiracion');
        let fase = 'Inhalar';
        let contador = 0;

        indicador.textContent = `${fase}...`;
        const interval = setInterval(() => {
            contador++;
            if (contador % 4 === 0) {
                fase = (fase === 'Inhalar') ? 'Exhalar' : 'Inhalar';
                indicador.textContent = `${fase}...`;
            }
            if (contador >= 16) {
                clearInterval(interval);
                indicador.textContent = 'Ejercicio completado.';
            }
        }, 1000);
    }
</script>

</body>

</html>
