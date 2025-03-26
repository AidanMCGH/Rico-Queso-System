<?php
require_once '../../includes/conexion.php';

$id_proveedor = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_proveedor) {
    $sql = "DELETE FROM proveedores WHERE id_proveedor = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_proveedor]);
}

header('Location: index.php');
exit();
?>