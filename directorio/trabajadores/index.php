<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';


$sql = "SELECT * FROM trabajadores";
$stmt = $conexion->query($sql);
$trabajadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Trabajadores</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
<?php include BASE_PATH . 'includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Trabajadores Registrados</h1>
        <a href="crear.php" class="btn btn-primary btn-custom">Agregar Trabajador</a>
        <div class="table-responsive mt-4">
            <table class="table table-striped table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trabajadores as $trabajador): ?>
                        <tr>
                            <td><?= $trabajador['id_trabajador'] ?></td>
                            <td><?= $trabajador['nombre'] ?></td>
                            <td><?= $trabajador['contacto'] ?: 'Sin contacto' ?></td>
                            <td>
                                <a href="editar.php?id=<?= $trabajador['id_trabajador'] ?>" class="btn btn-warning btn-custom">Editar</a>
                                <a href="eliminar.php?id=<?= $trabajador['id_trabajador'] ?>" class="btn btn-danger btn-custom">Eliminar</a>
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