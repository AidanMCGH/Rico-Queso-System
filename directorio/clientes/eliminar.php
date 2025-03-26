<?php 
require_once '../../includes/conexion.php';

$id_cliente = $_GET['id']; // Asegúrate de asignar el valor correctamente
$sql = "DELETE FROM clientes WHERE id_cliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id_cliente]); // Usa la variable correcta aquí

header('Location: index.php');
exit();

?>
