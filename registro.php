<?php
require_once "conexion.php";

// =====================================================
// MAPEO SOLO PARA LOS QUE FALLABAN
// =====================================================
$mapa_hoteles = [
    "puntablanca" => "SUNSOL PUNTA BLANCA",
    "ecoland"     => "SUNSOL ECOLAND"
];

// =====================================================
// VALIDAR HOTEL RECIBIDO
// =====================================================
$hotel_code = $_GET["hotel"] ?? "";

if ($hotel_code === "") {
    die("Error: hotel no recibido.");
}

// =====================================================
// DEFINIR CÓMO BUSCAR EN BD SEGÚN EL HOTEL
// =====================================================
if (isset($mapa_hoteles[$hotel_code])) {
    $busqueda = $mapa_hoteles[$hotel_code];
    $comparacion_sql = "nombre = ?";
} else {
    $busqueda = $hotel_code;
    $comparacion_sql = "LOWER(REPLACE(nombre, ' ', '')) = ?";
}

// =====================================================
// OBTENER HOTEL DESDE BD
// =====================================================
$query = "
    SELECT id_hotel, nombre
    FROM hoteles
    WHERE $comparacion_sql
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $busqueda);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: hotel no existe en BD (clave: $hotel_code / búsqueda: $busqueda)");
}

$hotel_db = $result->fetch_assoc();
$id_hotel = $hotel_db["id_hotel"];
$nombre_hotel = $hotel_db["nombre"];

// =====================================================
// SI VIENE EL FORMULARIO, GUARDAR
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fecha_desde = $_POST["desde"];
    $fecha_hasta = $_POST["hasta"];
    $cantidad_personas = intval($_POST["personas"]);

    $cant_indiv     = intval($_POST["hab_individual"]);
    $cant_doble     = intval($_POST["hab_doble"]);
    $cant_triple    = intval($_POST["hab_triple"]);
    $cant_cuadruple = intval($_POST["hab_cuadruple"]);

    if ($fecha_desde === "" || $fecha_hasta === "") {
        die("Fechas inválidas.");
    }

    if ($cantidad_personas <= 0) {
        die("La cantidad de personas no puede ser cero.");
    }

    // =====================================================
    // NUEVO: OBTENER id_tarifario REAL DEL HOTEL
    // =====================================================
    $stmt_tar = $conn->prepare("
        SELECT id_tarifario
        FROM tarifarios
        WHERE id_hotel = ?
        ORDER BY fecha_desde ASC
        LIMIT 1
    ");
    $stmt_tar->bind_param("i", $id_hotel);
    $stmt_tar->execute();
    $res_tar = $stmt_tar->get_result();

    if ($res_tar->num_rows === 0) {
        die("Error: El hotel seleccionado NO tiene tarifas cargadas.");
    }

    $tar = $res_tar->fetch_assoc();
    $id_tarifario = $tar["id_tarifario"];

    // =====================================================
    // 1) INSERTAR PRESUPUESTO
    // =====================================================
    $stmt = $conn->prepare("
        INSERT INTO presupuesto_reservas
            (id_turista, id_tarifario, fecha_reserva_desde, fecha_reserva_hasta, cantidad_personas, traslado_decimal, monto_total)
        VALUES
            (1, ?, ?, ?, ?, 0, 0)
    ");
    $stmt->bind_param("issi", $id_tarifario, $fecha_desde, $fecha_hasta, $cantidad_personas);
    $stmt->execute();

    $id_presupuesto = $conn->insert_id;

    // =====================================================
    // 2) TIPOS DE HABITACIÓN
    // =====================================================
    $tipos = $conn->query("SELECT * FROM tipo_habitaciones");

    // =====================================================
    // 3) INSERTAR DETALLE HABITACIONES
    // =====================================================
    while ($row = $tipos->fetch_assoc()) {
        $id_tipo = $row["id_tipo_habitacion"];
        $descripcion = strtolower($row["descripcion"]);

        $cantidad = 0;
        if ($descripcion === "individual")  $cantidad = $cant_indiv;
        if ($descripcion === "doble")       $cantidad = $cant_doble;
        if ($descripcion === "triple")      $cantidad = $cant_triple;
        if ($descripcion === "cuadruple")   $cantidad = $cant_cuadruple;

        if ($cantidad > 0) {
            $stmt2 = $conn->prepare("
                INSERT INTO detalle_habitaciones_presupuesto
                    (id_presupuesto, id_tipo_habitacion, cantidad_habitaciones)
                VALUES (?, ?, ?)
            ");
            $stmt2->bind_param("iii", $id_presupuesto, $id_tipo, $cantidad);
            $stmt2->execute();
        }
    }

    // =====================================================
    // 4) REDIRECCIÓN A PAQUETE
    // =====================================================
    header("Location: paquete.php?id_presupuesto=" . $id_presupuesto . "&hotel=" . $hotel_code);
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <h1>JJM TRAVEL</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<section class="contenido">

    <h2 style="font-size:3rem;">Reservar en: <?php echo $nombre_hotel; ?></h2>

    <form action="" method="POST" class="formulario-reserva">

        <label>Fecha desde:</label>
        <input type="date" name="desde" required>

        <label>Fecha hasta:</label>
        <input type="date" name="hasta" required>

        <label>Cantidad de personas:</label>
        <input type="number" name="personas" min="1" required>

        <h3>Habitaciones</h3>

        <label>Individual:</label>
        <input type="number" name="hab_individual" min="0" value="0">

        <label>Doble:</label>
        <input type="number" name="hab_doble" min="0" value="0">

        <label>Triple:</label>
        <input type="number" name="hab_triple" min="0" value="0">

        <label>Cuádruple:</label>
        <input type="number" name="hab_cuadruple" min="0" value="0">

        <button type="submit" class="btn">Continuar</button>

    </form>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>


