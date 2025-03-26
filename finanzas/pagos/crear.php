<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$sqlTrabajadores = "SELECT id_trabajador, nombre FROM trabajadores";
$stmtTrabajadores = $conexion->query($sqlTrabajadores);
$trabajadores = $stmtTrabajadores->fetchAll(PDO::FETCH_ASSOC);

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
        $sqlLote = "SELECT total_gasto FROM lotes WHERE id_lote = ?";
        $stmtLote = $conexion->prepare($sqlLote);
        $stmtLote->execute([$id_lote]);
        $lote = $stmtLote->fetch(PDO::FETCH_ASSOC);

        $sqlPedidos = "SELECT SUM(precio_total) as ingresos_totales FROM pedidos WHERE id_lote = ?";
        $stmtPedidos = $conexion->prepare($sqlPedidos);
        $stmtPedidos->execute([$id_lote]);
        $pedidos = $stmtPedidos->fetch(PDO::FETCH_ASSOC);
        $ingresos_totales = $pedidos['ingresos_totales'] ?? 0;

        $sqlPagos = "SELECT SUM(monto) as total_pagado FROM pagos WHERE id_lote = ?";
        $stmtPagos = $conexion->prepare($sqlPagos);
        $stmtPagos->execute([$id_lote]);
        $pagos = $stmtPagos->fetch(PDO::FETCH_ASSOC);
        $total_pagado = $pagos['total_pagado'] ?? 0;

        $ganancia_pura = $ingresos_totales - $lote['total_gasto'];
        $ganancia_restante = $ganancia_pura - $total_pagado;

        if ($ganancia_restante <= 0) {
            $error = "No queda ganancia suficiente en el lote para realizar más pagos.";
        } else {
            $monto = $ganancia_pura * ($porcentaje / 100); // Sigue usando ganancia pura para el cálculo
            if ($monto > $ganancia_restante) {
                $error = "El monto del pago ($" . number_format($monto, 2) . ") excede la ganancia restante ($" . number_format($ganancia_restante, 2) . ").";
            } else {
                $sql = "INSERT INTO pagos (id_lote, id_trabajador, monto, fecha_pago, descripcion, porcentaje_ganancia) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$id_lote, $id_trabajador, $monto, $fecha_pago, $descripcion, $porcentaje]);
                header('Location: index.php');
                exit();
            }
        }
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
    <title>Agregar Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">

</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php';?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Agregar Pago</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="id_trabajador" class="form-label">Trabajador:</label>
                    <select class="form-select" id="id_trabajador" name="id_trabajador" required>
                        <option value="">Seleccione un trabajador</option>
                        <?php foreach ($trabajadores as $trabajador): ?>
                            <option value="<?= $trabajador['id_trabajador'] ?>"><?= $trabajador['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_lote" class="form-label">Lote para calcular ganancia:</label>
                    <select class="form-select" id="id_lote" name="id_lote" required>
                        <option value="">Seleccione un lote</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?= $lote['id_lote'] ?>">Lote #<?= $lote['id_lote'] ?> (<?= $lote['fecha'] ?>, <?= $lote['kilos_producidos'] ?> kg)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganancia Restante del Lote:</label>
                    <p id="ganancia_lote" class="form-text">Seleccione un lote para ver la ganancia restante.</p>
                </div>
                <div class="mb-3">
                    <label for="porcentaje" class="form-label">Porcentaje de ganancia (%):</label>
                    <input type="number" class="form-control" id="porcentaje" name="porcentaje" 
                           step="0.01" min="0" max="100" required>
                </div>
                <div class="mb-3">
                    <label for="fecha_pago" class="form-label">Fecha de Pago:</label>
                    <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Guardar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('id_lote').addEventListener('change', function() {
            const idLote = this.value;
            const gananciaDisplay = document.getElementById('ganancia_lote');

            if (idLote === '') {
                gananciaDisplay.textContent = 'Seleccione un lote para ver la ganancia restante.';
                gananciaDisplay.className = 'form-text';
                return;
            }

            fetch('get_ganancia_lote.php?id=' + idLote)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        gananciaDisplay.textContent = data.error;
                        gananciaDisplay.className = 'form-text text-danger';
                    } else {
                        const ganancia = parseFloat(data.ganancia_restante);
                        if (ganancia >= 0) {
                            gananciaDisplay.textContent = '$' + ganancia.toFixed(2);
                            gananciaDisplay.className = 'form-text text-success';
                        } else {
                            gananciaDisplay.textContent = '-$' + Math.abs(ganancia).toFixed(2);
                            gananciaDisplay.className = 'form-text text-danger';
                        }
                    }
                })
                .catch(error => {
                    gananciaDisplay.textContent = 'Error al calcular la ganancia.';
                    gananciaDisplay.className = 'form-text text-danger';
                    console.error('Error:', error);
                });
        });
    </script>
</body>
</html>