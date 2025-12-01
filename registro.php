<?php
require_once "conexion.php";

// =====================================================
// VALIDAR HOTEL RECIBIDO
// =====================================================
$hotel_code = $_GET["hotel"] ?? "";

if ($hotel_code === "") {
    die("Error: hotel no recibido.");
}

// =====================================================
// OBTENER ID DEL HOTEL DESDE LA BD
// =====================================================
$stmt = $conn->prepare("
    SELECT id_hotel, nombre 
    FROM hoteles 
    WHERE LOWER(REPLACE(nombre, ' ', '')) = ?
");
$stmt->bind_param("s", $hotel_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: hotel no existe en BD.");
}

$hotel_db = $result->fetch_assoc();
$id_hotel = $hotel_db["id_hotel"];
$nombre_hotel = $hotel_db["nombre"];

// =====================================================
// SI VIENE EL FORMULARIO, GUARDAR TODO
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fecha_desde = $_POST["desde"];
    $fecha_hasta = $_POST["hasta"];
    $cantidad_personas = intval($_POST["personas"]);

    // habitaciones
    $cant_indiv = intval($_POST["hab_individual"]);
    $cant_doble = intval($_POST["hab_doble"]);
    $cant_triple = intval($_POST["hab_triple"]);
    $cant_cuadruple = intval($_POST["hab_cuadruple"]);

    // VALIDACIÓN SIMPLE
    if ($fecha_desde === "" || $fecha_hasta === "") {
        die("Fechas inválidas.");
    }

    if ($cantidad_personas <= 0) {
        die("La cantidad de personas no puede ser cero.");
    }

    // =====================================================
    // 1) INSERTAR PRESUPUESTO (SIN CALCULAR DÍAS — LO CALCULA EL TRIGGER)
    // =====================================================
    $stmt = $conn->prepare("
        INSERT INTO presupuesto_reservas
            (id_turista, id_tarifario, fecha_reserva_desde, fecha_reserva_hasta, cantidad_personas, traslado_decimal, monto_total)
        VALUES
            (1, 0, ?, ?, ?, 0, 0)
    ");
    $stmt->bind_param("ssi", $fecha_desde, $fecha_hasta, $cantidad_personas);
    $stmt->execute();

    $id_presupuesto = $conn->insert_id;

    // =====================================================
    // 2) OBTENER TIPOS DE HABITACIÓN DESDE LA BD
    // =====================================================
    $tipos = $conn->query("SELECT * FROM tipo_habitaciones");

    // =====================================================
    // 3) INSERTAR DETALLE DE HABITACIONES
    // =====================================================
    while ($row = $tipos->fetch_assoc()) {
        $id_tipo = $row["id_tipo_habitacion"];
        $descripcion = strtolower($row["descripcion"]);

        $cantidad = 0;
        if ($descripcion === "individual")     $cantidad = $cant_indiv;
        if ($descripcion === "doble")          $cantidad = $cant_doble;
        if ($descripcion === "triple")         $cantidad = $cant_triple;
        if ($descripcion === "cuadruple")      $cantidad = $cant_cuadruple;

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
    // 4) REDIRECCIÓN A PAQUETE.PHP
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
