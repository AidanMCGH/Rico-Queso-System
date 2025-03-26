<?php
require_once '../../includes/conexion.php';

$id_pago = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_pago) {
    $sql = "DELETE FROM pagos WHERE id_pago = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_pago]);
}

header('Location: index.php');
exit();
?>