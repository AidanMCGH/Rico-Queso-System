<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';
include BASE_PATH . 'includes/navbar.php';

$sql = "SELECT * FROM clientes";
$stmt = $conexion->query($sql);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <div class="container container-main">
        <h1 class="text-center mb-4">Clientes Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Cliente</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Dirección</th>
                        <th>Notas</th>
                        <th>Cumpleaños</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= $cliente['id_cliente'] ?></td>
                            <td><?= $cliente['nombre'] ?></td>
                            <td><?= $cliente['contacto'] ?></td>
                            <td><?= $cliente['direccion'] ?></td>
                            <td><?= $cliente['notas'] ?></td>
                            <td><?= $cliente['fecha_Nacimiento'] ?></td>
                            <td>$<?= number_format($cliente['precio_por_kilo'], 2) ?></td>
                            <td>
                                <a href="editar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                <a href="eliminar.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
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