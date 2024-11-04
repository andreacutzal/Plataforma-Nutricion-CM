<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

$pid = $_SESSION['pid'];  // ID del paciente

if (isset($_POST['enviar_evaluacion'])) {
    $motivo = $_POST['motivo'];
    $objetivo = $_POST['objetivo'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $presion = $_POST['presion'];
    $azucar = $_POST['azucar'];
    $alergias = implode(', ', $_POST['alergias'] ?? []);
    $enfermedades = $_POST['enfermedades'];
    $habitos = implode(', ', $_POST['habitos'] ?? []);
    $preferencias = $_POST['preferencias'];
    $expectativas = $_POST['expectativas'];
    $fecha = date('Y-m-d');
    
    // Manejo de la subida de imagen
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoNombre = 'fotos/' . uniqid() . '-' . $_FILES['foto']['name'];
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $fotoNombre)) {
            $foto = $fotoNombre;
        }
    }

    $query = "INSERT INTO evaluacion_paciente(pid, motivo_consulta, objetivo_principal, peso_inicial, altura, presion_arterial, 
              nivel_azucar, alergias, enfermedades, habitos, preferencias, expectativas, fecha_evaluacion, foto) 
              VALUES ('$pid', '$motivo', '$objetivo', '$peso', '$altura', '$presion', '$azucar', '$alergias', 
                      '$enfermedades', '$habitos', '$preferencias', '$expectativas', '$fecha', '$foto')";

    if (mysqli_query($con, $query)) {
        echo "<div class='alert alert-success'>Evaluación enviada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al enviar la evaluación.</div>";
    }
}

// Consultar el análisis y diagnóstico del paciente
$query_diagnostico = "SELECT analisis_diagnostico, foto FROM evaluacion_paciente WHERE pid = ? ORDER BY fecha_evaluacion DESC LIMIT 1";
$stmt = $con->prepare($query_diagnostico);
$stmt->bind_param("i", $pid);
$stmt->execute();
$stmt->bind_result($analisis_diagnostico, $foto);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación Médica</title>
    <!-- Asegúrate de que el archivo CSS está en la ruta correcta -->
    <link rel="stylesheet" href="css/evaluacion.css"> <!-- Ajusta la ruta si es necesario -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
    <div class="card p-4 shadow-sm">
        <div class="row">
            <div class="col-md-3 text-center">
                <!-- Foto del paciente con previsualización -->
                <img id="fotoPreview" src="<?php echo $foto ? $foto : 'path/to/default_photo.jpg'; ?>" alt="Foto del Paciente" class="img-fluid mb-3" width="150" height="200">
                <input type="file" name="foto" id="foto" class="form-control-file mb-2" accept="image/*" onchange="previewImage(event)">
            </div>
            <div class="col-md-9">
                <h2 class="text-center mb-4">Evaluación Médica</h2>
                <form method="POST" enctype="multipart/form-data" class="p-3 bg-light rounded">
                    <div class="form-group">
                        <label for="motivo">Motivo de Consulta</label>
                        <textarea name="motivo" id="motivo" class="form-control mb-2" placeholder="Motivo de Consulta" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="objetivo">Objetivo Principal</label>
                        <select name="objetivo" id="objetivo" class="form-control mb-2" required>
                            <option value="Bajar Peso">Bajar Peso</option>
                            <option value="Ganar Masa Muscular">Ganar Masa Muscular</option>
                            <option value="Mejorar la Alimentación">Mejorar la Alimentación</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="peso">Peso Inicial (kg)</label>
                                <input type="number" name="peso" id="peso" class="form-control mb-2" placeholder="Peso Inicial (kg)" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="altura">Altura (cm)</label>
                                <input type="number" name="altura" id="altura" class="form-control mb-2" placeholder="Altura (cm)" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="presion">Presión Arterial</label>
                        <input type="text" name="presion" id="presion" class="form-control mb-2" placeholder="Presión Arterial">
                    </div>

                    <div class="form-group">
                        <label for="azucar">Nivel de Azúcar</label>
                        <input type="text" name="azucar" id="azucar" class="form-control mb-2" placeholder="Nivel de Azúcar">
                    </div>

                    <div class="form-group">
                        <label>Seleccione Alergias</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="alergias[]" value="Gluten" id="alergia1">
                            <label class="form-check-label" for="alergia1">Gluten</label><br>
                            <input type="checkbox" class="form-check-input" name="alergias[]" value="Lácteos" id="alergia2">
                            <label class="form-check-label" for="alergia2">Lácteos</label><br>
                            <input type="checkbox" class="form-check-input" name="alergias[]" value="Frutos Secos" id="alergia3">
                            <label class="form-check-label" for="alergia3">Frutos Secos</label><br>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="enfermedades">Enfermedades Crónicas</label>
                        <textarea name="enfermedades" id="enfermedades" class="form-control mb-2" placeholder="Enfermedades Crónicas"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Seleccione Hábitos</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="habitos[]" value="Ejercicio Diario" id="habito1">
                            <label class="form-check-label" for="habito1">Ejercicio Diario</label><br>
                            <input type="checkbox" class="form-check-input" name="habitos[]" value="Sueño Regular" id="habito2">
                            <label class="form-check-label" for="habito2">Sueño Regular</label><br>
                            <input type="checkbox" class="form-check-input" name="habitos[]" value="Consumo de Agua Adecuado" id="habito3">
                            <label class="form-check-label" for="habito3">Consumo de Agua Adecuado</label><br>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="preferencias">Preferencias y Restricciones</label>
                        <textarea name="preferencias" id="preferencias" class="form-control mb-2" placeholder="Preferencias y Restricciones"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="expectativas">Expectativas y Dudas</label>
                        <textarea name="expectativas" id="expectativas" class="form-control mb-2" placeholder="Expectativas y Dudas"></textarea>
                    </div>

                    <button type="submit" name="enviar_evaluacion" class="btn btn-primary btn-block">Enviar Evaluación</button>
                </form>
            </div>
        </div>

        <hr>

        <h4>Análisis y Diagnóstico Recibido:</h4>
        <div>
            <?php echo isset($analisis_diagnostico) ? nl2br(htmlspecialchars($analisis_diagnostico)) : "Aún no hay análisis y diagnóstico registrado."; ?>
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

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('fotoPreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>

<?php mysqli_close($con); ?>
