<?php
// Recibir datos del turista desde registro.php
$nombre = $_GET["nombre"] ?? "N/A";
$apellido = $_GET["apellido"] ?? "N/A";
$cedula = $_GET["cedula"] ?? "N/A";
$ubicacion = $_GET["ubicacion"] ?? "N/A";
$telefono = $_GET["telefono"] ?? "N/A";
$correo = $_GET["correo"] ?? "N/A";
// Clave del hotel que viene de registro.php. Ejemplo: 'puntablanca'
$hotel_seleccionado_key = $_GET["hotel"] ?? ''; 

// =======================================================
// LÓGICA DE TARIFAS POR TIPO DE HABITACIÓN (SIMULADA)
// =======================================================

// Costos de Traslado y Tipos de Habitación
$COSTO_TRASLADO = 50; 
$costoTrasladoAplicado = 0; 

// Tarifas Base por NOCHE y por TIPO DE HABITACIÓN
$TARIFAS_HABITACION = [
    'puntablanca' => ['nombre' => 'SUNSOL PUNTA BLANCA', 'Individual' => 60, 'Doble' => 100, 'Triple' => 135],
    'ecoland'     => ['nombre' => 'SUNSOL ECOLAND', 'Individual' => 55, 'Doble' => 90, 'Triple' => 120],
    'hesperia'    => ['nombre' => 'HOTEL HESPERIA', 'Individual' => 80, 'Doble' => 130, 'Triple' => 175],
    'aguadorada'  => ['nombre' => 'HOTEL AGUA DORADA', 'Individual' => 70, 'Doble' => 115, 'Triple' => 150],
];

// Validamos si la clave del hotel recibido existe en nuestras tarifas
if (!array_key_exists($hotel_seleccionado_key, $TARIFAS_HABITACION)) {
    // Si la clave no existe o no se recibió, forzamos un mensaje de error y no mostramos el formulario
    $error_hotel = "Error: El hotel seleccionado no es válido o no se especificó.";
} else {
    $error_hotel = null;
}


