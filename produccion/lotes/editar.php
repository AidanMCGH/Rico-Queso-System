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

$sql_insumos = "SELECT * FROM insumos_lotes WHERE id_lote = ?";
$stmt_insumos = $conexion->prepare($sql_insumos);
$stmt_insumos->execute([$id_lote]);
$insumos = $stmt_insumos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_STRING);
    $kilos_producidos = filter_input(INPUT_POST, 'kilos_producidos', FILTER_VALIDATE_FLOAT);
    $insumos_post = $_POST['insumos'];

    try {
        $conexion->beginTransaction();

        // Actualizar fecha
        $sql_update_lote = "UPDATE lotes SET fecha = ? WHERE id_lote = ?";
        $stmt_update_lote = $conexion->prepare($sql_update_lote);
        $stmt_update_lote->execute([$fecha, $id_lote]);

        // Eliminar insumos existentes
        $sql_delete_insumos = "DELETE FROM insumos_lotes WHERE id_lote = ?";
        $stmt_delete_insumos = $conexion->prepare($sql_delete_insumos);
        $stmt_delete_insumos->execute([$id_lote]);

        // Insertar nuevos insumos y calcular total_gasto
        $total_gasto = 0;
        $sql_insert_insumo = "INSERT INTO insumos_lotes (id_lote, producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_insumo = $conexion->prepare($sql_insert_insumo);

        foreach ($insumos_post as $insumo) {
            $cantidad = filter_var($insumo['cantidad'], FILTER_VALIDATE_FLOAT);
            $precio_unitario = filter_var($insumo['precio_unitario'], FILTER_VALIDATE_FLOAT);
            $total_insumo = $cantidad * $precio_unitario;
            $total_gasto += $total_insumo;
            $stmt_insert_insumo->execute([$id_lote, $insumo['producto'], $cantidad, $precio_unitario, $total_insumo]);
        }

        // Actualizar total_gasto y, si hay kilos_producidos, recalcular costo_por_kilo
        if ($kilos_producidos && $kilos_producidos > 0) {
            $costo_por_kilo = $total_gasto / $kilos_producidos;
            $sql_update_total = "UPDATE lotes SET total_gasto = ?, kilos_producidos = ?, costo_por_kilo = ? WHERE id_lote = ?";
            $stmt_update_total = $conexion->prepare($sql_update_total);
            $stmt_update_total->execute([$total_gasto, $kilos_producidos, $costo_por_kilo, $id_lote]);
        } else {
            $sql_update_total = "UPDATE lotes SET total_gasto = ? WHERE id_lote = ?";
            $stmt_update_total = $conexion->prepare($sql_update_total);
            $stmt_update_total->execute([$total_gasto, $id_lote]);
        }

        $conexion->commit();
        header('Location: detalle.php?id=' . $id_lote);
        exit();
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "Error al actualizar el lote: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
    <h1 class="text-center mb-4">Editar Lote #<?= $lote['id_lote'] ?></h1>
    <div class="form-custom">
        <form method="POST">
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= $lote['fecha'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="kilos_producidos" class="form-label">Kilos Producidos (opcional):</label>
                <input type="number" step="0.01" class="form-control" id="kilos_producidos" name="kilos_producidos" value="<?= $lote['kilos_producidos'] ?: '' ?>" placeholder="Dejar en blanco si no cambia">
            </div>
            <div id="insumos">
                <?php foreach ($insumos as $index => $insumo): ?>
                    <div class="row mb-3 insumo-row">
                        <div class="col">
                            <label class="form-label">Producto:</label>
                            <input type="text" class="form-control" name="insumos[<?= $index ?>][producto]" value="<?= $insumo['producto'] ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Cantidad:</label>
                            <input type="number" step="0.01" class="form-control" name="insumos[<?= $index ?>][cantidad]" value="<?= $insumo['cantidad'] ?>" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Precio Unitario:</label>
                            <input type="number" step="0.01" class="form-control" name="insumos[<?= $index ?>][precio_unitario]" value="<?= $insumo['precio_unitario'] ?>" required>
                        </div>
                        <div class="col-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger" onclick="eliminarInsumo(this)">X</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-secondary" onclick="agregarInsumo()">Agregar otro insumo</button>
            <button type="submit" class="btn btn-primary btn-custom">Guardar Cambios</button>
        </form>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let insumoCount = <?= count($insumos) ?>;
        function agregarInsumo() {
            const div = document.createElement('div');
            div.classList.add('row', 'mb-3', 'insumo-row');
            div.innerHTML = `
                <div class="col">
                    <label class="form-label">Producto:</label>
                    <input type="text" class="form-control" name="insumos[${insumoCount}][producto]" required>
                </div>
                <div class="col">
                    <label class="form-label">Cantidad:</label>
                    <input type="number" step="0.01" class="form-control" name="insumos[${insumoCount}][cantidad]" required>
                </div>
                <div class="col">
                    <label class="form-label">Precio Unitario:</label>
                    <input type="number" step="0.01" class="form-control" name="insumos[${insumoCount}][precio_unitario]" required>
                </div>
                <div class="col-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger" onclick="eliminarInsumo(this)">X</button>
                </div>
            `;
            document.getElementById('insumos').appendChild(div);
            insumoCount++;
        }

        function eliminarInsumo(boton) {
            boton.closest('.insumo-row').remove();
        }
    </script>
</body>
</html>