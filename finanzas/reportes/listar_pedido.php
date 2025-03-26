<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sqlPedidos = "
    SELECT p.id_pedido, p.fecha_entrega, p.cantidad_kilos, p.precio_total, 
           l.id_lote, l.fecha AS fecha_lote, l.costo_por_kilo, l.kilos_producidos,
           (SELECT SUM(monto) FROM gastos WHERE id_pedido = p.id_pedido) AS total_gastos
    FROM pedidos p
    LEFT JOIN lotes l ON p.id_lote = l.id_lote";
$stmtPedidos = $conexion->query($sqlPedidos);
$pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Pedido</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Lista de Pedidos</h1>
        <div class="table-responsive">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Lote</th>
                        <th>Fecha Entrega</th>
                        <th>Kilos</th>
                        <th>Ingresos</th>
                        <th>Costos Totales</th>
                        <th>Ganancia</th>
                        <th>Margen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                        $total_gastos = $pedido['total_gastos'] ?: 0;
                        $costo_produccion = $pedido['costo_por_kilo'] ? $pedido['cantidad_kilos'] * $pedido['costo_por_kilo'] : 0;
                        $costos_totales = $costo_produccion + $total_gastos;
                        $ganancia = $pedido['precio_total'] - $costos_totales;
                        $margen_ganancia = $pedido['precio_total'] > 0 ? ($ganancia / $pedido['precio_total']) * 100 : 0;
                        ?>
                        <tr>
                            <td><?= $pedido['id_pedido'] ?></td>
                            <td><?= $pedido['id_lote'] ? "Lote #{$pedido['id_lote']} ({$pedido['fecha_lote']})" : 'Sin lote' ?></td>
                            <td><?= $pedido['fecha_entrega'] ?></td>
                            <td><?= number_format($pedido['cantidad_kilos'], 2) ?></td>
                            <td>$<?= number_format($pedido['precio_total'], 2) ?></td>
                            <td>$<?= number_format($costos_totales, 2) ?></td>
                            <td><span class="<?= $ganancia >= 0 ? 'text-success' : 'text-danger' ?>">$<?= number_format($ganancia, 2) ?></span></td>
                            <td><?= number_format($margen_ganancia, 2) ?>%</td>
                            <td>
                                <a href="pedido.php?id=<?= $pedido['id_pedido'] ?>" class="btn btn-primary btn-sm">Ver Reporte</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>