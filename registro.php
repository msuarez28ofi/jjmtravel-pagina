<?php
require_once "conexion.php";

/*
  registro.php FINAL
  -------------------
  - Requiere: ?hotel=xxx&id_turista=NN
  - Si no viene id_turista → redirige a turista.php
  - Valida fechas, inventario, capacidad
  - Obtiene tarifario real para evitar error de FK
  - Inserta presupuesto + detalle + descuenta inventario
  - Mantiene diseño sin cambios
*/

// ---------------------------
// MAPEO SOLO PARA LOS QUE FALLABAN
// ---------------------------
$mapa_hoteles = [
    "puntablanca" => "SUNSOL PUNTA BLANCA",
    "ecoland"     => "SUNSOL ECOLAND"
];

// ---------------------------
// RECIBIR PARAMS
// ---------------------------
$hotel_code = $_GET["hotel"] ?? "";
$id_turista = intval($_GET["id_turista"] ?? 0);

if ($hotel_code === "") {
    die("Error: hotel no recibido.");
}

// Si no tenemos id_turista → primero registrar turista
if ($id_turista <= 0) {
    header("Location: turista.php?hotel=" . urlencode($hotel_code));
    exit;
}

// ---------------------------
// RESOLVER NOMBRE DEL HOTEL
// ---------------------------
if (isset($mapa_hoteles[$hotel_code])) {
    $busqueda = $mapa_hoteles[$hotel_code];
    $comparacion_sql = "nombre = ?";
} else {
    $busqueda = strtolower(str_replace(" ", "", $hotel_code));
    $comparacion_sql = "LOWER(REPLACE(nombre, ' ', '')) = ?";
}

// ---------------------------
// OBTENER HOTEL DESDE BD
// ---------------------------
$query = "SELECT id_hotel, nombre FROM hoteles WHERE $comparacion_sql";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $busqueda);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Error: hotel no existe en BD.'); history.back();</script>";
    exit;
}

$hotel_db = $result->fetch_assoc();
$id_hotel = $hotel_db["id_hotel"];
$nombre_hotel = $hotel_db["nombre"];

