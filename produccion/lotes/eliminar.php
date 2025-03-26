<?php
require_once '../../includes/conexion.php';

$id_lote = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_lote) {
    $sql_insumos = "DELETE FROM insumos_lotes WHERE id_lote = ?";
    $stmt_insumos = $conexion->prepare($sql_insumos);
    $stmt_insumos->execute([$id_lote]);

    $sql_lote = "DELETE FROM lotes WHERE id_lote = ?";
    $stmt_lote = $conexion->prepare($sql_lote);
    $stmt_lote->execute([$id_lote]);
}

header('Location: index.php');
exit();
?>