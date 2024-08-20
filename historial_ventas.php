<?php
session_start();
if (!isset($_SESSION['empleado_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

$sql = "SELECT * FROM ventas ORDER BY fecha_venta DESC";
$result = $mysqli->query($sql);

echo "<h1>Historial de Ventas</h1>";
echo "<table>";
echo "<tr><th>ID Venta</th><th>Producto</th><th>Cantidad</th><th>Total</th><th>Fecha</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['id_venta'] . "</td><td>" . $row['producto'] . "</td><td>" . $row['cantidad'] . "</td><td>" . $row['total'] . "</td><td>" . $row['fecha_venta'] . "</td></tr>";
}
echo "</table>";
?>
