<?php

session_start();

include "funciones.php";

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contacto - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<header>

    <h1>GymManager</h1>


    <?php mostrar_menu(); ?>

</header>


<main class="reserva">


    <h2>Contacto</h2>


    <p>
        ¿Tienes alguna duda? Ponte en contacto con nosotros.
    </p>


    <div class="contacto-info">


        <p>

            📍 <strong>Dirección:</strong><br>

            Avenida del Gimnasio, 25

        </p>


        <p>

            📞 <strong>Teléfono:</strong><br>

            600 123 456

        </p>


        <p>

            📧 <strong>Email:</strong><br>

            info@gymmanager.com

        </p>


        <p>

            🕒 <strong>Horario:</strong><br>

            Lunes a viernes: 08:00 - 22:00<br>

            Sábados: 09:00 - 14:00

        </p>


    </div>


    <h3>Envíanos un mensaje</h3>


    <form action="enviar_mensaje.php" method="post">


        <label for="nombre">

            Nombre

        </label>


        <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Escribe tu nombre"
            required
        >


        <label for="email">

            Correo electrónico

        </label>


        <input
            type="email"
            id="email"
            name="email"
            placeholder="Escribe tu correo"
            required
        >


        <label for="mensaje">

            Mensaje

        </label>


        <textarea
            id="mensaje"
            name="mensaje"
            rows="5"
            placeholder="Escribe tu mensaje"
            required
        ></textarea>


        <button type="submit">

            📩 Enviar mensaje

        </button>


    </form>


</main>


<footer>

    <p>© 2026 GymManager</p>

</footer>


</body>

</html>