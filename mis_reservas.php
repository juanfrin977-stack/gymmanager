<?php

session_start();

include "conexion.php";
include "funciones.php";

$clases = obtener_clases($conexion);


// COMPROBAR QUE EL USUARIO HA INICIADO SESIÓN

if (!isset($_SESSION["usuario_id"])) {

    header("Location: login.html");
    exit;

}

$usuario_id = $_SESSION["usuario_id"];

$hoy = date("Y-m-d");


// BUSCAR SOLO LAS RESERVAS DEL USUARIO
// Primero las próximas y después las pasadas

// Se une con usuarios para mostrar el nombre y el correo del socio

$sql = "SELECT r.id, r.clase_id, r.fecha, r.fecha_reserva,
               u.nombre, u.email
        FROM reservas r
        INNER JOIN usuarios u ON u.id = r.usuario_id
        WHERE r.usuario_id = ?
        ORDER BY r.fecha < CURDATE(), r.fecha ASC";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $usuario_id);

$stmt->execute();

$resultado = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis reservas - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>GymManager</h1>

    <?php mostrar_menu(); ?>

</header>


<main class="reserva">

    <h2>Mis reservas</h2>

    <p>Aquí puedes consultar, modificar o cancelar tus reservas.</p>


    <?php if ($resultado->num_rows > 0): ?>

        <div class="reservas-contenedor">

            <?php while ($reserva = $resultado->fetch_assoc()): ?>

                <?php
                $pasada = $reserva["fecha"] < $hoy;
                $id_clase = (int) $reserva["clase_id"];
                $dia_reserva = (int) date("N", strtotime($reserva["fecha"]));
                ?>

                <div class="reserva-card">

                    <h3><?php echo htmlspecialchars(nombre_clase($id_clase)); ?></h3>


                    <p>
                        <strong>Nombre:</strong>
                        <?php echo htmlspecialchars($reserva["nombre"]); ?>
                    </p>


                    <p>
                        <strong>Correo:</strong>
                        <?php echo htmlspecialchars($reserva["email"]); ?>
                    </p>


                    <p>
                        <strong>Fecha de la clase:</strong>
                        <?php echo date("d/m/Y", strtotime($reserva["fecha"])); ?>

                        <?php if (isset($clases[$id_clase]["horas"][$dia_reserva])): ?>
                            a las <?php echo $clases[$id_clase]["horas"][$dia_reserva]; ?>
                        <?php endif; ?>
                    </p>


                    <p>
                        <strong>Reserva realizada:</strong>
                        <?php echo date("d/m/Y H:i", strtotime($reserva["fecha_reserva"])); ?>
                    </p>


                    <?php if ($pasada): ?>

                        <!-- RESERVA PASADA: SIN BOTONES -->

                        <p>
                            <strong>Estado:</strong>
                            Finalizada
                        </p>

                    <?php else: ?>

                        <div class="acciones-reserva">

                            <!-- MODIFICAR -->

                            <form action="editar_reserva.php" method="get">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $reserva['id']; ?>"
                                >

                                <button type="submit">
                                    ✏️ Modificar reserva
                                </button>

                            </form>


                            <!-- ELIMINAR -->

                            <form
                                action="eliminar_reserva.php"
                                method="post"
                                onsubmit="return confirm('¿Seguro que quieres eliminar esta reserva?');"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?php echo $reserva['id']; ?>"
                                >

                                <button type="submit">
                                    🗑️ Eliminar reserva
                                </button>

                            </form>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>


    <?php else: ?>

        <div class="reserva-card">

            <h3>📅 No tienes reservas</h3>

            <p>
                Actualmente no tienes ninguna reserva realizada.
            </p>

            <a href="clases.php" class="boton">
                Ver clases
            </a>

        </div>

    <?php endif; ?>


</main>


<footer>

    <p>© 2026 GymManager</p>

</footer>

</body>

</html>

<?php

$stmt->close();

$conexion->close();

?>