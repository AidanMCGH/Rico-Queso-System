<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_pedido) {
    header('Location: index.php');
    exit();
}

$sqlPedido = "SELECT * FROM pedidos WHERE id_pedido = ?";
$stmtPedido = $conexion->prepare($sqlPedido);
$stmtPedido->execute([$id_pedido]);
$pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

$sqlClientes = "SELECT id_cliente, nombre FROM clientes";
$stmtClientes = $conexion->query($sqlClientes);
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

$sqlLotes = "
    SELECT l.id_lote, l.fecha, l.kilos_producidos,
           (l.kilos_producidos - COALESCE(SUM(il.kilos_usados), 0) + 
            COALESCE((SELECT kilos_usados FROM inventario_lotes WHERE id_pedido = ?), 0)) AS kilos_disponibles
    FROM lotes l
    LEFT JOIN inventario_lotes il ON l.id_lote = il.id_lote
    WHERE l.kilos_producidos IS NOT NULL
    GROUP BY l.id_lote, l.fecha, l.kilos_producidos
    HAVING kilos_disponibles > 0";
$stmtLotes = $conexion->prepare($sqlLotes);
$stmtLotes->execute([$id_pedido]);
$lotes = $stmtLotes->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT);
    $id_lote = filter_input(INPUT_POST, 'id_lote', FILTER_VALIDATE_INT);
    $fecha_entrega = filter_input(INPUT_POST, 'fecha_entrega', FILTER_SANITIZE_STRING);
    $kilos = filter_input(INPUT_POST, 'cantidad_kilos', FILTER_VALIDATE_FLOAT);

    if ($id_cliente && $id_lote && $fecha_entrega && $kilos && $kilos > 0) {
        $sqlPrecio = "SELECT precio_por_kilo FROM clientes WHERE id_cliente = ?";
        $stmtPrecio = $conexion->prepare($sqlPrecio);
        $stmtPrecio->execute([$id_cliente]);
        $precio_por_kilo = $stmtPrecio->fetchColumn();

        $precio_total = $precio_por_kilo * $kilos;

        $sqlInventario = "
            SELECT (kilos_producidos - COALESCE(SUM(il.kilos_usados), 0) + 
                    COALESCE((SELECT kilos_usados FROM inventario_lotes WHERE id_pedido = ?), 0)) AS disponible
            FROM lotes l
            LEFT JOIN inventario_lotes il ON l.id_lote = il.id_lote
            WHERE l.id_lote = ?
            GROUP BY l.id_lote, l.kilos_producidos";
        $stmtInventario = $conexion->prepare($sqlInventario);
        $stmtInventario->execute([$id_pedido, $id_lote]);
        $disponible = $stmtInventario->fetchColumn();

        if ($disponible >= $kilos) {
            $sql = "UPDATE pedidos SET id_cliente = ?, id_lote = ?, cantidad_kilos = ?, precio_total = ?, fecha_entrega = ? WHERE id_pedido = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id_cliente, $id_lote, $kilos, $precio_total, $fecha_entrega, $id_pedido]);

            $sqlInventario = "UPDATE inventario_lotes SET id_lote = ?, kilos_usados = ? WHERE id_pedido = ?";
            $stmtInventario = $conexion->prepare($sqlInventario);
            $stmtInventario->execute([$id_lote, $kilos, $id_pedido]);

            header('Location: index.php');
            exit();
        } else {
            $error = "No hay suficiente inventario. Disponible: $disponible kilos.";
        }
    } else {
        $error = "Datos inválidos. Revisa los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Editar Pedido</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_cliente" class="form-label">Cliente:</label>
                    <select class="form-select" id="id_cliente" name="id_cliente" required>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id_cliente'] ?>" <?= $cliente['id_cliente'] == $pedido['id_cliente'] ? 'selected' : '' ?>>
                                <?= $cliente['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_lote" class="form-label">Lote:</label>
                    <select class="form-select" id="id_lote" name="id_lote" required>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote['id_lote'] ?>" <?= $lote['id_lote'] == $pedido['id_lote'] ? 'selected' : '' ?>>
                                Lote #<?= $lote['id_lote'] ?> (<?= $lote['fecha'] ?>) - <?= $lote['kilos_disponibles'] ?> kg
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_entrega" class="form-label">Fecha de Entrega:</label>
                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" value="<?= $pedido['fecha_entrega'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="kilos" class="form-label">Kilos:</label>
                    <input type="number" class="form-control" id="kilos" name="cantidad_kilos" value="<?= $pedido['cantidad_kilos'] ?>" step="0.01" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Actualizar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>