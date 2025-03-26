<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

// Obtener todos los pagos con información de trabajadores y lotes
$sql = "
    SELECT p.id_pago, t.nombre AS trabajador, p.monto, p.fecha_pago, p.descripcion, 
           p.id_lote, p.porcentaje_ganancia, l.fecha AS fecha_lote, l.kilos_producidos
    FROM pagos p
    JOIN trabajadores t ON p.id_trabajador = t.id_trabajador
    LEFT JOIN lotes l ON p.id_lote = l.id_lote";
$stmt = $conexion->query($sql);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Pagos Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Pago</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Monto</th>
                        <th>Porcentaje</th>
                        <th>Lote</th>
                        <th>Kilos</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?= $pago['id_pago'] ?></td>
                            <td><?= $pago['trabajador'] ?></td>
                            <td>$<?= number_format($pago['monto'], 2) ?></td>
                            <td><?= $pago['porcentaje_ganancia'] ?>%</td>
                            <td><?= $pago['id_lote'] ? "Lote #{$pago['id_lote']} ({$pago['fecha_lote']})" : 'N/A' ?></td>
                            <td><?= $pago['kilos_producidos'] ?? 'N/A' ?></td>
                            <td><?= $pago['fecha_pago'] ?></td>
                            <td><?= $pago['descripcion'] ?: 'Sin descripción' ?></td>
                            <td>
                                <a href="editar.php?id=<?= $pago['id_pago'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                <a href="eliminar.php?id=<?= $pago['id_pago'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
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