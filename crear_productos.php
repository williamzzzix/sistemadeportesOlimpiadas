<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO productos (nombre, precio, descripcion) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $nombre, $precio, $descripcion);
    $stmt->execute();

    echo "Producto creado exitosamente.";
}
?>
<form action="crear_producto.php" method="POST">
    <label for="nombre">Nombre del Producto:</label>
    <input type="text" id="nombre" name="nombre" required>
    <label for="precio">Precio:</label>
    <input type="number" id="precio" name="precio" required>
    <label for="descripcion">Descripción:</label>
    <input type="text" id="descripcion" name="descripcion" required>
    <button type="submit">Crear Producto</button>
</form>
