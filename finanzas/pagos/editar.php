<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_pago = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_pago) {
    header('Location: index.php');
    exit();
}

// Obtener datos del pago actual
$sqlPago = "SELECT * FROM pagos WHERE id_pago = ?";
$stmtPago = $conexion->prepare($sqlPago);
$stmtPago->execute([$id_pago]);
$pago = $stmtPago->fetch(PDO::FETCH_ASSOC);

// Obtener lista de trabajadores
$sqlTrabajadores = "SELECT id_trabajador, nombre FROM trabajadores";
$stmtTrabajadores = $conexion->query($sqlTrabajadores);
$trabajadores = $stmtTrabajadores->fetchAll(PDO::FETCH_ASSOC);

// Obtener lotes con kilos_producidos definidos
$sqlLotes = "SELECT id_lote, fecha, kilos_producidos FROM lotes WHERE kilos_producidos IS NOT NULL";
$stmtLotes = $conexion->query($sqlLotes);
$lotes = $stmtLotes->fetchAll(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_trabajador = filter_input(INPUT_POST, 'id_trabajador', FILTER_VALIDATE_INT);
    $id_lote = filter_input(INPUT_POST, 'id_lote', FILTER_VALIDATE_INT);
    $porcentaje = filter_input(INPUT_POST, 'porcentaje', FILTER_VALIDATE_FLOAT);
    $fecha_pago = filter_input(INPUT_POST, 'fecha_pago', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);

    if ($id_trabajador && $id_lote && $porcentaje >= 0 && $porcentaje <= 100 && $fecha_pago) {
        // Obtener kilos_producidos del lote seleccionado
        $sqlLote = "SELECT kilos_producidos FROM lotes WHERE id_lote = ?";
        $stmtLote = $conexion->prepare($sqlLote);
        $stmtLote->execute([$id_lote]);
        $lote = $stmtLote->fetch(PDO::FETCH_ASSOC);

        // Calcular monto: (kilos_producidos * 5$) * (porcentaje / 100)
        $ganancia_promedio = 5; // Precio fijo por kilo
        $ganancia_total = $lote['kilos_producidos'] * $ganancia_promedio;
        $monto = $ganancia_total * ($porcentaje / 100);

        // Actualizar el pago en la base de datos
        $sql = "UPDATE pagos SET id_lote = ?, id_trabajador = ?, monto = ?, fecha_pago = ?, 
                descripcion = ?, porcentaje_ganancia = ? WHERE id_pago = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_lote, $id_trabajador, $monto, $fecha_pago, $descripcion, $porcentaje, $id_pago]);
        header('Location: index.php');
        exit();
    } else {
        $error = "Datos inválidos. Revisa los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Editar Pago</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_trabajador" class="form-label">Trabajador:</label>
                    <select class="form-select" id="id_trabajador" name="id_trabajador" required>
                        <?php foreach ($trabajadores as $trabajador): ?>
                            <option value="<?= $trabajador['id_trabajador'] ?>" 
                                    <?= $trabajador['id_trabajador'] == $pago['id_trabajador'] ? 'selected' : '' ?>>
                                <?= $trabajador['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_lote" class="form-label">Lote para calcular ganancia:</label>
                    <select class="form-select" id="id_lote" name="id_lote" required>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote['id_lote'] ?>" 
                                    <?= $lote['id_lote'] == $pago['id_lote'] ? 'selected' : '' ?>>
                                Lote #<?= $lote['id_lote'] ?> (<?= $lote['fecha'] ?>, <?= $lote['kilos_producidos'] ?> kg)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="porcentaje" class="form-label">Porcentaje de ganancia (%):</label>
                    <input type="number" class="form-control" id="porcentaje" name="porcentaje" 
                           value="<?= $pago['porcentaje_ganancia'] ?>" step="0.01" min="0" max="100" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_pago" class="form-label">Fecha de Pago:</label>
                    <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                           value="<?= $pago['fecha_pago'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= $pago['descripcion'] ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Actualizar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>