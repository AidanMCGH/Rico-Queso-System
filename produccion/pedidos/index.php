<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sql = "
    SELECT p.id_pedido, c.nombre AS cliente, p.estado, p.fecha_entrega, p.cantidad_kilos, p.precio_total, l.id_lote, l.fecha AS fecha_lote
    FROM pedidos p
    JOIN clientes c ON p.id_cliente = c.id_cliente
    LEFT JOIN lotes l ON p.id_lote = l.id_lote";
$stmt = $conexion->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Pedidos Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Pedido</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Lote</th>
                        <th>Fecha de Entrega</th>
                        <th>Kilos</th>
                        <th>Precio Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= $pedido['id_pedido'] ?></td>
                            <td><?= $pedido['cliente'] ?></td>
                            <td><?= $pedido['id_lote'] ? "Lote #{$pedido['id_lote']} ({$pedido['fecha_lote']})" : 'Sin lote' ?></td>
                            <td><?= $pedido['fecha_entrega'] ?></td>
                            <td><?= $pedido['cantidad_kilos'] ?></td>
                            <td>$<?= number_format($pedido['precio_total'], 2) ?></td>
                            <td><?= $pedido['estado'] ?></td>
                            <td>
                                <?php if ($pedido['estado'] === 'pendiente'): ?>
                                    <a href="marcar_entregado.php?id=<?= $pedido['id_pedido'] ?>" class="btn btn-success btn-custom">Marcar como Entregado</a>
                                <?php else: ?>
                                    <span class="badge bg-success">Entregado</span>
                                <?php endif; ?>
                                <a href="editar.php?id=<?= $pedido['id_pedido'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                <a href="eliminar.php?id=<?= $pedido['id_pedido'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
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