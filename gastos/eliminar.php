<?php
require_once '../includes/conexion.php';

$id_gasto = $_GET['id'];
$sql = "DELETE FROM gastos WHERE id_gasto = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_gasto]);

header('Location: index.php');
exit();
?>
<?php
require_once '../includes/conexion.php';

$id_gasto = $_GET['id'];
$sql = "DELETE FROM gastos WHERE id_gasto = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_gasto]);

header('Location: index.php');
exit();
?>
