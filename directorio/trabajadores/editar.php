<?php
require_once '../../includes/conexion.php';
require_once '../../config.php';

$id_trabajador = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_trabajador) {
    header('Location: index.php');
    exit();
}

$sqlTrabajador = "SELECT * FROM trabajadores WHERE id_trabajador = ?";
$stmtTrabajador = $conexion->prepare($sqlTrabajador);
$stmtTrabajador->execute([$id_trabajador]);
$trabajador = $stmtTrabajador->fetch(PDO::FETCH_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $contacto = filter_input(INPUT_POST, 'contacto', FILTER_SANITIZE_STRING);

    if ($nombre) {
        $sql = "UPDATE trabajadores SET nombre = ?, contacto = ? WHERE id_trabajador = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre, $contacto, $id_trabajador]);
        header('Location: index.php');
        exit();
    } else {
        $error = "El nombre es obligatorio.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Trabajador</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . 'includes/navbar.php'; ?>

    <div class="container container-main">
        <h1 class="text-center mb-4">Editar Trabajador</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <div class="form-custom">
            <form method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $trabajador['nombre'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contacto" class="form-label">Contacto:</label>
                    <input type="text" class="form-control" id="contacto" name="contacto" value="<?= $trabajador['contacto'] ?>" placeholder="Ej. Teléfono o email">
                </div>
                <button type="submit" class="btn btn-primary btn-custom">Actualizar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>