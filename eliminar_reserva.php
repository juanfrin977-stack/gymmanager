<?php

session_start();

include "conexion.php";


// COMPROBAR QUE EL USUARIO HA INICIADO SESIÓN

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

$usuario_id = $_SESSION["usuario_id"];


// COMPROBAR QUE SE HA RECIBIDO EL ID

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {

    $id = intval($_POST["id"]);


    // ELIMINAR SOLO SI LA RESERVA PERTENECE AL USUARIO
    // Y NO HA PASADO TODAVÍA

    $sql = "DELETE FROM reservas
            WHERE id = ?
            AND usuario_id = ?
            AND fecha >= CURDATE()";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "ii",
        $id,
        $usuario_id
    );

    $stmt->execute();

    $stmt->close();

}


// CERRAR CONEXIÓN

$conexion->close();


// VOLVER SIEMPRE A MIS RESERVAS

header("Location: mis_reservas.php");
exit;

?>