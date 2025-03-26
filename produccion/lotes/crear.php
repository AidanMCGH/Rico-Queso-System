<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_STRING);
    $insumos = $_POST['insumos'];
    $total_gasto = 0;

    $sql_lote = "INSERT INTO lotes (fecha, total_gasto) VALUES (?, 0)";
    $stmt_lote = $conexion->prepare($sql_lote);
    $stmt_lote->execute([$fecha]);
    $id_lote = $conexion->lastInsertId();

    $sql_insumo = "INSERT INTO insumos_lotes (id_lote, producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)";
    $stmt_insumo = $conexion->prepare($sql_insumo);
    foreach ($insumos as $insumo) {
        $cantidad = filter_var($insumo['cantidad'], FILTER_VALIDATE_FLOAT);
        $precio_unitario = filter_var($insumo['precio_unitario'], FILTER_VALIDATE_FLOAT);
        $total_insumo = $cantidad * $precio_unitario;
        $total_gasto += $total_insumo;
        $stmt_insumo->execute([$id_lote, $insumo['producto'], $cantidad, $precio_unitario, $total_insumo]);
    }

    $sql_update = "UPDATE lotes SET total_gasto = ? WHERE id_lote = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->execute([$total_gasto, $id_lote]);

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Lote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Agregar Lote</h1>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div id="insumos">
                    <div class="row mb-3 insumo-row">
                        <div class="col">
                            <label class="form-label">Producto:</label>
                            <input type="text" class="form-control" name="insumos[0][producto]" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Cantidad:</label>
                            <input type="number" step="0.01" class="form-control" name="insumos[0][cantidad]" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Precio Unitario:</label>
                            <input type="number" step="0.01" class="form-control" name="insumos[0][precio_unitario]" required>
                        </div>
                        <div class="col-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger" onclick="eliminarInsumo(this)">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="agregarInsumo()">Agregar otro insumo</button>
                <button type="submit" class="btn btn-primary btn-custom">Guardar Lote</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let insumoCount = 1;
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
            const insumoRows = document.querySelectorAll('.insumo-row');
            if (insumoRows.length > 1) {
                boton.closest('.insumo-row').remove();
            } else {
                alert('Debe haber al menos un insumo');
            }
        }
    </script>
</body>
</html>