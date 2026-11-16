<?php

include "conexion.php";
include "funciones.php";


// COMPROBAR QUE SE HA ENVIADO EL FORMULARIO

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: registro.html");
    exit;
}


$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";


// COMPROBAR CAMPOS

if (empty($nombre) || empty($email) || empty($password)) {

    mostrar_error(
        "Faltan datos",
        "Todos los campos son obligatorios.",
        "registro.html",
        "Volver al registro"
    );
}


// COMPROBAR EMAIL

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    mostrar_error(
        "Email no válido",
        "Introduce una dirección de correo válida.",
        "registro.html",
        "Volver al registro"
    );
}


// COMPROBAR LONGITUD DE LA CONTRASEÑA

if (strlen($password) < 8) {

    mostrar_error(
        "Contraseña demasiado corta",
        "La contraseña debe tener al menos 8 caracteres.",
        "registro.html",
        "Volver al registro"
    );
}


// COMPROBAR SI EL EMAIL YA EXISTE

$sql_comprobar = "SELECT id
                  FROM usuarios
                  WHERE email = ?";

$stmt_comprobar = $conexion->prepare($sql_comprobar);

$stmt_comprobar->bind_param("s", $email);

$stmt_comprobar->execute();

$resultado = $stmt_comprobar->get_result();

if ($resultado->num_rows > 0) {

    mostrar_error(
        "Cuenta ya existente",
        "Ya existe una cuenta registrada con ese correo electrónico.",
        "login.html",
        "Iniciar sesión"
    );
}

$stmt_comprobar->close();


// CIFRAR CONTRASEÑA

$password_cifrada = password_hash($password, PASSWORD_DEFAULT);


// GUARDAR USUARIO

$sql = "INSERT INTO usuarios (nombre, email, password)
        VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "sss",
    $nombre,
    $email,
    $password_cifrada
);

$creado = $stmt->execute();

$stmt->close();

$conexion->close();


if (!$creado) {

    mostrar_error(
        "Error",
        "No se ha podido crear la cuenta. Inténtalo más tarde.",
        "registro.html",
        "Volver al registro"
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cuenta creada - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>

        <h1>GymManager</h1>

        <?php mostrar_menu(); ?>

    </header>


    <main class="reserva">

        <h2>¡Cuenta creada correctamente!</h2>

        <p>
            Ya puedes iniciar sesión con tu correo y tu contraseña.
        </p>

        <a href="login.html" class="boton">
            Iniciar sesión
        </a>

    </main>


    <footer>

        <p>© 2026 GymManager</p>

    </footer>

</body>

</html>