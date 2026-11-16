<?php

session_start();

include "conexion.php";
include "funciones.php";


// -----------------------------------------
// COMPROBAR QUE SE HA ENVIADO EL FORMULARIO
// -----------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: contacto.php");
    exit;

}


// -----------------------------------------
// RECIBIR LOS DATOS
// -----------------------------------------

$nombre = trim($_POST["nombre"] ?? "");
$email = trim($_POST["email"] ?? "");
$mensaje = trim($_POST["mensaje"] ?? "");


// -----------------------------------------
// COMPROBAR CAMPOS OBLIGATORIOS
// -----------------------------------------

if (empty($nombre) || empty($email) || empty($mensaje)) {

    mostrar_error(
        "Faltan datos",
        "Todos los campos son obligatorios.",
        "contacto.php",
        "Volver a contacto"
    );

}


// -----------------------------------------
// COMPROBAR EMAIL
// -----------------------------------------

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    mostrar_error(
        "Email no válido",
        "Introduce una dirección de correo válida.",
        "contacto.php",
        "Volver a contacto"
    );

}


// -----------------------------------------
// GUARDAR EL MENSAJE
// -----------------------------------------

// Si el usuario ha iniciado sesión guardamos su id; si no, queda vacío

$usuario_id = $_SESSION["usuario_id"] ?? null;

$sql = "INSERT INTO mensajes (usuario_id, nombre, email, mensaje)
        VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "isss",
    $usuario_id,
    $nombre,
    $email,
    $mensaje
);

$guardado = $stmt->execute();

$stmt->close();

$conexion->close();


if (!$guardado) {

    mostrar_error(
        "Error",
        "No se ha podido enviar el mensaje. Inténtalo más tarde.",
        "contacto.php",
        "Volver a contacto"
    );

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mensaje enviado - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>GymManager</h1>

    <?php mostrar_menu(); ?>

</header>


<main class="reserva">

    <h2>¡Mensaje enviado correctamente!</h2>

    <p>
        Gracias, <?php echo htmlspecialchars($nombre); ?>.
    </p>

    <p>
        Hemos recibido tu mensaje y nos pondremos en contacto contigo.
    </p>

    <a href="contacto.php" class="boton">
        Volver a contacto
    </a>

</main>


<footer>

    <p>© 2026 GymManager</p>

</footer>

</body>

</html>