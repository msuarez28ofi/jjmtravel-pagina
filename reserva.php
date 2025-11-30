<?php
// =========================================================================
// RECUPERAR DATOS DE LA RESERVA (Vienen por URL desde paquete.php)
// =========================================================================
$hotel = $_GET["hotel"] ?? "N/A";
$total = $_GET["total"] ?? 0;
$personas = $_GET["personas"] ?? 0;
$noches = $_GET["noches"] ?? 0;

// Datos del turista
$nombre = urldecode($_GET["nombre"] ?? "");
$apellido = urldecode($_GET["apellido"] ?? "");
$cedula = $_GET["cedula"] ?? "N/A";
$telefono = $_GET["telefono"] ?? "N/A";
$correo = $_GET["correo"] ?? "N/A";

// NOTA IMPORTANTE: En este punto, se ejecutaría la lógica de SQL para:
// 1. Verificar disponibilidad de la habitación.
// 2. Insertar el registro del Turista si es nuevo.
// 3. Insertar el registro de la Reserva con el total calculado.

// Simulando la "grabación" exitosa
$exito = true;
// Generar un código de reserva simulado
$codigo_reserva = "RES-" . strtoupper(substr(md5(time()), 0, 6)); 
$fecha_reserva = date("d/m/Y");

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
    <h1>Confirmación de Reserva</h1>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<section class="contenido" style="text-align: center;">

    <?php if ($exito): ?>
        <div class="formulario" style="max-width: 600px; padding: 40px; border-left: 5px solid #2ecc71;">
            <h3 style="color: #2ecc71; margin-top: 0;">¡Reserva Exitosa!</h3>
            <p style="font-size: 18px;">Su paquete turístico ha sido confirmado y registrado en nuestro sistema.</p>

            <table style="width: 100%; margin: 25px 0; text-align: left; border-collapse: collapse;">
                <tr><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><strong>Código de Reserva:</strong></td><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><?php echo $codigo_reserva; ?></td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><strong>Fecha de Reserva:</strong></td><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><?php echo $fecha_reserva; ?></td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><strong>Hotel Seleccionado:</strong></td><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><?php echo $hotel; ?></td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><strong>Duración:</strong></td><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><?php echo $noches; ?> Noches</td></tr>
                <tr><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><strong>N° de Huéspedes:</strong></td><td style="padding: 8px; border-bottom: 1px dashed #ddd;"><?php echo $personas; ?></td></tr>
                <tr><td style="padding: 8px; font-size: 20px; color: #e67e22;"><strong>TOTAL PAGADO:</strong></td><td style="padding: 8px; font-size: 20px; color: #e67e22;"><strong>$<?php echo number_format($total, 2); ?></strong></td></tr>
            </table>

            <h4>Datos del Turista</h4>
            <ul style="list-style: none; padding: 0; text-align: left;">
                <li><strong>Nombre:</strong> <?php echo $nombre . " " . $apellido; ?></li>
                <li><strong>Cédula:</strong> <?php echo $cedula; ?></li>
                <li><strong>Correo:</strong> <?php echo $correo; ?></li>
                <li><strong>Teléfono:</strong> <?php echo $telefono; ?></li>
            </ul>

            <p style="margin-top: 30px; font-style: italic;">Recibirá un correo electrónico con los detalles de su viaje. ¡Gracias por elegirnos!</p>
        </div>
    <?php else: ?>
        <div class="formulario" style="max-width: 500px; padding: 30px; border-left: 5px solid #e74c3c;">
            <h3 style="color: #e74c3c; margin-top: 0;">Error en la Reserva</h3>
            <p>Lo sentimos, no pudimos completar su reserva. Por favor, intente nuevamente o <a href="index.php">vuelva al inicio</a>.</p>
        </div>
    <?php endif; ?>

</section>

</body>
</html>