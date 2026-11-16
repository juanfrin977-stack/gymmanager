<?php

session_start();

include "conexion.php";
include "funciones.php";

$clases = obtener_clases($conexion);


// -----------------------------------------
// COMPROBAR SESIÓN
// -----------------------------------------

if (!isset($_SESSION["usuario_id"])) {

    mostrar_error(
        "Debes iniciar sesión",
        "Para realizar una reserva necesitas iniciar sesión.",
        "login.html",
        "Iniciar sesión"
    );
}


// -----------------------------------------
// DATOS DEL USUARIO LOGUEADO
// -----------------------------------------

$usuario_id = $_SESSION["usuario_id"];


// -----------------------------------------
// COMPROBAR FORMULARIO
// -----------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: reserva.php");
    exit;
}


// -----------------------------------------
// OBTENER DATOS DE LA RESERVA
// -----------------------------------------

$clase = intval($_POST["clase"] ?? 0);
$fecha = $_POST["fecha"] ?? "";


// -----------------------------------------
// COMPROBAR CAMPOS
// -----------------------------------------

if (empty($clase) || empty($fecha)) {

    mostrar_error(
        "Faltan datos",
        "Debes seleccionar una clase y una fecha.",
        "reserva.php",
        "Volver a reservas"
    );
}


// -----------------------------------------
// COMPROBAR FORMATO DE LA FECHA
// -----------------------------------------

$fecha_objeto = DateTime::createFromFormat("Y-m-d", $fecha);

if (!$fecha_objeto || $fecha_objeto->format("Y-m-d") !== $fecha) {

    mostrar_error(
        "Fecha no válida",
        "La fecha introducida no es correcta.",
        "reserva.php",
        "Volver a reservas"
    );
}


// -----------------------------------------
// COMPROBAR CLASE VÁLIDA
// -----------------------------------------

if (!isset($clases[$clase])) {

    mostrar_error(
        "Clase no válida",
        "La clase seleccionada no existe.",
        "reserva.php",
        "Volver a reservas"
    );
}


// -----------------------------------------
// COMPROBAR FECHA
// -----------------------------------------

$hoy = date("Y-m-d");

if ($fecha < $hoy) {

    mostrar_error(
        "Fecha no válida",
        "No puedes reservar una fecha anterior a hoy.",
        "reserva.php",
        "Volver a reservas"
    );
}


// -----------------------------------------
// COMPROBAR DÍA DE LA SEMANA
// -----------------------------------------

$dia_semana = date("N", strtotime($fecha));

if (!in_array($dia_semana, $clases[$clase]["dias_numero"])) {

    mostrar_error(
        "Fecha no disponible",
        $clases[$clase]["nombre"] . " solo se imparte: " . $clases[$clase]["dias"] . ".",
        "clases.php",
        "Ver clases"
    );
}


// -----------------------------------------
// COMPROBAR RESERVA DUPLICADA
// -----------------------------------------

$sql_comprobar = "SELECT id
                  FROM reservas
                  WHERE usuario_id = ?
                  AND clase_id = ?
                  AND fecha = ?";

$stmt_comprobar = $conexion->prepare($sql_comprobar);

$stmt_comprobar->bind_param(
    "iis",
    $usuario_id,
    $clase,
    $fecha
);

$stmt_comprobar->execute();

$resultado = $stmt_comprobar->get_result();

if ($resultado->num_rows > 0) {

    mostrar_error(
        "Reserva duplicada",
        "Ya tienes una reserva para esta clase y fecha.",
        "mis_reservas.php",
        "Ver mis reservas"
    );
}

$stmt_comprobar->close();


// -----------------------------------------
// COMPROBAR PLAZAS
// -----------------------------------------

$sql_plazas = "SELECT COUNT(*) AS total
               FROM reservas
               WHERE clase_id = ?
               AND fecha = ?";

$stmt_plazas = $conexion->prepare($sql_plazas);

$stmt_plazas->bind_param(
    "is",
    $clase,
    $fecha
);

$stmt_plazas->execute();

$fila = $stmt_plazas->get_result()->fetch_assoc();

$reservas_actuales = $fila["total"];

$stmt_plazas->close();

if ($reservas_actuales >= $clases[$clase]["plazas"]) {

    mostrar_error(
        "Clase completa",
        "Lo sentimos, esta clase ya tiene todas las plazas ocupadas para esa fecha.",
        "clases.php",
        "Ver clases"
    );
}


// -----------------------------------------
// GUARDAR RESERVA
// -----------------------------------------

$sql = "INSERT INTO reservas
        (usuario_id, clase_id, fecha)
        VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "iis",
    $usuario_id,
    $clase,
    $fecha
);

$guardado = $stmt->execute();

$stmt->close();

$conexion->close();


if (!$guardado) {

    mostrar_error(
        "Error",
        "No se ha podido guardar la reserva. Inténtalo más tarde.",
        "reserva.php",
        "Volver a reservas"
    );
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reserva realizada - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>

        <h1>GymManager</h1>

        <?php mostrar_menu(); ?>

    </header>


    <main class="reserva">

        <h2>¡Reserva realizada correctamente!</h2>

        <p>
            Has reservado <?php echo htmlspecialchars(nombre_clase($clase)); ?>
            el <?php echo date("d/m/Y", strtotime($fecha)); ?>
            a las <?php echo $clases[$clase]["horas"][$dia_semana]; ?>.
        </p>

        <a href="mis_reservas.php" class="boton">
            Ver mis reservas
        </a>

    </main>


    <footer>

        <p>© 2026 GymManager</p>

    </footer>

</body>

</html>