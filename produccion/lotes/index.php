<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sql = "SELECT * FROM lotes ORDER BY fecha DESC";
$stmt = $conexion->query($sql);
$lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Lotes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Lotes Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Lote</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total Gasto</th>
                        <th>Kilos Producidos</th>
                        <th>Costo por Kilo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lotes as $lote): ?>
                        <tr>
                            <td><?= $lote['id_lote'] ?></td>
                            <td><?= $lote['fecha'] ?></td>
                            <td>$<?= number_format($lote['total_gasto'], 2) ?></td>
                            <td><?= $lote['kilos_producidos'] ? number_format($lote['kilos_producidos'], 2) : 'Pendiente' ?></td>
                            <td><?= $lote['costo_por_kilo'] ? '$' . number_format($lote['costo_por_kilo'], 2) : 'Pendiente' ?></td>
                            <td>
                                <a href="detalle.php?id=<?= $lote['id_lote'] ?>" class="btn btn-info btn-custom">Ver Detalle</a>
                                <?php if (!$lote['kilos_producidos']): ?>
                                    <a href="cerrar.php?id=<?= $lote['id_lote'] ?>" class="btn btn-success btn-custom">Cerrar Lote</a>
                                <?php endif; ?>
                                <a href="eliminar.php?id=<?= $lote['id_lote'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
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