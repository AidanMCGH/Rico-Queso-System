<?php
require_once '../../includes/conexion.php';

$id_trabajador = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id_trabajador) {
    $sql = "DELETE FROM trabajadores WHERE id_trabajador = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id_trabajador]);
}

header('Location: index.php');
exit();
?>