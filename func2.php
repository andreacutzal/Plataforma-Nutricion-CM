<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "myhmsdb");

if (isset($_POST['patsub1'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Verificar si el correo electrónico ya está registrado
    $query_email_check = "SELECT * FROM paciente WHERE email = ?";
    $stmt_email_check = $con->prepare($query_email_check);
    $stmt_email_check->bind_param("s", $email);
    $stmt_email_check->execute();
    $result_email_check = $stmt_email_check->get_result();

    if ($result_email_check->num_rows > 0) {
        echo "<script>alert('El correo electrónico ya está registrado. Por favor, usa otro correo.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Validar que el número de contacto tenga exactamente 8 dígitos numéricos
    if (!preg_match('/^[2-9][0-9]{7}$/', $contact)) {
        echo "<script>alert('El número de contacto debe ser de 8 dígitos y comenzar con un dígito entre 2 y 9 (Guatemala).'); window.location.href = 'index.php';</script>";
        exit();
    }

    if ($password === $cpassword) {
        // Cifrar la contraseña antes de guardarla en la base de datos
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO paciente (fname, lname, gender, email, contact, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssss", $fname, $lname, $gender, $email, $contact, $hashed_password);
        $result = $stmt->execute();

        if ($result) {
            $_SESSION['username'] = "$fname $lname";
            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['gender'] = $gender;
            $_SESSION['contact'] = $contact;
            $_SESSION['email'] = $email;
            header("Location: admin-panel.php");
            exit();
        } else {
            echo "<script>alert('Error al registrar el usuario.');</script>";
        }
    } else {
        echo "<script>alert('Las contraseñas no coinciden.'); window.location.href = 'index.php';</script>";
    }
}
?>
