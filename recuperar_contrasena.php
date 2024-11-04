<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (!$con) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

// Inicializar variables
$error = '';
$success = '';
$step = 1; // Pasos del proceso: 1 = solicitud de correo, 2 = ingreso del código, 3 = nueva contraseña

// Paso 1: Solicitud de correo electrónico
if (isset($_POST['request_reset'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $query = "SELECT * FROM paciente WHERE email = '$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Generar código de verificación simulado
        $_SESSION['verification_code'] = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        
        // Simulación de envío de email (en producción, enviar el código por email)
        $success = "Se ha enviado un código de verificación a su correo electrónico.";
        $step = 2;
    } else {
        $error = "El correo electrónico no está registrado.";
    }
}

// Paso 2: Verificación del código
if (isset($_POST['verify_code'])) {
    $code = $_POST['verification_code'];
    if ($code == $_SESSION['verification_code']) {
        $success = "Código verificado. Ahora puede ingresar una nueva contraseña.";
        $step = 3;
    } else {
        $error = "El código de verificación es incorrecto.";
        $step = 2;
    }
}

// Paso 3: Restablecimiento de la contraseña
if (isset($_POST['reset_password'])) {
    $new_password = mysqli_real_escape_string($con, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
    
    if ($new_password === $confirm_password) {
        // Actualizar la contraseña en la base de datos
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];
        
        $query = "UPDATE paciente SET password = '$hashed_password' WHERE email = '$email'";
        if (mysqli_query($con, $query)) {
            $success = "Su contraseña ha sido restablecida correctamente.";
            session_unset();
            session_destroy();
            $step = 1; // Reiniciar el proceso
        } else {
            $error = "Error al actualizar la contraseña.";
        }
    } else {
        $error = "Las contraseñas no coinciden.";
        $step = 3;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body style="background: -webkit-linear-gradient(left, #3931af, #00c6ff); color: white;">
    <div class="container" style="margin-top: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card" style="background-color: #f8f9fa; color: black; padding: 20px;">
                    <h3 class="text-center">Recuperar Contraseña</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <?php if ($step == 1): ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <button type="submit" name="request_reset" class="btn btn-primary btn-block">Enviar Código</button>
                        </form>
                    
                    <?php elseif ($step == 2): ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="verification_code">Código de Verificación</label>
                                <input type="text" name="verification_code" class="form-control" required>
                            </div>
                            <button type="submit" name="verify_code" class="btn btn-primary btn-block">Verificar Código</button>
                        </form>
                    
                    <?php elseif ($step == 3): ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="new_password">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn btn-primary btn-block">Restablecer Contraseña</button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center" style="margin-top: 15px;">
                        <a href="index1.php">Volver al Inicio de Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
