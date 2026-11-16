<?php

session_start();

include "conexion.php";
include "funciones.php";

$clases = obtener_clases($conexion);

$usuarioAutenticado = isset($_SESSION["usuario_id"]);
$nombreUsuario = $_SESSION["usuario_nombre"] ?? "";

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="GymManager: consulta nuestras clases y gestiona tus reservas de gimnasio.">

    <title>GymManager | Tu gimnasio</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>GymManager</h1>

    <?php mostrar_menu(); ?>

</header>


<section class="hero">

    <h2>Bienvenido a nuestro gimnasio</h2>

    <?php if ($usuarioAutenticado && $nombreUsuario !== ""): ?>

        <p>
            ¡Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?>!
        </p>

    <?php endif; ?>

    <p>Gestiona tus clases y reservas de forma sencilla.</p>

    <a href="clases.php" class="boton">Ver clases</a>

</section>


<section class="clases">

    <h2>Nuestras clases</h2>

    <p>Encuentra la actividad que mejor se adapte a ti.</p>

    <div class="tarjetas">

        <?php foreach ($clases as $id_clase => $clase): ?>

            <article class="tarjeta">

                <h3><?php echo htmlspecialchars(nombre_clase($id_clase)); ?></h3>

                <p><?php echo htmlspecialchars($clase["descripcion_corta"]); ?></p>

                <a href="reserva.php?clase=<?php echo $id_clase; ?>" class="boton">
                    Reservar
                </a>

            </article>

        <?php endforeach; ?>

    </div>

</section>


<footer>

    <p>&copy; <?php echo date("Y"); ?> GymManager</p>

</footer>

</body>

</html>