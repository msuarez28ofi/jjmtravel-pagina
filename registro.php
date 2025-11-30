<?php
require_once "conexion.php";

$errores = [];
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar y validar datos básicos
    $nombre  = trim($_POST['nombre'] ?? '');
    $apellido= trim($_POST['apellido'] ?? '');
    $tipo_doc= intval($_POST['id_tipo_documento'] ?? 1); // por defecto 1 (V)
    $documento = trim($_POST['cedula'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $email     = trim($_POST['correo'] ?? '');

    if ($nombre === '' || $apellido === '') {
        $errores[] = "Nombre y apellido son obligatorios.";
    }
    if ($documento === '') {
        $errores[] = "Número de documento obligatorio.";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Formato de correo no válido.";
    }

    if (empty($errores)) {
        // Preparar llamada al procedimiento almacenado
        // Usaremos multi_query para manejar el SELECT retornado por el SP
        $p_nombre  = $conn->real_escape_string($nombre);
        $p_apellido= $conn->real_escape_string($apellido);
        $p_doc     = $conn->real_escape_string($documento);
        $p_tel     = $conn->real_escape_string($telefono);
        $p_email   = $conn->real_escape_string($email);
        $p_tipo    = intval($tipo_doc);

        // Construir la llamada (usamos parámetros ya escapados)
        $sql = "CALL sp_insertar_turista('$p_nombre', '$p_apellido', $p_tipo, '$p_doc', '$p_tel', '$p_email')";

        if ($conn->multi_query($sql)) {
            // Obtener primer resultado (el SELECT que devuelve id_turista)
            if ($result = $conn->store_result()) {
                $row = $result->fetch_assoc();
                $id_turista = $row['id_turista'] ?? null;
                $result->free();
                // limpiar conjuntos de resultados pendientes
                while ($conn->more_results() && $conn->next_result()) {
                    $extraResult = $conn->store_result();
                    if ($extraResult) { $extraResult->free(); }
                }

                if ($id_turista) {
                    $success = "Turista registrado (o encontrado). ID: " . $id_turista;
                    // Redirigir a la siguiente página (presupuesto) pasando id_turista
                    header("Location: presupuesto.php?id_turista=" . intval($id_turista));
                    exit();
                } else {
                    $errores[] = "No se pudo obtener el ID del turista.";
                }
            } else {
                $errores[] = "Error al ejecutar el procedimiento.";
            }
        } else {
            $errores[] = "Error en la llamada SQL: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro del Turista</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header><h1>Registro del Turista</h1></header>
<section class="contenido">
    <?php if (!empty($errores)): ?>
        <div style="background:#ffd6d6;padding:10px;border-radius:6px;margin-bottom:12px;">
            <?php foreach($errores as $e) echo "<div>- " . htmlspecialchars($e) . "</div>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background:#d6ffd8;padding:10px;border-radius:6px;margin-bottom:12px;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="formulario" novalidate>
        <label>Nombre:</label>
        <input type="text" name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">

        <label>Apellido:</label>
        <input type="text" name="apellido" required value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">

        <label>Tipo de documento:</label>
        <select name="id_tipo_documento">
            <option value="1">V - Venezolano</option>
            <option value="2">E - Extranjero</option>
        </select>

        <label>Cédula / Pasaporte:</label>
        <input type="text" name="cedula" required value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">

        <label>Correo Electrónico:</label>
        <input type="email" name="correo" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>">

        <button type="submit" class="btn">Continuar</button>
    </form>
</section>
<footer><p>© 2025 Agencia de Viajes Margarita</p></footer>
</body>
</html>
