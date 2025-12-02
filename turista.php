<?php
require_once "conexion.php";

// ===============================================
// VALIDAR HOTEL RECIBIDO
// ===============================================
$hotel_code = $_GET["hotel"] ?? "";

if ($hotel_code === "") {
    die("Error: hotel no recibido.");
}

// Este parámetro se enviará a registro.php luego
// por lo tanto NO TOCAR
// $hotel_code = "ecoland" / "puntablanca" / etc.

// ===============================================
// SI EL FORMULARIO VIENE EN POST → PROCESAR
// ===============================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $apellido = trim($_POST["apellido"]);
    $tipo_doc = intval($_POST["tipo_documento"]);
    $num_doc = trim($_POST["numero_documento"]);
    $telefono = trim($_POST["telefono"]);
    $email = trim($_POST["email"]);

    // ===== VALIDACIÓN SIMPLE =====
    if ($nombre === "" || $apellido === "" || $num_doc === "" || $telefono === "" || $email === "") {
        echo "<script>alert('Debe completar todos los campos.'); history.back();</script>";
        exit;
    }

    // ===============================================
    // LLAMAR AL PROCEDIMIENTO sp_insertar_turista
    // ===============================================
    /*
        sp_insertar_turista(
            nombre,
            apellido,
            id_tipo_documento,
            numero_documento,
            telefono,
            email
        )
    */

    $stmt = $conn->prepare("CALL sp_insertar_turista(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $nombre, $apellido, $tipo_doc, $num_doc, $telefono, $email);
    $stmt->execute();

    // El SP devuelve SIEMPRE el id_turista (nuevo o existente)
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Error al obtener ID del turista desde el procedimiento.");
    }

    $id_turista = $row["id_turista"];

    // ===============================================
    // REDIRECCIÓN A registro.php
    // ===============================================
    header("Location: registro.php?hotel=$hotel_code&id_turista=$id_turista");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos del Turista</title>
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
    <h2 style="font-size:3rem;">Datos del Turista</h2>

    <form method="POST" class="formulario-reserva">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Apellido:</label>
        <input type="text" name="apellido" required>

        <label>Tipo de documento:</label>
        <select name="tipo_documento" required>
            <?php
            // Cargar tipos de documento desde la BD
            $docs = $conn->query("SELECT * FROM tipo_documento ORDER BY descripcion");
            while ($d = $docs->fetch_assoc()):
            ?>
                <option value="<?php echo $d['id_tipo_documento']; ?>">
                    <?php echo $d['descripcion']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Número de documento:</label>
        <input type="text" name="numero_documento" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <button type="submit" class="btn">Continuar</button>

    </form>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
