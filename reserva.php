<?php

session_start();

include "conexion.php";
include "funciones.php";

$clases = obtener_clases($conexion);


// -----------------------------------------
// COMPROBAR SESIÓN
// -----------------------------------------

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}


// -----------------------------------------
// DATOS DEL USUARIO
// -----------------------------------------

$nombre_usuario = $_SESSION["usuario_nombre"];
$email_usuario = $_SESSION["usuario_email"];

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Reserva - GymManager</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>GymManager</h1>

<?php mostrar_menu(); ?>

</header>

<main class="reserva">

<h2>Reserva tu clase</h2>

<p>
    Completa el formulario para reservar tu plaza.
</p>


<form
    action="guardar_reserva.php"
    method="post"
    id="formularioReserva"
>


    <!-- NOMBRE DEL USUARIO -->

    <label for="nombre">
        Nombre completo
    </label>

    <input
        type="text"
        id="nombre"
        name="nombre"
        value="<?php echo htmlspecialchars($nombre_usuario); ?>"
        readonly
        required
    >


    <!-- CORREO DEL USUARIO -->

    <label for="email">
        Correo electrónico
    </label>

    <input
        type="email"
        id="email"
        name="email"
        value="<?php echo htmlspecialchars($email_usuario); ?>"
        readonly
        required
    >


    <!-- CLASE (las opciones salen de la tabla clases) -->

    <label for="clase">
        Clase seleccionada
    </label>

    <select
        id="clase"
        name="clase"
        required
    >

        <option value="">
            -- Selecciona una clase --
        </option>

        <?php foreach ($clases as $id_clase => $datos): ?>

            <option value="<?php echo $id_clase; ?>">
                <?php echo htmlspecialchars(nombre_clase($id_clase)); ?>
                (<?php echo htmlspecialchars($datos["dias"]); ?>, <?php echo htmlspecialchars($datos["hora"]); ?>)
            </option>

        <?php endforeach; ?>

    </select>


    <!-- FECHA -->

    <label for="fecha">
        Fecha
    </label>

    <input
        type="date"
        id="fecha"
        name="fecha"
        required
    >


    <button
        type="submit"
        class="boton"
    >
        Confirmar reserva
    </button>


</form>


</main>


<footer>

    <p>© 2026 GymManager</p>

</footer>


<script>

// -----------------------------------------
// FECHA ACTUAL
// -----------------------------------------

const hoy = new Date();

const anio = hoy.getFullYear();

const mes = String(hoy.getMonth() + 1).padStart(2, "0");

const dia = String(hoy.getDate()).padStart(2, "0");

const fechaHoy = `${anio}-${mes}-${dia}`;


// -----------------------------------------
// CAMPO DE FECHA
// -----------------------------------------

const campoFecha = document.getElementById("fecha");

// No permitir fechas anteriores a hoy

campoFecha.min = fechaHoy;


// -----------------------------------------
// OBTENER DATOS DE LA URL
// -----------------------------------------

const parametros = new URLSearchParams(window.location.search);

const claseURL = parametros.get("clase");

const fechaURL = parametros.get("fecha");


// -----------------------------------------
// CLASE DESDE LA URL
// -----------------------------------------

// Lista de clases permitidas (ids de la tabla clases)

const clasesPermitidas = <?php echo json_encode(array_map("strval", array_keys($clases))); ?>;

const selectClase = document.getElementById("clase");

if (claseURL && clasesPermitidas.includes(claseURL)) {

    // Seleccionar automáticamente la clase

    selectClase.value = claseURL;

    // Bloquear el selector para que el usuario
    // no pueda cambiar la clase cuando viene
    // desde la página de clases.

    selectClase.disabled = true;

}


// -----------------------------------------
// FECHA DESDE LA URL
// -----------------------------------------

if (fechaURL && fechaURL >= fechaHoy) {

    // Mostrar la fecha seleccionada

    campoFecha.value = fechaURL;

    // Evitar que se modifique desde el formulario

    campoFecha.readOnly = true;

}


// -----------------------------------------
// ENVIAR FORMULARIO
// -----------------------------------------

const formulario = document.getElementById("formularioReserva");

formulario.addEventListener("submit", function () {

    // Si la clase estaba bloqueada porque
    // veníamos desde clases.php, la volvemos
    // a activar justo antes de enviar el formulario.
    // Así PHP recibe correctamente $_POST["clase"].

    selectClase.disabled = false;

});

</script>

</body>

</html>