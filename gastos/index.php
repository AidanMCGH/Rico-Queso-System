<?php
require_once '../includes/conexion.php';

$sql = "
    SELECT g.id_gasto, p.id_pedido, g.descripcion, g.monto, g.fecha_gasto 
    FROM gastos g
    JOIN pedidos p ON g.id_pedido = p.id_pedido";
$stmt = $conexion->query($sql);
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gastos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Gastos Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Gasto</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID Gasto</th>
                        <th>ID Pedido</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td><?= $gasto['id_gasto'] ?></td>
                            <td><?= $gasto['id_pedido'] ?></td>
                            <td><?= $gasto['descripcion'] ?></td>
                            <td>$<?= number_format($gasto['monto'], 2) ?></td>
                            <td><?= $gasto['fecha_gasto'] ?></td>
                            <td>
                                <a href="editar.php?id=<?= $gasto['id_gasto'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                <a href="eliminar.php?id=<?= $gasto['id_gasto'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>