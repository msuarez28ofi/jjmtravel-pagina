<?php
require "conexion.php";

/* ==========================================================
   VALIDAR ID DEL PRESUPUESTO
   ========================================================== */
$id_presupuesto = $_GET["id_presupuesto"] ?? 0;
$hotel_key = $_GET["hotel"] ?? "";

if ($id_presupuesto == 0) {
    die("Error: presupuesto no válido.");
}

/* ==========================================================
   MAPA ENTRE CLAVE TEXTUAL Y ID HOTEL REAL
   ========================================================== */
$hotel_map = [
    "puntablanca" => 1,
    "ecoland"     => 2,
    "hesperia"    => 3,
    "aguadorada"  => 4
];

$id_hotel = $hotel_map[$hotel_key] ?? 0;

/* ==========================================================
   CARGAR PRESUPUESTO DESDE LA BASE DE DATOS
   ========================================================== */
$sql_pre = "
    SELECT *
    FROM presupuesto_reservas
    WHERE id_presupuesto = $id_presupuesto
    LIMIT 1
";
$res_pre = $conn->query($sql_pre);
$pres = $res_pre->fetch_assoc();

if (!$pres) {
    die("Error: presupuesto no encontrado.");
}

/* ==========================================================
   CARGAR DETALLES DEL PRESUPUESTO
   ========================================================== */
$sql_det = "
    SELECT dhp.*, th.descripcion, th.capacidad_maxima
    FROM detalle_habitaciones_presupuesto dhp
    INNER JOIN tipo_habitaciones th ON dhp.id_tipo_habitacion = th.id_tipo_habitacion
    WHERE dhp.id_presupuesto = $id_presupuesto
";
$res_det = $conn->query($sql_det);

/* ==========================================================
   CARGAR INFORMACIÓN DEL HOTEL
   ========================================================== */
$sql_hotel = "SELECT * FROM hoteles WHERE id_hotel = $id_hotel";
$res_hotel = $conn->query($sql_hotel);
$hotel = $res_hotel->fetch_assoc();

/* ==========================================================
   CREAR LA RESERVA FINAL
   ========================================================== */
$codigo = "RES-" . strtoupper(substr(md5(time()), 0, 6));
$monto_pagar = $pres["monto_total"];

$sql_insert = "
    INSERT INTO reservas (id_presupuesto, codigo_reserva, monto_pagar, status)
    VALUES ($id_presupuesto, '$codigo', $monto_pagar, 'PENDIENTE')
";

$conn->query($sql_insert);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header>
    <h1>Reserva Confirmada</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<section class="contenido">

    <div class="comprobante">

        <h2>Su Reserva ha sido Generada</h2>

        <h3>Detalles del Hotel</h3>
        <table>
            <tr><td>Hotel:</td><td><?php echo $hotel["nombre"]; ?></td></tr>
            <tr><td>Dirección:</td><td><?php echo $hotel["direccion"]; ?></td></tr>
            <tr><td>Categoría:</td><td><?php echo $hotel["categoria"]; ?> estrellas</td></tr>
        </table>

        <h3>Fechas</h3>
        <table>
            <tr><td>Entrada:</td><td><?php echo $pres["fecha_reserva_desde"]; ?></td></tr>
            <tr><td>Salida:</td><td><?php echo $pres["fecha_reserva_hasta"]; ?></td></tr>
            <tr><td>Días:</td><td><?php echo $pres["cantidad_dias"]; ?></td></tr>
            <tr><td>Noches:</td><td><?php echo $pres["cantidad_noches"]; ?></td></tr>
        </table>

        <h3>Habitaciones Seleccionadas</h3>
        <table>
            <?php while ($row = $res_det->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["descripcion"]; ?></td>
                    <td><?php echo $row["cantidad_habitaciones"]; ?> hab.</td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Código de Reserva</h3>
        <table>
            <tr><td>Código:</td><td><strong><?php echo $codigo; ?></strong></td></tr>
            <tr><td>Estado:</td><td>PENDIENTE</td></tr>
        </table>

        <div class="total-final">
            <h4>Total a Pagar</h4>
            <span>$<?php echo number_format($pres["monto_total"], 2); ?></span>
        </div>

        <p class="nota-final">
            Gracias por reservar con JJM TRAVEL. Pronto nos comunicaremos con usted.
        </p>

    </div>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
