<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sqlIngresos = "SELECT SUM(precio_total) AS total_ingresos FROM pedidos";
$stmtIngresos = $conexion->query($sqlIngresos);
$total_ingresos = $stmtIngresos->fetch(PDO::FETCH_ASSOC)['total_ingresos'] ?: 0;

$sqlGastos = "SELECT SUM(monto) AS total_gastos FROM gastos";
$stmtGastos = $conexion->query($sqlGastos);
$total_gastos = $stmtGastos->fetch(PDO::FETCH_ASSOC)['total_gastos'] ?: 0;

$sqlCostos = "SELECT SUM(total_gasto) AS total_costos_produccion FROM lotes";
$stmtCostos = $conexion->query($sqlCostos);
$total_costos_produccion = $stmtCostos->fetch(PDO::FETCH_ASSOC)['total_costos_produccion'] ?: 0;

$sqlPagos = "SELECT SUM(monto) AS total_pagos_trabajadores FROM pagos";
$stmtPagos = $conexion->query($sqlPagos);
$total_pagos_trabajadores = $stmtPagos->fetch(PDO::FETCH_ASSOC)['total_pagos_trabajadores'] ?: 0;

$sqlKilos = "SELECT SUM(kilos_producidos) AS total_kilos, SUM(total_gasto) / SUM(kilos_producidos) AS costo_promedio_kilo 
             FROM lotes WHERE kilos_producidos IS NOT NULL";
$stmtKilos = $conexion->query($sqlKilos);
$resultado_kilos = $stmtKilos->fetch(PDO::FETCH_ASSOC);
$total_kilos_producidos = $resultado_kilos['total_kilos'] ?: 0;
$costo_promedio_kilo = $resultado_kilos['costo_promedio_kilo'] ?: 0;

$balance_general = $total_ingresos - ($total_gastos + $total_costos_produccion + $total_pagos_trabajadores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Reporte General</h1>
        <div class="card-custom">
            <h2>Resumen Financiero</h2>
            <p><strong>Total Ingresos:</strong> $<?= number_format($total_ingresos, 2) ?></p>
            <p><strong>Gastos Administrativos:</strong> $<?= number_format($total_gastos, 2) ?></p>
            <p><strong>Costos de Producción (Lotes):</strong> $<?= number_format($total_costos_produccion, 2) ?></p>
            <p><strong>Pagos a Trabajadores:</strong> $<?= number_format($total_pagos_trabajadores, 2) ?></p>
            <p><strong>Total Kilos Producidos:</strong> <?= number_format($total_kilos_producidos, 2) ?> kg</p>
            <p><strong>Costo Promedio por Kilo:</strong> $<?= number_format($costo_promedio_kilo, 2) ?></p>
            <hr>
            <h2>Balance General</h2>
            <p><strong>Balance Neto:</strong> $<?= number_format($balance_general, 2) ?></p>
        </div>
        <a href="../index.php" class="btn btn-primary btn-custom">Volver</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>