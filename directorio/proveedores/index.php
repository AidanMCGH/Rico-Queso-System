<?php
require_once '../../includes/conexion.php'; // Conexión PDO
require_once '../../config.php'; // Configuración (BASE_URL, BASE_PATH, etc.)

$sql = "SELECT * FROM proveedores";
$stmt = $conexion->query($sql);
$proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Proveedores Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Proveedor</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Contacto</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($proveedores) > 0): ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td><?= htmlspecialchars($proveedor['id_proveedor']) ?></td>
                                <td><?= htmlspecialchars($proveedor['nombre']) ?></td>
                                <td><?= htmlspecialchars($proveedor['direccion'] ?: 'Sin dirección') ?></td>
                                <td><?= htmlspecialchars($proveedor['contacto'] ?: 'Sin contacto') ?></td>
                                <td><?= htmlspecialchars($proveedor['descripcion_rubro'] ?: 'Sin descripción') ?></td>
                                <td>
                                    <a href="editar.php?id=<?= $proveedor['id_proveedor'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                    <a href="eliminar.php?id=<?= $proveedor['id_proveedor'] ?>" class="btn btn-danger btn-custom" onclick="return confirm('¿Estás seguro de eliminar este proveedor?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center"></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>