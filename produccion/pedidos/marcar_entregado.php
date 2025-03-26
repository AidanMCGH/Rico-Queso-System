<?php
require_once '../../includes/conexion.php';

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_pedido) {
    $sql = "UPDATE pedidos SET estado = 'entregado' WHERE id_pedido = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_pedido]);
}

header('Location: index.php');
exit();
?>
