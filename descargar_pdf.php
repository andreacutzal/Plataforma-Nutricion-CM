<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

// Conexión a la base de datos
$con = mysqli_connect("localhost", "root", "", "myhmsdb");
if (mysqli_connect_errno()) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID de la receta
$id = $_GET['id'] ?? '';

$query = "SELECT * FROM recetas WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Receta no encontrada.");
}

$receta = $result->fetch_assoc();

// Contenido HTML para el PDF
$html = "
    <h1 style='text-align: center;'>{$receta['nombre']}</h1>
    <p><strong>Descripción:</strong> {$receta['objetivo']}</p>
    <p><strong>Tipo de comida:</strong> {$receta['tipo_comida']}</p>
    <p><strong>Ingredientes:</strong> {$receta['ingredientes']}</p>
    <p><strong>Instrucciones:</strong> {$receta['instrucciones']}</p>
";

if (!empty($receta['multimedia'])) {
    $html .= "<p><strong>Multimedia:</strong> <a href='{$receta['multimedia']}'>Ver Multimedia</a></p>";
}

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar el PDF
$dompdf->stream("receta_{$receta['id']}.pdf", ["Attachment" => 1]);

$stmt->close();
mysqli_close($con);
?>
