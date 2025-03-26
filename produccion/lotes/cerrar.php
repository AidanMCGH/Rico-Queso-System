<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_lote = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_lote) {
    header('Location: index.php');
    exit();
}

$sql_lote = "SELECT * FROM lotes WHERE id_lote = ?";
$stmt_lote = $conexion->prepare($sql_lote);
$stmt_lote->execute([$id_lote]);
$lote = $stmt_lote->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kilos_producidos = filter_input(INPUT_POST, 'kilos_producidos', FILTER_VALIDATE_FLOAT);
    if ($kilos_producidos && $kilos_producidos > 0) {
        $costo_por_kilo = $lote['total_gasto'] / $kilos_producidos;
        $sql = "UPDATE lotes SET kilos_producidos = ?, costo_por_kilo = ? WHERE id_lote = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$kilos_producidos, $costo_por_kilo, $id_lote]);
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
        <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Cerrar Lote #<?= $lote['id_lote'] ?></h1>
        <div class="form-custom">
            <p><strong>Total Gastado:</strong> $<?= number_format($lote['total_gasto'], 2) ?></p>
            <form method="POST">
                <div class="mb-3">
                    <label for="kilos_producidos" class="form-label">Kilos de Queso Producidos:</label>
                    <input type="number" step="0.01" class="form-control" id="kilos_producidos" name="kilos_producidos" required>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Cerrar Lote</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>