<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

// Subir archivo multimedia
function subirArchivo($file) {
    $nombreArchivo = basename($file['name']);
    $rutaDestino = 'uploads/' . $nombreArchivo;
    if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        return $rutaDestino;
    }
    return null;
}

if (isset($_POST['enviar'])) {
    $nombreAlimento = $_POST['nombre_alimento'];
    $calorias = $_POST['calorias'];
    $nutrientes = $_POST['nutrientes'];
    $alergenos = $_POST['alergenos'];

    $queryAlimento = "INSERT INTO alimentos (nombre, calorias, nutrientes, alergenos) 
                      VALUES ('$nombreAlimento', '$calorias', '$nutrientes', '$alergenos')";
    mysqli_query($con, $queryAlimento);

    $nombreReceta = $_POST['nombre_receta'];
    $tipoComida = $_POST['tipo_comida'];
    $objetivo = $_POST['objetivo'];
    $ingredientes = $_POST['ingredientes'];
    $instrucciones = $_POST['instrucciones'];
    $enlace = $_POST['enlace_externo'] ?? '';

    $multimedia = subirArchivo($_FILES['multimedia']);

    $queryReceta = "INSERT INTO recetas (nombre, tipo_comida, objetivo, ingredientes, instrucciones, multimedia, recurso_url) 
                    VALUES ('$nombreReceta', '$tipoComida', '$objetivo', '$ingredientes', '$instrucciones', '$multimedia', '$enlace')";
    mysqli_query($con, $queryReceta);

    $recetaId = mysqli_insert_id($con);

    $pid = $_POST['pid'];
    $fecha = $_POST['fecha'];

    $queryPlan = "INSERT INTO planes_alimenticios (pid, receta_id, fecha) 
                  VALUES ('$pid', '$recetaId', '$fecha')";
    mysqli_query($con, $queryPlan);

    echo "<div class='alert alert-success' role='alert'>¡Plan alimenticio asignado correctamente!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Recetas - Nutricionista</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/recetas_comidas_doctor.css">
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
    <h2 class="text-center">Gestión Completa: Alimentos, Recetas y Planes</h2>

    <form method="POST" enctype="multipart/form-data">
        <h4>Agregar Alimento</h4>
        <input type="text" name="nombre_alimento" class="form-control mb-2" placeholder="Nombre del Alimento" required>
        <input type="number" name="calorias" class="form-control mb-2" placeholder="Calorías" required>
        <textarea name="nutrientes" class="form-control mb-2" placeholder="Nutrientes"></textarea>
        <textarea name="alergenos" class="form-control mb-2" placeholder="Alérgenos"></textarea>

        <h4 class="mt-4">Agregar Receta</h4>
        <input type="text" name="nombre_receta" class="form-control mb-2" placeholder="Nombre de la Receta" required>
        <select name="tipo_comida" class="form-control mb-2" required>
            <option value="Desayuno">Desayuno</option>
            <option value="Almuerzo">Almuerzo</option>
            <option value="Cena">Cena</option>
            <option value="Snack">Snack</option>
        </select>
        <select name="objetivo" class="form-control mb-2" required>
            <option value="Bajar Peso">Bajar Peso</option>
            <option value="Mantener Peso">Mantener Peso</option>
            <option value="Ganar Masa Muscular">Ganar Masa Muscular</option>
        </select>
        <textarea name="ingredientes" class="form-control mb-2" placeholder="Ingredientes" required></textarea>
        <textarea name="instrucciones" class="form-control mb-2" placeholder="Instrucciones" required></textarea>
        <input type="file" name="multimedia" class="form-control mb-2">
        <input type="text" name="enlace_externo" class="form-control mb-2" placeholder="Enlace Externo (opcional)">

        <h4 class="mt-4">Asignar Plan Alimenticio</h4>
        <input type="number" name="pid" class="form-control mb-2" placeholder="ID del Paciente" required>
        <input type="date" name="fecha" class="form-control mb-2" required>

        <button type="submit" name="enviar" class="btn btn-success btn-block mt-4">Enviar Plan al Paciente</button>
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