// Cuando el usuario envíe la información del viaje (el formulario en esta misma página)
if ($_SERVER["REQUEST_METHOD"] === "POST" && $error_hotel === null) {

    // Datos del formulario de viaje
    $num_ind = (int)$_POST["hab_individual"];
    $num_dob = (int)$_POST["hab_doble"];
    $num_tri = (int)$_POST["hab_triple"];
    $entrada = $_POST["entrada"];
    $salida = $_POST["salida"];
    $traslado = $_POST["traslado"] ?? "no"; 

    // Cálculo del total de personas y habitaciones
    $total_habitaciones = $num_ind + $num_dob + $num_tri;
    $total_personas = ($num_ind * 1) + ($num_dob * 2) + ($num_tri * 3);

    if ($total_habitaciones === 0) {
        die("Error: Debe seleccionar al menos una habitación para continuar la reserva.");
    }
    
    // Validar que las fechas sean válidas y calcular días y noches
    $fecha1 = strtotime($entrada);
    $fecha2 = strtotime($salida);
    $dias = ($fecha2 - $fecha1) / 86400;
    $noches = $dias; // Para fines de cálculo, noches = días

    if ($dias < 1) {
        die("Error: La fecha de salida debe ser mayor que la de entrada.");
    }

    // LÓGICA DE TRASLADO: Aplicar el costo si el usuario lo marcó
    if ($traslado === "si") {
        $costoTrasladoAplicado = $COSTO_TRASLADO;
    }
    
    // =======================================================
    // CALCULAR TOTALES POR EL HOTEL SELECCIONADO
    // =======================================================
    $tarifa_hotel = $TARIFAS_HABITACION[$hotel_seleccionado_key];

    $costo_base_habitaciones = 0;
        
    // Sumar costo de Habitaciones Individuales
    $costo_base_habitaciones += $num_ind * $tarifa_hotel['Individual'] * $noches;
        
    // Sumar costo de Habitaciones Dobles
    $costo_base_habitaciones += $num_dob * $tarifa_hotel['Doble'] * $noches;
        
    // Sumar costo de Habitaciones Triples
    $costo_base_habitaciones += $num_tri * $tarifa_hotel['Triple'] * $noches;
        
    // Costo Final = Costo Habitaciones + Costo Traslado
    $total_final = $costo_base_habitaciones + $costoTrasladoAplicado;

    // Guardamos el nombre legible del hotel
    $nombre_hotel_legible = $tarifa_hotel['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selección de Paquete</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <h1>Paquetes Disponibles</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<section class="contenido">

<?php if ($error_hotel !== null): ?>
    <h3>Error en la Reserva</h3>
    <p class="error-mensaje"><?php echo $error_hotel; ?></p>
    <p>Por favor, regrese al <a href="agencia.php">Inicio</a> y seleccione un hotel válido.</p>

<?php elseif (!isset($total_final)): ?>
    
    <!-- PANTALLA INICIAL PARA SELECCIONAR HABITACIONES Y FECHAS -->
    
    <h3>Datos del Viaje en: **<?php echo $TARIFAS_HABITACION[$hotel_seleccionado_key]['nombre']; ?>**</h3>
    <p>Hola **<?php echo $nombre . " " . $apellido; ?>**, ingresa las fechas y la distribución de habitaciones que deseas.</p>

    <form method="POST" class="formulario">

        <!-- GRUPO DE SELECCIÓN DE HABITACIONES -->
        <fieldset class="grupo-habitaciones">
            <legend>Selección de Habitaciones 🏨</legend>
            
            <label>Habitaciones Individuales (1 pers. - $<?php echo $TARIFAS_HABITACION[$hotel_seleccionado_key]['Individual']; ?>/noche):</label>
            <input type="number" name="hab_individual" min="0" value="0">

            <label>Habitaciones Dobles (2 pers. - $<?php echo $TARIFAS_HABITACION[$hotel_seleccionado_key]['Doble']; ?>/noche):</label>
            <input type="number" name="hab_doble" min="0" value="0">
            
            <label>Habitaciones Triples (3 pers. - $<?php echo $TARIFAS_HABITACION[$hotel_seleccionado_key]['Triple']; ?>/noche):</label>
            <input type="number" name="hab_triple" min="0" value="0">
        </fieldset>
        <!-- FIN GRUPO DE SELECCIÓN -->

        <label>Fecha de entrada:</label>
        <input type="date" name="entrada" required>

        <label>Fecha de salida:</label>
        <input type="date" name="salida" required>
        
        <!-- Campo para preguntar por Traslado -->
        <label>¿Desea traslado Aeropuerto/Puerto - Hotel (Costo fijo: $<?php echo $COSTO_TRASLADO; ?>)?</label>
        <select name="traslado" required>
            <option value="no">No</option>
            <option value="si">Sí</option>
        </select>

        <button type="submit" class="btn">Calcular</button>
    </form>

<?php else: ?>

    <!-- PANTALLA DE RESULTADOS Y RESERVA FINAL -->
    
    <h3>Cálculo de Reserva para **<?php echo $nombre_hotel_legible; ?>**</h3>
    <p>Calculado para **<?php echo $total_personas; ?>** personas, en **<?php echo $total_habitaciones; ?>** habitaciones durante **<?php echo $dias; ?>** días / **<?php echo $noches; ?>** noches.
    </p>
    
    <div class="distribucion-resumen">
        <p>Distribución seleccionada:</p>
        <ul>
            <?php if ($num_ind > 0) echo "<li>{$num_ind} Habitación(es) Individual(es)</li>"; ?>
            <?php if ($num_dob > 0) echo "<li>{$num_dob} Habitación(es) Doble(s)</li>"; ?>
            <?php if ($num_tri > 0) echo "<li>{$num_tri} Habitación(es) Triple(s)</li>"; ?>
        </ul>
    </div>

    <?php if ($costoTrasladoAplicado > 0): ?>
        <p class="nota-traslado">✅ Traslado Incluido: Se ha añadido un costo de $<?php echo $costoTrasladoAplicado; ?>.</p>
    <?php else: ?>
        <p class="nota-traslado">❌ Traslado No Incluido.</p>
    <?php endif; ?>

    <div class="card card-reserva-final">
        <h4>Total a Pagar en <?php echo $nombre_hotel_legible; ?></h4>
        <p>Monto Total: <strong>$<?php echo number_format($total_final, 2); ?></strong></p>

        <!-- Redirección a reservar.php con todos los datos necesarios -->
        <a class="btn"
            href="reservar.php?hotel=<?php echo $hotel_seleccionado_key; ?>&total=<?php echo $total_final; ?>&personas=<?php echo $total_personas; ?>&dias=<?php echo $dias; ?>&noches=<?php echo $noches; ?>&entrada=<?php echo $entrada; ?>&salida=<?php echo $salida; ?>&nombre=<?php echo $nombre; ?>&apellido=<?php echo $apellido; ?>&cedula=<?php echo $cedula; ?>&telefono=<?php echo $telefono; ?>&correo=<?php echo $correo; ?>&traslado=<?php echo $costoTrasladoAplicado; ?>&num_ind=<?php echo $num_ind; ?>&num_dob=<?php echo $num_dob; ?>&num_tri=<?php echo $num_tri; ?>">
            Confirmar Reserva
        </a>
    </div>

<?php endif; ?>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>