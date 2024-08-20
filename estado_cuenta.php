<?php
require_once 'db.php'; // Archivo de conexión a la base de datos

session_start();

// Verificar si el usuario es un jefe de ventas o personal de ventas
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'vendedor') {
    header("Location: login.php"); // Redirigir a la página de inicio de sesión si no está autenticado
    exit();
}

// Consultar las facturas a cobrar
$sql_facturas = "SELECT id_factura, cliente_id, fecha, monto_total, estado FROM facturas ORDER BY fecha DESC";
$result_facturas = $mysqli->query($sql_facturas);

// Consultar los estados de cuenta ordenados por cliente
$sql_clientes = "SELECT id_cliente, nombre FROM clientes";
$result_clientes = $mysqli->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta</title>
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de tener un archivo CSS para los estilos -->
</head>
<body>
    <h1>Estado de Cuenta</h1>

    <h2>Facturas a Cobrar</h2>
    <table>
        <tr>
            <th>ID Factura</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Monto Total</th>
            <th>Estado</th>
        </tr>
        <?php while ($row = $result_facturas->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id_factura']; ?></td>
            <td><?php
                $cliente_id = $row['cliente_id'];
                $cliente_query = $mysqli->query("SELECT nombre FROM clientes WHERE id_cliente = $cliente_id");
                $cliente = $cliente_query->fetch_assoc();
                echo $cliente['nombre'];
            ?></td>
            <td><?php echo $row['fecha']; ?></td>
            <td>$<?php echo number_format($row['monto_total'], 2); ?></td>
            <td><?php echo $row['estado']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Estados de Cuenta por Cliente</h2>
    <form action="estado_cuenta_cliente.php" method="post">
        <label for="cliente_id">Seleccione un Cliente:</label>
        <select id="cliente_id" name="cliente_id">
            <?php while ($row = $result_clientes->fetch_assoc()): ?>
            <option value="<?php echo $row['id_cliente']; ?>"><?php echo $row['nombre']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Ver Estado de Cuenta</button>
    </form>

    <a href="index.php">Volver a la página principal</a>
</body>
</html>
