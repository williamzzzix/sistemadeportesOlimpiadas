<?php
require_once 'db.php'; // Archivo de conexión a la base de datos

session_start();

// Verificar si el usuario es un jefe de ventas o personal de ventas
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'vendedor') {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no está autenticado
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cliente_id"])) {
    $cliente_id = $_POST["cliente_id"];

    // Consultar el estado de cuenta del cliente
    $sql_estado_cuenta = "SELECT f.id_factura, f.fecha, f.monto_total, f.estado FROM facturas f WHERE f.cliente_id = ? ORDER BY f.fecha DESC";
    $stmt = $mysqli->prepare($sql_estado_cuenta);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result_estado_cuenta = $stmt->get_result();
    
    // Obtener el nombre del cliente
    $cliente_query = $mysqli->query("SELECT nombre FROM clientes WHERE id_cliente = $cliente_id");
    $cliente = $cliente_query->fetch_assoc();
    $nombre_cliente = $cliente['nombre'];
} else {
    header("Location: estado_cuenta.php"); // Redirigir si no se ha enviado el cliente_id
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - Cliente</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de tener un archivo CSS para los estilos -->
</head>
<body>
    <h1>Estado de Cuenta de <?php echo $nombre_cliente; ?></h1>

    <table>
        <tr>
            <th>ID Factura</th>
            <th>Fecha</th>
            <th>Monto Total</th>
            <th>Estado</th>
        </tr>
        <?php while ($row = $result_estado_cuenta->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id_factura']; ?></td>
            <td><?php echo $row['fecha']; ?></td>
            <td>$<?php echo number_format($row['monto_total'], 2); ?></td>
            <td><?php echo $row['estado']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="estado_cuenta.php">Volver a la lista de facturas</a>
</body>
</html>
