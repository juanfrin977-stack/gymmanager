<?php

session_start();

include "conexion.php";
include "funciones.php";

$clases = obtener_clases($conexion);


// -----------------------------------------
// FECHA SELECCIONADA
// -----------------------------------------

$fecha = $_GET["fecha"] ?? date("Y-m-d");


// Si la fecha no es válida o es anterior a hoy, se usa la de hoy

$fecha_objeto = DateTime::createFromFormat("Y-m-d", $fecha);

if (!$fecha_objeto || $fecha_objeto->format("Y-m-d") !== $fecha || $fecha < date("Y-m-d")) {
    $fecha = date("Y-m-d");
}


// -----------------------------------------
// DÍA DE LA SEMANA (1 = lunes ... 7 = domingo)
// -----------------------------------------

$dia_semana = date("N", strtotime($fecha));

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Clases - GymManager</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>GymManager</h1>

    <?php mostrar_menu(); ?>

</header>

<main>


<section class="clases">

    <h2>Nuestras clases</h2>

    <p>
        Selecciona una fecha para consultar las plazas disponibles.
    </p>


    <!-- SELECTOR DE FECHA -->

    <form method="GET" action="clases.php" class="selector-fecha">

        <label for="fecha">
            📅 Fecha:
        </label>

        <input
            type="date"
            id="fecha"
            name="fecha"
            value="<?php echo htmlspecialchars($fecha); ?>"
            min="<?php echo date("Y-m-d"); ?>"
            required
        >

        <button type="submit" class="boton">
            Consultar plazas
        </button>

    </form>


    <div class="contenedor-clases">


        <?php foreach ($clases as $id_clase => $clase): ?>


            <div class="card">

                <h3><?php echo htmlspecialchars(nombre_clase($id_clase)); ?></h3>

                <p><?php echo htmlspecialchars($clase["descripcion"]); ?></p>

                <p>📅 <?php echo htmlspecialchars($clase["dias"]); ?></p>

                <p>🕐 <?php echo htmlspecialchars($clase["hora"]); ?></p>


                <?php

                // Comprobar si la clase se realiza ese día

                $clase_disponible = in_array($dia_semana, $clase["dias_numero"]);

                ?>


                <?php if (!$clase_disponible): ?>

                    <p class="plazas">
                        ❌ No disponible este día
                    </p>

                <?php else: ?>

                    <?php

                    // Contar reservas de esa clase y fecha

                    $sql = "SELECT COUNT(*) AS total
                            FROM reservas
                            WHERE clase_id = ?
                            AND fecha = ?";

                    $stmt = $conexion->prepare($sql);

                    $stmt->bind_param("is", $id_clase, $fecha);

                    $stmt->execute();

                    $fila = $stmt->get_result()->fetch_assoc();

                    $stmt->close();

                    $plazas = max(0, $clase["plazas"] - $fila["total"]);

                    ?>


                    <?php if ($plazas > 0): ?>

                        <p class="plazas">
                            🪑 <?php echo $plazas; ?> plazas disponibles
                        </p>

                        <a
                            href="reserva.php?clase=<?php echo $id_clase; ?>&fecha=<?php echo urlencode($fecha); ?>"
                            class="boton"
                        >
                            Reservar
                        </a>

                    <?php else: ?>

                        <p class="plazas">
                            ❌ COMPLETO
                        </p>

                    <?php endif; ?>

                <?php endif; ?>

            </div>


        <?php endforeach; ?>


    </div>

</section>

</main>

<footer>

    <p>© 2026 GymManager</p>

</footer>

</body>

</html>

<?php

$conexion->close();

?>