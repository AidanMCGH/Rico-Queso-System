<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Quesos</title>
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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Logo y nombre de la empresa -->
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo de la empresa"> <!-- Cambia la URL por la de tu logo -->
                Rico Queso S.A.
            </a>
            <!-- Botón para dispositivos móviles -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menú de navegación -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./directorio/index.php">Directorio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./produccion/index.php">Produccion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./finanzas/index.php">Administracion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container container-main">
        <h1 class="text-center mb-4">Bienvenido al Sistema de Gestión de Quesos</h1>
        <div class="d-grid gap-3">
            <a href="./directorio/index.php" class="btn btn-primary btn-custom">Directorio</a>
            <a href="./produccion/index.php" class="btn btn-secondary btn-custom">Produccion</a>
            <a href="./finanzas/index.php" class="btn btn-info btn-custom">Administracion</a>
            

        </div>
    </div>

    <!-- Bootstrap JS (opcional, para funcionalidades como el menú desplegable en móviles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
