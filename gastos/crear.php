<?php
require_once '../includes/conexion.php';

$sqlPedidos = "SELECT id_pedido FROM pedidos";
$stmtPedidos = $conexion->query($sqlPedidos);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = filter_input(INPUT_POST, 'id_pedido', FILTER_VALIDATE_INT);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
    $fecha = filter_input(INPUT_POST, 'fecha_gasto', FILTER_SANITIZE_STRING);

    if ($id_pedido && $descripcion && $monto && $monto > 0 && $fecha) {
        $sql = "INSERT INTO gastos (id_pedido, descripcion, monto, fecha_gasto) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_pedido, $descripcion, $monto, $fecha]);
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
    <title>Agregar Gasto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Agregar Gasto</h1>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_pedido" class="form-label">Pedido:</label>
                    <select class="form-select" id="id_pedido" name="id_pedido" required>
                        <option value="">Seleccione un pedido</option>
                        <?php foreach ($pedidos as $pedido): ?>
                            <option value="<?= $pedido['id_pedido'] ?>">Pedido #<?= $pedido['id_pedido'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
                <div class="mb-3">
                    <label for="monto" class="form-label">Monto:</label>
                    <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_gasto" class="form-label">Fecha:</label>
                    <input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto" required>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Guardar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>