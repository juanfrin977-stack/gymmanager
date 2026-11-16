<?php

// DATOS DE CONEXIÓN

$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "gymmanager";


// CONECTAR CON LA BASE DE DATOS

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);


// SI FALLA, MOSTRAR UN MENSAJE GENÉRICO

if ($conexion->connect_error) {

    die("No se ha podido conectar con la base de datos. Inténtalo más tarde.");

}


// JUEGO DE CARACTERES (necesario para los emojis)

$conexion->set_charset("utf8mb4");

?>