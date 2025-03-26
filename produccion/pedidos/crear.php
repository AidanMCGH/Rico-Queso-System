<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sqlClientes = "SELECT id_cliente, nombre FROM clientes";
$stmtClientes = $conexion->query($sqlClientes);
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

$sqlLotes = "
    SELECT l.id_lote, l.fecha, l.kilos_producidos, 
           (l.kilos_producidos - COALESCE(SUM(il.kilos_usados), 0)) AS kilos_disponibles
    FROM lotes l
    LEFT JOIN inventario_lotes il ON l.id_lote = il.id_lote
    WHERE l.kilos_producidos IS NOT NULL
    GROUP BY l.id_lote, l.fecha, l.kilos_producidos
    HAVING kilos_disponibles > 0";
$stmtLotes = $conexion->query($sqlLotes);
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
            SELECT (kilos_producidos - COALESCE(SUM(il.kilos_usados), 0)) AS disponible
            FROM lotes l
            LEFT JOIN inventario_lotes il ON l.id_lote = il.id_lote
            WHERE l.id_lote = ?
            GROUP BY l.id_lote, l.kilos_producidos";
        $stmtInventario = $conexion->prepare($sqlInventario);
        $stmtInventario->execute([$id_lote]);
        $disponible = $stmtInventario->fetchColumn();

        if ($disponible >= $kilos) {
            $sql = "INSERT INTO pedidos (id_cliente, id_lote, cantidad_kilos, precio_total, fecha_pedido, fecha_entrega, estado)
                    VALUES (?, ?, ?, ?, CURDATE(), ?, 'pendiente')";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$id_cliente, $id_lote, $kilos, $precio_total, $fecha_entrega]);
            $id_pedido = $conexion->lastInsertId();

            $sqlInventario = "INSERT INTO inventario_lotes (id_lote, id_pedido, kilos_usados) VALUES (?, ?, ?)";
            $stmtInventario = $conexion->prepare($sqlInventario);
            $stmtInventario->execute([$id_lote, $id_pedido, $kilos]);

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
    <title>Agregar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Agregar Pedido</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_cliente" class="form-label">Cliente:</label>
                    <select class="form-select" id="id_cliente" name="id_cliente" required>
                        <option value="">Seleccione un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_lote" class="form-label">Lote:</label>
                    <select class="form-select" id="id_lote" name="id_lote" required>
                        <option value="">Seleccione un lote</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote['id_lote'] ?>">Lote #<?= $lote['id_lote'] ?> (<?= $lote['fecha'] ?>) - <?= $lote['kilos_disponibles'] ?> kg</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fecha_entrega" class="form-label">Fecha de Entrega:</label>
                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
                </div>
                <div class="mb-3">
                    <label for="kilos" class="form-label">Kilos:</label>
                    <input type="number" class="form-control" id="kilos" name="cantidad_kilos" step="0.01" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Guardar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>