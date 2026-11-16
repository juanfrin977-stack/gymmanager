<?php

// -----------------------------------------
// FUNCIONES COMUNES DE GYMMANAGER
// -----------------------------------------

// Iniciar la sesión si la página todavía no lo ha hecho

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// -----------------------------------------
// CARGAR LAS CLASES DESDE LA BASE DE DATOS
// Devuelve un array indexado por el id de la clase:
//
// $clases[2] = [
//     "nombre" => "Yoga", "icono" => "🧘",
//     "descripcion_corta" => "...", "descripcion" => "...",
//     "plazas" => 20,
//     "dias_numero" => [2, 4],              (1 = lunes ... 7 = domingo)
//     "horas" => [2 => "17:00", 4 => "17:00"],
//     "dias" => "Martes y jueves",
//     "hora" => "17:00"
// ]
// -----------------------------------------

function obtener_clases($conexion)
{
    $sql = "SELECT c.id, c.nombre, c.icono, c.descripcion_corta,
                   c.descripcion, c.plazas, h.dia_semana,
                   TIME_FORMAT(h.hora, '%H:%i') AS hora
            FROM clases c
            LEFT JOIN horarios h ON h.clase_id = c.id
            ORDER BY c.id, h.dia_semana";

    $resultado = $conexion->query($sql);

    $clases = [];

    while ($fila = $resultado->fetch_assoc()) {

        $id = (int) $fila["id"];

        // Primera vez que aparece la clase: guardar sus datos

        if (!isset($clases[$id])) {
            $clases[$id] = [
                "nombre" => $fila["nombre"],
                "icono" => $fila["icono"],
                "descripcion_corta" => $fila["descripcion_corta"],
                "descripcion" => $fila["descripcion"],
                "plazas" => (int) $fila["plazas"],
                "dias_numero" => [],
                "horas" => []
            ];
        }

        // Cada fila del JOIN añade un día de la semana

        if ($fila["dia_semana"] !== null) {
            $dia = (int) $fila["dia_semana"];
            $clases[$id]["dias_numero"][] = $dia;
            $clases[$id]["horas"][$dia] = $fila["hora"];
        }
    }

    // Textos para mostrar en pantalla

    foreach ($clases as $id => $clase) {
        $clases[$id]["dias"] = texto_dias($clase["dias_numero"]);
        $clases[$id]["hora"] = implode(" / ", array_unique($clase["horas"]));
    }

    return $clases;
}


// -----------------------------------------
// DÍAS DE LA SEMANA EN TEXTO
// Ejemplo: [1, 3] -> "Lunes y miércoles"
// -----------------------------------------

function texto_dias($dias_numero)
{
    $nombres = [1 => "lunes", 2 => "martes", 3 => "miércoles", 4 => "jueves",
                5 => "viernes", 6 => "sábado", 7 => "domingo"];

    $dias = [];

    foreach ($dias_numero as $numero) {
        $dias[] = $nombres[$numero];
    }

    if (count($dias) === 0) {
        return "Sin horario";
    }

    $ultimo = array_pop($dias);

    $texto = count($dias) > 0 ? implode(", ", $dias) . " y " . $ultimo : $ultimo;

    return mb_strtoupper(mb_substr($texto, 0, 1)) . mb_substr($texto, 1);
}


// -----------------------------------------
// NOMBRE DE UNA CLASE CON SU ICONO
// Ejemplo: 2 -> "🧘 Yoga"
// -----------------------------------------

function nombre_clase($id)
{
    global $clases;

    if (isset($clases[$id])) {
        return $clases[$id]["icono"] . " " . $clases[$id]["nombre"];
    }

    return "Clase no disponible";
}


// -----------------------------------------
// MENÚ DE NAVEGACIÓN
// Muestra unos enlaces u otros según si hay sesión
// -----------------------------------------

function mostrar_menu()
{
    $con_sesion = isset($_SESSION["usuario_id"]);

    echo '<nav>';
    echo '<a href="index.php">Inicio</a>';
    echo '<a href="clases.php">Clases</a>';

    if ($con_sesion) {
        echo '<a href="reserva.php">Reservas</a>';
        echo '<a href="mis_reservas.php">Mis reservas</a>';
    }

    echo '<a href="contacto.php">Contacto</a>';

    if ($con_sesion) {
        echo '<a href="cerrar_sesion.php">Cerrar sesión</a>';
    } else {
        echo '<a href="login.html">Iniciar sesión</a>';
        echo '<a href="registro.html">Crear cuenta</a>';
    }

    echo '</nav>';
}


// -----------------------------------------
// PÁGINA DE ERROR CON EL DISEÑO DE LA WEB
// Muestra el mensaje y termina la ejecución
// -----------------------------------------

function mostrar_error($titulo, $mensaje, $enlace = "index.php", $texto_enlace = "Volver al inicio")
{
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($titulo); ?> - GymManager</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>

        <header>
            <h1>GymManager</h1>
            <?php mostrar_menu(); ?>
        </header>

        <main class="reserva">

            <h2><?php echo htmlspecialchars($titulo); ?></h2>

            <p><?php echo htmlspecialchars($mensaje); ?></p>

            <a href="<?php echo htmlspecialchars($enlace); ?>" class="boton">
                <?php echo htmlspecialchars($texto_enlace); ?>
            </a>

        </main>

        <footer>
            <p>© 2026 GymManager</p>
        </footer>

    </body>

    </html>
    <?php

    exit;
}

?>