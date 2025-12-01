<?php
require "conexion.php";

/* ==========================================================
   MAPA ENTRE LAS CLAVES TEXTUALES Y LOS ID DE TU BASE REAL
   Esto mantiene tu DISEÑO ORIGINAL funcionando sin cambios.
   ========================================================== */
$hotel_map = [
    "puntablanca" => 1,
    "ecoland"     => 2,
    "hesperia"    => 3,
    "aguadorada"  => 4
];

// Recibir la clave textual desde hotel.php
$hotel_key = $_GET["hotel"] ?? "";

// Validar clave
if (!isset($hotel_map[$hotel_key])) {
    die("Error: hotel no válido.");
}

// Obtener el id_hotel real de la BD
$id_hotel = $hotel_map[$hotel_key];

/* ==========================================================
   OBTENER INFORMACIÓN DEL HOTEL DESDE LA BASE DE DATOS
   ========================================================== */
$sql_hotel = "SELECT * FROM hoteles WHERE id_hotel = $id_hotel";
$res_hotel = $conn->query($sql_hotel);
$hotel = $res_hotel->fetch_assoc();

if (!$hotel) {
    die("Error: hotel no encontrado en la base de datos.");
}

/* ==========================================================
   OBTENER TIPOS DE HABITACIÓN DISPONIBLES
   (Individual, Doble, Triple, Cuádruple)
   ========================================================== */
$sql_tipos = "SELECT * FROM tipo_habitaciones ORDER BY id_tipo_habitacion";
$res_tipos = $conn->query($sql_tipos);

/* ==========================================================
   SI AÚN NO HAN ENVIADO EL FORMULARIO, MOSTRARLO
   ========================================================== */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Paquete</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header>
    <h1>Reservar en <?php echo $hotel["nombre"]; ?></h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
        </ul>
    </nav>
</header>

<section class="contenido">

    <h3>Seleccione fechas y habitaciones</h3>

    <form method="POST" class="formulario">

        <input type="hidden" name="id_hotel" value="<?php echo $id_hotel; ?>">
        <input type="hidden" name="hotel_key" value="<?php echo $hotel_key; ?>">

        <h3>Fechas</h3>

        <label>Entrada:</label>
        <input type="date" name="entrada" required>

        <label>Salida:</label>
        <input type="date" name="salida" required>

        <label>Cantidad de Personas:</label>
        <input type="number" min="1" name="cantidad_personas" required>

        <h3>Habitaciones</h3>

        <?php while ($tipo = $res_tipos->fetch_assoc()): ?>
            <label><?php echo $tipo["descripcion"]; ?> (capacidad <?php echo $tipo["capacidad_maxima"]; ?>):</label>
            <input type="number" name="hab_<?php echo $tipo["id_tipo_habitacion"]; ?>" value="0" min="0">
        <?php endwhile; ?>

        <button type="submit" class="btn">Calcular</button>
    </form>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>

<?php
    exit();
}

/* ==========================================================
   PROCESAR FORMULARIO → CALCULAR PRECIOS REALES
   ========================================================== */

$id_hotel = $_POST["id_hotel"];
$hotel_key = $_POST["hotel_key"];
$entrada = $_POST["entrada"];
$salida = $_POST["salida"];
$cantidad_personas = intval($_POST["cantidad_personas"]);

// 1) Insertar presupuesto (aquí NO se conoce el monto aún)
$sql_insert_pres = "
    INSERT INTO presupuesto_reservas (id_turista, id_tarifario, fecha_reserva_desde, fecha_reserva_hasta, cantidad_personas)
    VALUES (1, 1, '$entrada', '$salida', $cantidad_personas)
";
$conn->query($sql_insert_pres);

$id_presupuesto = $conn->insert_id;

// 2) Volver a leer tipos
$sql_tipos = "SELECT * FROM tipo_habitaciones ORDER BY id_tipo_habitacion";
$res_tipos = $conn->query($sql_tipos);

$total_final = 0;
$detalles = [];

// 3) Procesar habitaciones seleccionadas
while ($tipo = $res_tipos->fetch_assoc()) {

    $id_tipo = $tipo["id_tipo_habitacion"];
    $cantidad = intval($_POST["hab_$id_tipo"]);

    if ($cantidad > 0) {

        // Buscar la tarifa real según el hotel
        $sql_tarifa = "
            SELECT tarifa 
            FROM tarifarios 
            WHERE id_hotel = $id_hotel 
              AND id_tipo_habitacion = $id_tipo
            LIMIT 1
        ";
        $res_tarifa = $conn->query($sql_tarifa);
        $tarifa = $res_tarifa->fetch_assoc()["tarifa"];

        // Insertar detalle
        $sql_ins_det = "
            INSERT INTO detalle_habitaciones_presupuesto (id_presupuesto, id_tipo_habitacion, cantidad_habitaciones)
            VALUES ($id_presupuesto, $id_tipo, $cantidad)
        ";
        $conn->query($sql_ins_det);

        $detalles[] = [
            "tipo" => $tipo["descripcion"],
            "cantidad" => $cantidad,
            "tarifa" => $tarifa
        ];

        $total_final += $cantidad * $tarifa;
    }
}

// 4) Actualizar monto total del presupuesto
$sql_upd = "
    UPDATE presupuesto_reservas 
    SET monto_total = $total_final
    WHERE id_presupuesto = $id_presupuesto
";
$conn->query($sql_upd);

// 5) Redirigir a reserva final
header("Location: reserva.php?id_presupuesto=$id_presupuesto&hotel=$hotel_key");
exit();

?>
