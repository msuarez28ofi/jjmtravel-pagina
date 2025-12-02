<?php
require_once "conexion.php";

// Recibir ID del turista desde registro.php
$id_turista = $_GET['id_turista'] ?? null;

// Recibir hotel desde hotel.php
$hotel = $_GET['hotel'] ?? null;

if (!$id_turista || !$hotel) {
    die("Error: Faltan datos necesarios para continuar la reserva.");
}

// Información básica de tarifas por hotel (igual a tu paquete.php)
$TARIFAS = [
    'puntablanca' => ['Individual' => 60, 'Doble' => 100, 'Triple' => 135],
    'ecoland'     => ['Individual' => 55, 'Doble' => 90,  'Triple' => 120],
    'hesperia'    => ['Individual' => 80, 'Doble' => 130, 'Triple' => 175],
    'aguadorada'  => ['Individual' => 70, 'Doble' => 115, 'Triple' => 150],
];

// Validar hotel correcto
if (!isset($TARIFAS[$hotel])) {
    die("Error: hotel no válido.");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Paquete</title>
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

    <h2>Selecciona los detalles de tu viaje</h2>
    <p>Hotel seleccionado: <strong><?php echo strtoupper($hotel); ?></strong></p>

    <form action="paquete.php" method="GET" class="formulario">

        <!-- Enviar estos datos ocultos -->
        <input type="hidden" name="id_turista" value="<?php echo $id_turista; ?>">
        <input type="hidden" name="hotel" value="<?php echo $hotel; ?>">

        <label>Fecha de entrada:</label>
        <input type="date" name="entrada" required>

        <label>Fecha de salida:</label>
        <input type="date" name="salida" required>

        <label>Habitaciones Individuales (1 pers):</label>
        <input type="number" name="hab_individual" min="0" value="0">

        <label>Habitaciones Dobles (2 pers):</label>
        <input type="number" name="hab_doble" min="0" value="0">

        <label>Habitaciones Triples (3 pers):</label>
        <input type="number" name="hab_triple" min="0" value="0">

        <label>¿Desea traslado aeropuerto/hotel? (+$50)</label>
        <select name="traslado">
            <option value="no">No</option>
            <option value="si">Sí</option>
        </select>

        <button type="submit" class="btn">Calcular Paquete</button>

    </form>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
