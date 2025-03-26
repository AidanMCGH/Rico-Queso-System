<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_cliente = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_cliente) {
    header('Location: index.php');
    exit();
}

$sqlCliente = "SELECT * FROM clientes WHERE id_cliente = ?";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->execute([$id_cliente]);
$cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $contacto = filter_input(INPUT_POST, 'contacto', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $fecha = filter_input(INPUT_POST, 'fecha_Nacimiento', FILTER_SANITIZE_STRING);
    $precio_por_kilo = filter_input(INPUT_POST, 'precio_por_kilo', FILTER_VALIDATE_FLOAT);
    $notas = filter_input(INPUT_POST, 'notas', FILTER_SANITIZE_STRING);

    if ($nombre && $contacto && $precio_por_kilo !== false) {
        $sql = "UPDATE clientes SET nombre = ?, contacto = ?, direccion = ?, fecha_Nacimiento = ?, notas = ?, precio_por_kilo = ? WHERE id_cliente = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $contacto, $direccion, $fecha, $notas, $precio_por_kilo, $id_cliente]);
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Editar Cliente</h1>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $cliente['nombre'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_Nacimiento" class="form-label">Fecha de Nacimiento:</label>
                    <input type="date" class="form-control" id="fecha_Nacimiento" name="fecha_Nacimiento" value="<?= $cliente['fecha_Nacimiento'] ?>">
                </div>
                <div class="mb-3">
                    <label for="contacto" class="form-label">Contacto:</label>
                    <input type="text" class="form-control" id="contacto" name="contacto" value="<?= $cliente['contacto'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?= $cliente['direccion'] ?>">
                </div>
                <div class="mb-3">
                    <label for="notas" class="form-label">Notas:</label>
                    <textarea class="form-control" id="notas" name="notas" rows="3"><?= $cliente['notas'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="precio_por_kilo" class="form-label">Precio por Kilo:</label>
                    <input type="number" step="0.01" class="form-control" id="precio_por_kilo" name="precio_por_kilo" value="<?= $cliente['precio_por_kilo'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Actualizar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>