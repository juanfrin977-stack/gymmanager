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

$usuario_id = $_SESSION["usuario_id"];


// -----------------------------------------
// COMPROBAR FORMULARIO
// -----------------------------------------

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: mis_reservas.php");
    exit;
}


// -----------------------------------------
// OBTENER DATOS
// -----------------------------------------

$id = intval($_POST["id"] ?? 0);

$clase = intval($_POST["clase"] ?? 0);

$fecha = $_POST["fecha"] ?? "";


// -----------------------------------------
// COMPROBAR DATOS
// -----------------------------------------

if ($id <= 0 || empty($clase) || empty($fecha)) {

    mostrar_error(
        "Faltan datos",
        "Todos los campos son obligatorios.",
        "mis_reservas.php",
        "Volver a mis reservas"
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
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}


// -----------------------------------------
// COMPROBAR QUE LA RESERVA PERTENECE
// AL USUARIO Y NO HA PASADO TODAVÍA
// -----------------------------------------

$sql_reserva = "SELECT id
                FROM reservas
                WHERE id = ?
                AND usuario_id = ?
                AND fecha >= CURDATE()";

$stmt_reserva = $conexion->prepare($sql_reserva);

$stmt_reserva->bind_param(
    "ii",
    $id,
    $usuario_id
);

$stmt_reserva->execute();

$resultado_reserva = $stmt_reserva->get_result();

if ($resultado_reserva->num_rows === 0) {

    mostrar_error(
        "Reserva no encontrada",
        "No puedes modificar esta reserva: no existe, no es tuya o ya ha pasado.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}

$stmt_reserva->close();


// -----------------------------------------
// COMPROBAR CLASE VÁLIDA
// -----------------------------------------

if (!isset($clases[$clase])) {

    mostrar_error(
        "Clase no válida",
        "La clase seleccionada no existe.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}


// -----------------------------------------
// COMPROBAR FECHA
// -----------------------------------------

$hoy = date("Y-m-d");

if ($fecha < $hoy) {

    mostrar_error(
        "Fecha no válida",
        "No puedes seleccionar una fecha anterior a hoy.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}


// -----------------------------------------
// COMPROBAR DÍA DE LA CLASE
// -----------------------------------------

$dia_semana = date("N", strtotime($fecha));

if (!in_array($dia_semana, $clases[$clase]["dias_numero"])) {

    mostrar_error(
        "Fecha no disponible",
        $clases[$clase]["nombre"] . " solo se imparte: " . $clases[$clase]["dias"] . ".",
        "editar_reserva.php?id=" . $id,
        "Volver a modificar la reserva"
    );
}


// -----------------------------------------
// COMPROBAR QUE NO EXISTE OTRA RESERVA
// DEL MISMO USUARIO
// -----------------------------------------

$sql_duplicada = "SELECT id
                  FROM reservas
                  WHERE usuario_id = ?
                  AND clase_id = ?
                  AND fecha = ?
                  AND id != ?";

$stmt_duplicada = $conexion->prepare($sql_duplicada);

$stmt_duplicada->bind_param(
    "iisi",
    $usuario_id,
    $clase,
    $fecha,
    $id
);

$stmt_duplicada->execute();

$resultado_duplicada = $stmt_duplicada->get_result();

if ($resultado_duplicada->num_rows > 0) {

    mostrar_error(
        "Reserva duplicada",
        "Ya tienes otra reserva para esta clase y fecha.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}

$stmt_duplicada->close();


// -----------------------------------------
// COMPROBAR PLAZAS
// -----------------------------------------

$sql_plazas = "SELECT COUNT(*) AS total
               FROM reservas
               WHERE clase_id = ?
               AND fecha = ?
               AND id != ?";

$stmt_plazas = $conexion->prepare($sql_plazas);

$stmt_plazas->bind_param(
    "isi",
    $clase,
    $fecha,
    $id
);

$stmt_plazas->execute();

$fila = $stmt_plazas->get_result()->fetch_assoc();

$reservas_actuales = $fila["total"];

$stmt_plazas->close();

if ($reservas_actuales >= $clases[$clase]["plazas"]) {

    mostrar_error(
        "Clase completa",
        "Lo sentimos, esta clase ya tiene todas las plazas ocupadas para esa fecha.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}


// -----------------------------------------
// ACTUALIZAR RESERVA
// -----------------------------------------

$sql_update = "UPDATE reservas
               SET clase_id = ?,
                   fecha = ?
               WHERE id = ?
               AND usuario_id = ?";

$stmt_update = $conexion->prepare($sql_update);

$stmt_update->bind_param(
    "isii",
    $clase,
    $fecha,
    $id,
    $usuario_id
);

$actualizado = $stmt_update->execute();

$stmt_update->close();

$conexion->close();


if (!$actualizado) {

    mostrar_error(
        "Error",
        "No se ha podido modificar la reserva. Inténtalo más tarde.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );
}

header("Location: mis_reservas.php");
exit;

?>