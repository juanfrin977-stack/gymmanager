<?php

session_start();

include "conexion.php";
include "funciones.php";


// COMPROBAR QUE SE HA ENVIADO EL FORMULARIO

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.html");
    exit;
}


$email = trim($_POST["email"] ?? "");

$password = $_POST["password"] ?? "";


// COMPROBAR CAMPOS

if (empty($email) || empty($password)) {

    mostrar_error(
        "Faltan datos",
        "El correo y la contraseña son obligatorios.",
        "login.html",
        "Volver al inicio de sesión"
    );
}


// BUSCAR USUARIO

$sql = "SELECT id, nombre, email, password
        FROM usuarios
        WHERE email = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();

$stmt->close();

$conexion->close();


// COMPROBAR USUARIO Y CONTRASEÑA
// (mismo mensaje en los dos casos para no dar pistas de qué correos existen)

if (!$usuario || !password_verify($password, $usuario["password"])) {

    mostrar_error(
        "Datos incorrectos",
        "El correo o la contraseña no son correctos.",
        "login.html",
        "Volver a intentarlo"
    );
}


// CREAR SESIÓN

// Nuevo identificador de sesión para evitar la fijación de sesión
session_regenerate_id(true);

$_SESSION["usuario_id"] = $usuario["id"];

$_SESSION["usuario_nombre"] = $usuario["nombre"];

$_SESSION["usuario_email"] = $usuario["email"];


// REDIRIGIR A LA PÁGINA PRINCIPAL

header("Location: index.php");

exit;

?>