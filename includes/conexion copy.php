<?php
// conexión.php
$host = 'sql105.infinityfree.com';
$dbname = 'if0_38491741_gestion_ventas_gastos';
$user = 'if0_38491741';
$password = '30144019Sp';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>
