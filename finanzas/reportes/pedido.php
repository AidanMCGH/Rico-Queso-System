<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_pedido) {
    header('Location: listar_pedido.php');
    exit();
}

// Consulta del pedido con información del lote
$sqlPedido = "
    SELECT p.*, l.id_lote, l.fecha AS fecha_lote, l.costo_por_kilo, l.kilos_producidos, l.total_gasto AS gasto_lote
    FROM pedidos p
    LEFT JOIN lotes l ON p.id_lote = l.id_lote
    WHERE p.id_pedido = ?";
$stmtPedido = $conexion->prepare($sqlPedido);
$stmtPedido->execute([$id_pedido]);
$pedido = $stmtPedido->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: listar_pedido.php');
    exit();
}

// Nombre del cliente
$sqlCliente = "SELECT nombre FROM clientes WHERE id_cliente = ?";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->execute([$pedido['id_cliente']]);
$cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

// Gastos administrativos
$sqlGastos = "SELECT SUM(monto) AS total_gastos FROM gastos WHERE id_pedido = ?";
$stmtGastos = $conexion->prepare($sqlGastos);
$stmtGastos->execute([$id_pedido]);
$total_gastos = $stmtGastos->fetch(PDO::FETCH_ASSOC)['total_gastos'] ?: 0;

// Cálculos financieros
$costo_produccion = $pedido['costo_por_kilo'] ? ($pedido['cantidad_kilos'] * $pedido['costo_por_kilo']) : 0;
$ingresos_totales = $pedido['precio_total'];
$costos_totales = $costo_produccion + $total_gastos;
$ganancia = $ingresos_totales - $costos_totales;
$margen_ganancia = $ingresos_totales > 0 ? ($ganancia / $ingresos_totales) * 100 : 0;
$costo_por_kilo_vendido = $pedido['cantidad_kilos'] > 0 ? $costos_totales / $pedido['cantidad_kilos'] : 0;
$porcentaje_lote_usado = ($pedido['kilos_producidos'] && $pedido['cantidad_kilos']) ? ($pedido['cantidad_kilos'] / $pedido['kilos_producidos']) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte del Pedido #<?= $pedido['id_pedido'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Reporte del Pedido #<?= $pedido['id_pedido'] ?></h1>
        <div class="card-custom">
            <h2>Detalles Generales</h2>
            <p><strong>Cliente:</strong> <?= $cliente['nombre'] ?></p>
            <p><strong>Lote Asociado:</strong> <?= $pedido['id_lote'] ? "Lote #{$pedido['id_lote']} ({$pedido['fecha_lote']})" : 'Sin lote' ?></p>
            <p><strong>Fecha de Entrega:</strong> <?= $pedido['fecha_entrega'] ?></p>
            <p><strong>Kilos Solicitados:</strong> <?= number_format($pedido['cantidad_kilos'], 2) ?></p>

            <h2 class="mt-4">Detalles de Producción</h2>
            <p><strong>Kilos Producidos (Lote):</strong> <?= $pedido['kilos_producidos'] ? number_format($pedido['kilos_producidos'], 2) : 'No disponible' ?></p>
            <p><strong>% del Lote Usado:</strong> <?= $porcentaje_lote_usado ? number_format($porcentaje_lote_usado, 2) . '%' : 'No disponible' ?></p>
            <p><strong>Costo por Kilo (Producción):</strong> <?= $pedido['costo_por_kilo'] ? '$' . number_format($pedido['costo_por_kilo'], 2) : 'No disponible' ?></p>
            <p><strong>Costo Total del Lote:</strong> <?= $pedido['gasto_lote'] ? '$' . number_format($pedido['gasto_lote'], 2) : 'No disponible' ?></p>

            <h2 class="mt-4">Detalles Financieros</h2>
            <p><strong>Ingresos Totales:</strong> $<?= number_format($ingresos_totales, 2) ?></p>
            <p><strong>Costo de Producción:</strong> $<?= number_format($costo_produccion, 2) ?></p>
            <p><strong>Gastos Administrativos:</strong> $<?= number_format($total_gastos, 2) ?></p>
            <p><strong>Costos Totales:</strong> $<?= number_format($costos_totales, 2) ?></p>
            <p><strong>Costo por Kilo Vendido:</strong> $<?= number_format($costo_por_kilo_vendido, 2) ?></p>
            <p><strong>Ganancia Neta:</strong> <span class="<?= $ganancia >= 0 ? 'text-success' : 'text-danger' ?>">$<?= number_format($ganancia, 2) ?></span></p>
            <p><strong>Margen de Ganancia:</strong> <?= number_format($margen_ganancia, 2) ?>%</p>
        </div>
        <a href="listar_pedido.php" class="btn btn-primary btn-custom mt-3">Volver a la lista de pedidos</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>