// ---------------------------------
// PROCESAR FORMULARIO
// ---------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fecha_desde = $_POST["desde"] ?? "";
    $fecha_hasta = $_POST["hasta"] ?? "";
    $cantidad_personas = intval($_POST["personas"] ?? 0);

    $cant_indiv     = intval($_POST["hab_individual"] ?? 0);
    $cant_doble     = intval($_POST["hab_doble"] ?? 0);
    $cant_triple    = intval($_POST["hab_triple"] ?? 0);
    $cant_cuadruple = intval($_POST["hab_cuadruple"] ?? 0);

    $traslado = isset($_POST["traslado"]) ? 1 : 0;

    // ---------- validar fechas ----------
    if ($fecha_desde === "" || $fecha_hasta === "") {
        echo "<script>alert('Debe seleccionar fechas válidas.'); history.back();</script>";
        exit;
    }

    $ts_desde = strtotime($fecha_desde);
    $ts_hasta = strtotime($fecha_hasta);

    if ($ts_desde === false || $ts_hasta === false) {
        echo "<script>alert('Formato de fecha inválido.'); history.back();</script>";
        exit;
    }

    if ($ts_desde >= $ts_hasta) {
        echo "<script>alert('La fecha de inicio debe ser menor a la fecha final.'); history.back();</script>";
        exit;
    }

    $noches = intval(($ts_hasta - $ts_desde) / 86400);
    if ($noches <= 0) {
        echo "<script>alert('Debe seleccionar mínimo 1 noche.'); history.back();</script>";
        exit;
    }

    if ($cantidad_personas <= 0) {
        echo "<script>alert('Debe indicar la cantidad de personas.'); history.back();</script>";
        exit;
    }

    // ---------- obtener tarifario ----------
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
        echo "<script>alert('El hotel no tiene tarifas registradas.'); history.back();</script>";
        exit;
    }

    $id_tarifario = $res_tar->fetch_assoc()["id_tarifario"];

    // ---------- inventario ----------
    $stock = [];
    $res_inv = $conn->query("SELECT * FROM inventario_habitaciones WHERE id_hotel = $id_hotel");

    while ($row = $res_inv->fetch_assoc()) {
        $stock[$row["id_tipo_habitacion"]] = intval($row["habitaciones_disponibles"]);
    }

    // ---------- capacidad ----------
    $tipos_q = $conn->query("SELECT * FROM tipo_habitaciones");
    $capacidad_total = 0;
    $detalles = [];

    while ($row = $tipos_q->fetch_assoc()) {
        $id_tipo = $row["id_tipo_habitacion"];
        $desc = strtolower($row["descripcion"]);
        $cap = intval($row["capacidad_maxima"]);

        $cant = 0;
        if ($desc === "individual")  $cant = $cant_indiv;
        if ($desc === "doble")       $cant = $cant_doble;
        if ($desc === "triple")      $cant = $cant_triple;
        if ($desc === "cuadruple")   $cant = $cant_cuadruple;

        if ($cant > 0) {
            $detalles[$id_tipo] = $cant;
            $capacidad_total += $cant * $cap;
        }
    }

    if ($capacidad_total < $cantidad_personas) {
        echo "<script>alert('La capacidad total ($capacidad_total) es menor que las personas ($cantidad_personas).'); history.back();</script>";
        exit;
    }

    // ---------- validar stock ----------
    foreach ($detalles as $id_tipo => $cant) {
        if ($cant > $stock[$id_tipo]) {
            echo "<script>alert('No hay suficientes habitaciones del tipo $id_tipo.'); history.back();</script>";
            exit;
        }
    }

    // ---------- insertar presupuesto ----------
    $stmt_ins = $conn->prepare("
        INSERT INTO presupuesto_reservas
        (id_turista, id_tarifario, fecha_reserva_desde, fecha_reserva_hasta, 
         cantidad_noches, cantidad_personas, traslado_decimal, monto_total)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt_ins->bind_param("iissiii", 
        $id_turista, $id_tarifario, 
        $fecha_desde, $fecha_hasta, 
        $noches, $cantidad_personas, $traslado
    );
    $stmt_ins->execute();

    $id_presupuesto = $conn->insert_id;

    // ---------- insertar detalle + descontar inventario ----------
    foreach ($detalles as $id_tipo => $cant) {

        // detalle
        $stmt_det = $conn->prepare("
            INSERT INTO detalle_habitaciones_presupuesto
            (id_presupuesto, id_tipo_habitacion, cantidad_habitaciones)
            VALUES (?, ?, ?)
        ");
        $stmt_det->bind_param("iii", $id_presupuesto, $id_tipo, $cant);
        $stmt_det->execute();

        // descontar
        $stmt_up = $conn->prepare("
            UPDATE inventario_habitaciones
            SET habitaciones_disponibles = habitaciones_disponibles - ?
            WHERE id_hotel = ? AND id_tipo_habitacion = ?
        ");
        $stmt_up->bind_param("iii", $cant, $id_hotel, $id_tipo);
        $stmt_up->execute();
    }

    // ---------- redirigir ----------
    header("Location: paquete.php?id_presupuesto=$id_presupuesto&hotel=$hotel_code");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Reserva</title>
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
        Reservar en: <?php echo htmlspecialchars($nombre_hotel); ?>
    </h2>

    <!-- FORMULARIO MODERNO Y VERTICAL -->
    <form action="" method="POST" class="form-reserva-vertical">

        <h3 class="titulo-form">Datos de la Reserva</h3>

        <label>Fecha desde:</label>
        <input type="date" name="desde" required>

        <label>Fecha hasta:</label>
        <input type="date" name="hasta" required>

        <label>Cantidad de personas:</label>
        <input type="number" name="personas" min="1" required>

        <h3 class="titulo-form">Habitaciones</h3>

        <label>Individual:</label>
        <input type="number" name="hab_individual" min="0" value="0">

        <label>Doble:</label>
        <input type="number" name="hab_doble" min="0" value="0">

        <label>Triple:</label>
        <input type="number" name="hab_triple" min="0" value="0">

        <label>Cuádruple:</label>
        <input type="number" name="hab_cuadruple" min="0" value="0">

        <label style="margin-top:15px; display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="traslado">
            Incluir traslado (15 USD)
        </label>

        <button type="submit" class="btn btn-reserva">Continuar</button>

    </form>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
