<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header('Location: login.php');
    exit();
}

// Mostrar opciones solo para empleados
echo "<h1>Panel de Control del Empleado</h1>";

echo "<a href='crear_producto.php'>Crear nuevo producto</a><br>";
echo "<a href='modificar_producto.php'>Modificar producto</a><br>";
echo "<a href='eliminar_producto.php'>Eliminar producto</a><br>";
echo "<a href='historial_ventas.php'>Ver historial de ventas</a><br>";

echo "<a href='logout.php'>Cerrar sesión</a>";
?>
