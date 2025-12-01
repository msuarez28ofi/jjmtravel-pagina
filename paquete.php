<?php
require_once "conexion.php";

// ===============================================
// VALIDAR DATOS RECIBIDOS
// ===============================================
$id_presupuesto = $_GET["id_presupuesto"] ?? 0;
$hotel_code     = $_GET["hotel"] ?? "";

if ($id_presupuesto == 0 || $hotel_code == "") {
    die("Error: datos incompletos.");
}

// ===============================================
// OBTENER ID DEL HOTEL REAL
// ===============================================
$stmt = $conn->prepare("
    SELECT id_hotel, nombre 
    FROM hoteles
    WHERE LOWER(REPLACE(nombre, ' ', '')) = ?
");
$stmt->bind_param("s", $hotel_code);
$stmt->execute();
$rs = $stmt->get_result();

if ($rs->num_rows == 0) die("Error: hotel no encontrado.");

$hotel = $rs->fetch_assoc();
$id_hotel = $hotel["id_hotel"];
$nombre_hotel = $hotel["nombre"];

// ===============================================
// OBTENER DATOS DEL PRESUPUESTO
// ===============================================
$stmt = $conn->prepare("
    SELECT *
    FROM presupuesto_reservas
    WHERE id_presupuesto = ?
");
$stmt->bind_param("i", $id_presupuesto);
$stmt->execute();
$pres = $stmt->get_result()->fetch_assoc();

$desde      = $pres["fecha_reserva_desde"];
$hasta      = $pres["fecha_reserva_hasta"];
$noches     = $pres["cantidad_noches"];
$personas   = $pres["cantidad_personas"];

// ===============================================
// OBTENER HABITACIONES DEL PRESUPUESTO
// ===============================================
$habitaciones = $conn->query("
    SELECT dhp.*, th.descripcion, th.capacidad_maxima
    FROM detalle_habitaciones_presupuesto dhp
    INNER JOIN tipo_habitaciones th 
        ON dhp.id_tipo_habitacion = th.id_tipo_habitacion
    WHERE dhp.id_presupuesto = $id_presupuesto
");

// ===============================================
// OBTENER TARIFAS DEL HOTEL
// ===============================================
$tarifas_query = $conn->query("
    SELECT *
    FROM tarifarios
    WHERE id_hotel = $id_hotel
");

// Guardamos tarifas en arreglo cómodo
$tarifas = [];
while ($t = $tarifas_query->fetch_assoc()) {
    $tarifas[$t["id_tipo_habitacion"]] = $t["tarifa"];
}

// ===============================================
// CALCULAR TOTAL
// ===============================================
$total_general = 0;
$lineas = [];

while ($h = $habitaciones->fetch_assoc()) {

    $id_tipo = $h["id_tipo_habitacion"];
    $descripcion = $h["descripcion"];
    $cantidad = $h["cantidad_habitaciones"];

    if (!isset($tarifas[$id_tipo])) {
        $lineas[] = "No hay tarifario disponible para habitación $descripcion.";
        continue;
    }

    $tarifa = $tarifas[$id_tipo];

    $subtotal = $tarifa * $cantidad * $noches;
    $total_general += $subtotal;

    $lineas[] = "$cantidad x Habitación $descripcion → $tarifa USD/noche → $subtotal USD";
}

// ===============================================
// ACTUALIZAR TOTAL EN BD
// ===============================================
$stmt = $conn->prepare("
    UPDATE presupuesto_reservas
    SET monto_total = ?
    WHERE id_presupuesto = ?
");
$stmt->bind_param("di", $total_general, $id_presupuesto);
$stmt->execute();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paquete - Resumen</title>
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

    <h2 style="font-size:3rem;">Resumen del Paquete</h2>
    <h3>Hotel seleccionado: <b><?php echo $nombre_hotel; ?></b></h3>

    <p><b>Fecha desde:</b> <?php echo $desde; ?></p>
    <p><b>Fecha hasta:</b> <?php echo $hasta; ?></p>
    <p><b>Noches:</b> <?php echo $noches; ?></p>
    <p><b>Personas:</b> <?php echo $personas; ?></p>

    <h3>Detalle de habitaciones</h3>
    <ul>
        <?php foreach ($lineas as $linea): ?>
            <li><?php echo $linea; ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Total del paquete: <b><?php echo number_format($total_general, 2); ?> USD</b></h2>

    <br>
    <a class="btn" href="reserva.php?id_presupuesto=<?php echo $id_presupuesto; ?>">Confirmar Reserva</a>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
