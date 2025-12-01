<?php
require_once "conexion.php";

// ===============================================
// VALIDAR ID DEL PRESUPUESTO
// ===============================================
$id_presupuesto = $_GET["id_presupuesto"] ?? 0;

if ($id_presupuesto == 0) {
    die("Error: no se recibió el presupuesto.");
}

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

if (!$pres) {
    die("Error: el presupuesto no existe.");
}

$monto_total = $pres["monto_total"];

// ===============================================
// ¿YA EXISTE UNA RESERVA PARA ESTE PRESUPUESTO?
// ===============================================
$rs = $conn->query("
    SELECT * 
    FROM reservas
    WHERE id_presupuesto = $id_presupuesto
");

if ($rs->num_rows > 0) {
    // Ya existe reserva → mostrarla
    $reserva = $rs->fetch_assoc();
    $codigo_reserva = $reserva["codigo_reserva"];
    $status = $reserva["status"];
} else {

    // ===============================================
    // CREAR NUEVO CÓDIGO DE RESERVA
    // ===============================================
    $generated = rand(10000, 99999);
    $codigo_reserva = "RSV-" . $generated;

    // ===============================================
    // INSERTAR LA RESERVA
    // ===============================================
    $stmt = $conn->prepare("
        INSERT INTO reservas 
            (id_presupuesto, codigo_reserva, monto_pagar, status)
        VALUES 
            (?, ?, ?, 'PENDIENTE')
    ");
    $stmt->bind_param("isd", $id_presupuesto, $codigo_reserva, $monto_total);
    $stmt->execute();

    $status = "PENDIENTE";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Reserva</title>
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

    <h2 style="font-size:3rem;">¡Reserva generada con éxito!</h2>

    <div class="card-reserva">
        <p><b>Código de reserva:</b> <?php echo $codigo_reserva; ?></p>
        <p><b>Monto total:</b> <?php echo number_format($monto_total, 2); ?> USD</p>
        <p><b>Status actual:</b> <?php echo $status; ?></p>
        <p><b>Fecha de creación:</b> <?php echo date("Y-m-d H:i"); ?></p>
    </div>

    <br><br>

    <a href="agencia.php" class="btn">Volver al inicio</a>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
