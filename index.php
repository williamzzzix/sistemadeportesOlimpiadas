<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeportesOlimpiadas - Inicio</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1>Bienvenido a DeportesOlimpiadas</h1>
    <nav>
        <a href="listado_productos.php">Ver Productos</a> |
        <a href="ver_carrito.php">Ver Carrito</a> |
        <?php if (isset($_SESSION['empleado_id']) || isset($_SESSION['cliente_id'])): ?>
            <!-- Mostrar un saludo al usuario logueado -->
            <span>Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</span> |
            <a href="logout.php">Cerrar Sesión</a>
        <?php else: ?>
            <a href="login.php">Iniciar Sesión</a> | 
            <a href="registro_cliente.php">Registrarse</a>
        <?php endif; ?>
    </nav>
</body>
</html>
