<?php
require_once '../../includes/conexion.php';

header('Content-Type: application/json');

$id_lote = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_lote) {
    echo json_encode(['error' => 'ID de lote inválido']);
    exit();
}

$sqlLote = "SELECT total_gasto FROM lotes WHERE id_lote = ?";
$stmtLote = $conexion->prepare($sqlLote);
$stmtLote->execute([$id_lote]);
$lote = $stmtLote->fetch(PDO::FETCH_ASSOC);

if (!$lote) {
    echo json_encode(['error' => 'Lote no encontrado']);
    exit();
}

$sqlPedidos = "SELECT SUM(precio_total) as ingresos_totales FROM pedidos WHERE id_lote = ?";
$stmtPedidos = $conexion->prepare($sqlPedidos);
$stmtPedidos->execute([$id_lote]);
$pedidos = $stmtPedidos->fetch(PDO::FETCH_ASSOC);
$ingresos_totales = $pedidos['ingresos_totales'] ?? 0;

$sqlPagos = "SELECT SUM(monto) as total_pagado FROM pagos WHERE id_lote = ?";
$stmtPagos = $conexion->prepare($sqlPagos);
$stmtPagos->execute([$id_lote]);
$pagos = $stmtPagos->fetch(PDO::FETCH_ASSOC);
$total_pagado = $pagos['total_pagado'] ?? 0;

$ganancia_pura = $ingresos_totales - $lote['total_gasto'];
$ganancia_restante = $ganancia_pura - $total_pagado;

echo json_encode(['ganancia_restante' => $ganancia_restante]);
?>