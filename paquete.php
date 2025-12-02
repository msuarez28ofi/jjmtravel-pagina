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

// ========================================================
// MISMO MÉTODO QUE EN REGISTRO.PHP PARA ENCONTRAR EL HOTEL
// ========================================================
$mapa_hoteles = [
    "puntablanca" => "SUNSOL PUNTA BLANCA",
    "ecoland"     => "SUNSOL ECOLAND"
];

if (isset($mapa_hoteles[$hotel_code])) {
    $busqueda = $mapa_hoteles[$hotel_code];
    $comparacion_sql = "nombre = ?";
} else {
    $busqueda = $hotel_code;
    $comparacion_sql = "LOWER(REPLACE(nombre, ' ', '')) = ?";
}

$stmt = $conn->prepare("
    SELECT id_hotel, nombre 
    FROM hoteles
    WHERE $comparacion_sql
");
$stmt->bind_param("s", $busqueda);
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
$traslado   = intval($pres["traslado_decimal"]); // 1 = si, 0 = no

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

// Guardamos tarifas en arreglo
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

    $lineas[] = "$cantidad × Habitación $descripcion → $tarifa USD/noche → $subtotal USD";
}

// ===============================
// SUMAR TRASLADO (SI LO PIDIERON)
// ===============================
$costo_traslado = 0;

if ($traslado == 1) {
    $costo_traslado = 15;  // costo fijo
    $total_general += $costo_traslado;
    $lineas[] = "Traslado: 15 USD (opcional)";
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
    <link rel="icon" href="Imagenes/Pagina Logo 2.png">
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

    <h2 style="font-size:3rem; text-align:center; margin-bottom:25px;">
        Resumen del Paquete
    </h2>

    <!-- TARJETA RESUMEN -->
    <div class="card-resumen">

        <h3 class="titulo-form">Hotel Seleccionado</h3>
        <p><b><?php echo $nombre_hotel; ?></b></p>

        <h3 class="titulo-form">Fechas</h3>
        <p><b>Desde:</b> <?php echo $desde; ?></p>
        <p><b>Hasta:</b> <?php echo $hasta; ?></p>
        <p><b>Noches:</b> <?php echo $noches; ?></p>

        <h3 class="titulo-form">Detalles del Viaje</h3>
        <p><b>Personas:</b> <?php echo $personas; ?></p>

        <?php if ($traslado == 1): ?>
            <p><b>Traslado incluido:</b> Sí (15 USD)</p>
        <?php else: ?>
            <p><b>Traslado incluido:</b> No</p>
        <?php endif; ?>

        <h3 class="titulo-form">Distribución de Habitaciones</h3>
        <ul class="lista-habitaciones">
            <?php foreach ($lineas as $linea): ?>
                <li><?php echo $linea; ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="total-box">
            <h2>Total del Paquete</h2>
            <span><?php echo number_format($total_general, 2); ?> USD</span>
        </div>

        <a class="btn btn-confirmar" 
           href="reserva.php?id_presupuesto=<?php echo $id_presupuesto; ?>">
           Confirmar Reserva
        </a>

    </div>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>

