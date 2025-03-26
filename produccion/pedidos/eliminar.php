<?php
require_once '../../includes/conexion.php';

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_pedido) {
    $sqlInventario = "DELETE FROM inventario_lotes WHERE id_pedido = ?";
    $stmtInventario = $conexion->prepare($sqlInventario);
    $stmtInventario->execute([$id_pedido]);

    $sql = "DELETE FROM pedidos WHERE id_pedido = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_pedido]);
}

header('Location: index.php');
exit();
?>
