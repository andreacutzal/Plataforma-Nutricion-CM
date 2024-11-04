<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

if (isset($_POST['submit_recomendacion'])) {
    $pid = $_POST['pid'];
    $nivel_estres = $_POST['nivel_estres'];
    $recomendaciones = $_POST['recomendaciones'];
    $video_url = $_POST['video_url'];

    $query = "INSERT INTO gestion_estres (pid, nivel_estres, recomendaciones, video_url, fecha) 
              VALUES ('$pid', '$nivel_estres', '$recomendaciones', '$video_url', NOW())";

    if (mysqli_query($con, $query)) {
        echo "<script>alert('Recomendación agregada con éxito.');</script>";
    } else {
        echo "<script>alert('Error al agregar la recomendación.');</script>";
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Agregar Recomendaciones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/gestion_estres_doctor.css">
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
    <h2 class="text-center">Agregar Recomendación y Video</h2>

    <form method="POST" action="gestion_estres_doctor.php">
        <div class="form-group">
            <label for="pid">ID del Paciente:</label>
            <input type="number" class="form-control" id="pid" name="pid" required>
        </div>
        <div class="form-group">
            <label for="nivel_estres">Nivel de Estrés:</label>
            <input type="number" class="form-control" id="nivel_estres" name="nivel_estres" required>
        </div>
        <div class="form-group">
            <label for="recomendaciones">Recomendaciones:</label>
            <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="video_url">Enlace del Video:</label>
            <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://youtube.com/..." required>
        </div>
        <button type="submit" name="submit_recomendacion" class="btn btn-primary btn-block">Agregar Recomendación</button>
    </form>
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
