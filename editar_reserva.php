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


// COMPROBAR QUE SE HA RECIBIDO EL ID

if (!isset($_GET["id"])) {

    header("Location: mis_reservas.php");
    exit;

}

$id = intval($_GET["id"]);


// BUSCAR LA RESERVA SOLO SI PERTENECE AL USUARIO
// Y NO HA PASADO TODAVÍA

$sql = "SELECT r.id, r.clase_id, r.fecha, u.nombre, u.email
        FROM reservas r
        INNER JOIN usuarios u ON u.id = r.usuario_id
        WHERE r.id = ?
        AND r.usuario_id = ?
        AND r.fecha >= CURDATE()";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "ii",
    $id,
    $usuario_id
);

$stmt->execute();

$resultado = $stmt->get_result();

$reserva = $resultado->fetch_assoc();

$stmt->close();

$conexion->close();


if (!$reserva) {

    mostrar_error(
        "Reserva no encontrada",
        "Esta reserva no existe, no pertenece a tu cuenta o ya ha pasado.",
        "mis_reservas.php",
        "Volver a mis reservas"
    );

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modificar reserva - GymManager</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


    <header>

        <h1>GymManager</h1>

        <?php mostrar_menu(); ?>

    </header>


    <main class="reserva">

        <h2>Modificar reserva</h2>


        <form action="actualizar_reserva.php" method="post">


            <!-- ID DE LA RESERVA -->

            <input
                type="hidden"
                name="id"
                value="<?php echo $reserva['id']; ?>"
            >


            <!-- NOMBRE -->

            <label for="nombre">
                Nombre
            </label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                value="<?php echo htmlspecialchars($reserva['nombre']); ?>"
                readonly
                required
            >


            <!-- CORREO -->

            <label for="email">
                Correo electrónico
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($reserva['email']); ?>"
                readonly
                required
            >


            <!-- CLASE (las opciones salen de la tabla clases) -->

            <label for="clase">
                Clase
            </label>

            <select
                id="clase"
                name="clase"
                required
            >

                <?php foreach ($clases as $id_clase => $datos): ?>

                    <option
                        value="<?php echo $id_clase; ?>"
                        <?php if ((int) $reserva["clase_id"] === $id_clase) echo "selected"; ?>
                    >
                        <?php echo htmlspecialchars(nombre_clase($id_clase)); ?>
                        (<?php echo htmlspecialchars($datos["dias"]); ?>, <?php echo htmlspecialchars($datos["hora"]); ?>)
                    </option>

                <?php endforeach; ?>

            </select>


            <!-- FECHA (no deja elegir días anteriores a hoy) -->

            <label for="fecha">
                Fecha
            </label>

            <input
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo htmlspecialchars($reserva['fecha']); ?>"
                min="<?php echo date('Y-m-d'); ?>"
                required
            >


            <!-- GUARDAR -->

            <button type="submit">
                💾 Guardar cambios
            </button>


        </form>

    </main>


    <footer>

        <p>© 2026 GymManager</p>

    </footer>


</body>

</html>