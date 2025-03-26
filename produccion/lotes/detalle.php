<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_lote = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_lote) {
    header('Location: index.php');
    exit();
}

// Obtener información del lote
$sql_lote = "SELECT * FROM lotes WHERE id_lote = ?";
$stmt_lote = $conexion->prepare($sql_lote);
$stmt_lote->execute([$id_lote]);
$lote = $stmt_lote->fetch(PDO::FETCH_ASSOC);

// Obtener insumos del lote
$sql_insumos = "SELECT * FROM insumos_lotes WHERE id_lote = ?";
$stmt_insumos = $conexion->prepare($sql_insumos);
$stmt_insumos->execute([$id_lote]);
$insumos = $stmt_insumos->fetchAll(PDO::FETCH_ASSOC);

// Obtener ingresos de los pedidos asociados al lote
$sql_pedidos = "SELECT SUM(precio_total) as ingresos_totales FROM pedidos WHERE id_lote = ?";
$stmt_pedidos = $conexion->prepare($sql_pedidos);
$stmt_pedidos->execute([$id_lote]);
$pedidos = $stmt_pedidos->fetch(PDO::FETCH_ASSOC);
$ingresos_totales = $pedidos['ingresos_totales'] ?? 0;

// Calcular ganancia pura
$ganancia_pura = $ingresos_totales - $lote['total_gasto'];

// Obtener suma de pagos asociados al lote
$sql_pagos = "SELECT SUM(monto) as total_pagado FROM pagos WHERE id_lote = ?";
$stmt_pagos = $conexion->prepare($sql_pagos);
$stmt_pagos->execute([$id_lote]);
$pagos = $stmt_pagos->fetch(PDO::FETCH_ASSOC);
$total_pagado = $pagos['total_pagado'] ?? 0;

// Calcular ganancia restante
$ganancia_restante = $ganancia_pura - $total_pagado;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Detalle del Lote #<?= $lote['id_lote'] ?></h1>
        <p><strong>Fecha:</strong> <?= $lote['fecha'] ?></p>
        <p><strong>Total Gasto:</strong> $<?= number_format($lote['total_gasto'], 2) ?></p>
        <p><strong>Kilos Producidos:</strong> <?= $lote['kilos_producidos'] ? number_format($lote['kilos_producidos'], 2) : 'Pendiente' ?></p>
        <p><strong>Costo por Kilo:</strong> <?= $lote['costo_por_kilo'] ? '$' . number_format($lote['costo_por_kilo'], 2) : 'Pendiente' ?></p>
        <p><strong>Ingresos Totales:</strong> $<?= number_format($ingresos_totales, 2) ?></p>
        <p><strong>Ganancia Pura:</strong> 
            <?php if ($ganancia_pura >= 0): ?>
                <span class="text-success">$<?= number_format($ganancia_pura, 2) ?></span>
            <?php else: ?>
                <span class="text-danger">-$<?= number_format(abs($ganancia_pura), 2) ?></span>
            <?php endif; ?>
        </p>
        <p><strong>Total Pagado:</strong> $<?= number_format($total_pagado, 2) ?></p>
        <p><strong>Ganancia Restante:</strong> 
            <?php if ($ganancia_restante >= 0): ?>
                <span class="text-success">$<?= number_format($ganancia_restante, 2) ?></span>
            <?php else: ?>
                <span class="text-danger">-$<?= number_format(abs($ganancia_restante), 2) ?></span>
            <?php endif; ?>
        </p>

        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($insumos as $insumo): ?>
                        <tr>
                            <td><?= $insumo['producto'] ?></td>
                            <td><?= $insumo['cantidad'] ?></td>
                            <td>$<?= number_format($insumo['precio_unitario'], 2) ?></td>
                            <td>$<?= number_format($insumo['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn btn-primary btn-custom">Volver</a>
        <a href="editar.php?id=<?= $lote['id_lote'] ?>" class="btn btn-warning btn-custom">Editar Lote</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>