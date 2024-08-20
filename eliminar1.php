<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];

    $sql = "DELETE FROM productos WHERE id_producto = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();

    echo "Producto eliminado exitosamente.";
}
?>
<form action="eliminar_producto.php" method="POST">
    <button type="submit">Eliminar Producto</button>
</form>
