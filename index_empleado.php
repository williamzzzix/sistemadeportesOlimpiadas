<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está autenticado como empleado
if (!isset($_SESSION['empleado_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Empleado';

// Verificar conexión a la base de datos
if (!$mysqli) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$nombre_de_producto = isset($_POST['nombre_de_producto']) ? $_POST['nombre_de_producto'] : '';

// Preparar la consulta SQL
$sql = "SELECT * FROM productos";
if (!empty($nombre_de_producto)) {
    $nombre_de_producto = $mysqli->real_escape_string($nombre_de_producto);
    $sql .= " WHERE nombre_producto LIKE '%$nombre_de_producto%'";
}

// Ejecutar la consulta SQL
$result = $mysqli->query($sql);

// Verificar si la consulta se ejecutó correctamente
if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}

// Si se envía el formulario para agregar/modificar/eliminar un producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'agregar') {
            // Lógica para agregar un nuevo producto
            $nombre_producto = $_POST['nombre_producto'];
            $descripcion = $_POST['descripcion'];
            $precio_producto = $_POST['precio_producto'];
            $cantidad_disponible = $_POST['cantidad_disponible'];
            $imagen = isset($_FILES['imagen']['name']) ? $_FILES['imagen']['name'] : '';

            // Guardar la imagen en el servidor
            if ($imagen) {
                $imagen_temp = $_FILES['imagen']['tmp_name'];
                $imagen_path = "imagenes_productos/" . basename($imagen);
                move_uploaded_file($imagen_temp, $imagen_path);
            }

            $sql = "INSERT INTO productos (nombre_producto, descripcion, precio_producto, cantidad_disponible, imagen) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssdii', $nombre_producto, $descripcion, $precio_producto, $cantidad_disponible, $imagen);
            $stmt->execute();
            $stmt->close();
        } elseif ($_POST['accion'] === 'modificar') {
            // Redirigir a la página de modificación del producto
            $id_producto = $_POST['id_producto'];
            header("Location: modificar1.php?id=$id_producto");
            exit();
        } elseif ($_POST['accion'] === 'eliminar') {
            // Lógica para eliminar un producto
            $id_producto = $_POST['id_producto'];

            $sql = "DELETE FROM productos WHERE id_producto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('i', $id_producto);
            $stmt->execute();
            $stmt->close();
        }

        // Redirigir para evitar reenvío del formulario
        header("Location: index_empleado.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="estiloss.css">
</head>
<body>
    <header>
        <h1>Listado de Productos</h1>
        <p>Hola, <?php echo htmlspecialchars($nombre_usuario); ?>. Bienvenido a la sección de productos.</p>
        <a href="index.php" class="btn-back">Volver al Inicio</a>
    </header>
    <div class="form-container">
        <form action="index_empleado.php" method="post">
            <legend>Búsqueda de Productos</legend>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" placeholder="Buscar" name="nombre_de_producto" style="margin-right: 10px;">
                <button class="botonbusqueda" type="submit" title="Buscar"><img src="search.svg" alt="Buscar"></button>
            </div>
        </form>
        <div class="form-container">
            <a href="agregar_producto.php" class="btn-add-product">Agregar Nuevo Producto</a>
        </div>
    </div>
    <main>
        <div class="product-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-item'>";
                    echo "<h2>{$row['nombre_producto']}</h2>";
                    echo "<p>{$row['descripcion']}</p>";
                    echo "<p>Precio: $" . number_format($row['precio_producto'], 2) . "</p>";

                    // Opciones de modificar y eliminar solo para empleados
                    echo "<form action='index_empleado.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='id_producto' value='{$row['id_producto']}'>";
                    echo "<input type='hidden' name='accion' value='modificar'>";
                    echo "<button type='submit'>Modificar</button>";
                    echo "</form>";
                    echo "<form action='index_empleado.php' method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='id_producto' value='{$row['id_producto']}'>";
                    echo "<input type='hidden' name='accion' value='eliminar'>";
                    echo "<button type='submit'>Eliminar</button>";
                    echo "</form>";

                    echo "</div>";
                }
            } else {
                echo "<p>No hay productos disponibles.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
