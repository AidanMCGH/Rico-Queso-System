
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio</title>
    <!-- Bootstrap CSS -->
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
        }
        .navbar-custom {
            background-color: #343a40; /* Color de fondo oscuro */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand img {
            height: 40px; /* Tamaño del logo */
            margin-right: 10px;
        }
        .navbar-nav .nav-link {
            color: #fff !important; /* Texto blanco */
            font-size: 16px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        .navbar-nav .nav-link:hover {
            color: #ffc107 !important; /* Color amarillo al pasar el mouse */
        }
        .container-main {
            margin-top: 80px; /* Espacio para no solapar con la navbar */
            padding: 20px;
        }
        .btn-custom {
            width: 100%;
            margin: 10px 0;
            padding: 15px;
            font-size: 18px;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<?php 
    require_once '../config.php';
    include BASE_PATH . 'includes/navbar.php'; 
    ?>
    

    <!-- Contenido principal -->
    <div class="container container-main">
        <h1 class="text-center mb-4">Directorio</h1>
        <div class="d-grid gap-3">
            <a href="<?php echo BASE_URL; ?>directorio/clientes/index.php" class="btn btn-primary btn-custom">Clientes</a>
            <a href="<?php echo BASE_URL; ?>directorio/trabajadores/index.php" class="btn btn-dark btn-custom">Trabajadores</a>
            <a href="<?php echo BASE_URL; ?>directorio/proveedores/index.php" class="btn btn-primary btn-custom">Proveedores</a>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, para funcionalidades como el menú desplegable en móviles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>