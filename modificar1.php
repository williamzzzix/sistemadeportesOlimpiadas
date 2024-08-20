<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <link rel="website icon" type="svg" href="android2.svg">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        h1 {
            background-color: #333;
            color: #fff;
            padding: 10px;
        }

        form {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>
<body>
    <h1>Modificar Producto</h1>

    <?php
    session_start();
    require_once 'db.php';

    // Verificar si el usuario está autenticado como empleado
    if (!isset($_SESSION['empleado_id'])) {
        header("Location: login.php");
        exit();
    }

    // Verificar conexión a la base de datos
    if (!$mysqli) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Preparar la consulta SQL para obtener los detalles del producto
    $sql = "SELECT * FROM productos WHERE id_producto = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $datos = $result->fetch_assoc();
    $stmt->close();

    if (!$datos) {
        echo "<p>Producto no encontrado.</p>";
        exit();
    }

    // Manejar la actualización del producto
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Guardar'])) {
        $nombre_producto = $_POST['nombre_producto'];
        $descripcion = $_POST['descripcion'];
        $cantidad_disponible = $_POST['cantidad_disponible'];
        $precio_producto = $_POST['precio_producto'];

        // Manejar la carga de la imagen
        $imagen_nombre = $_FILES['imagen']['name'];
        $imagen_temp = $_FILES['imagen']['tmp_name'];
        $imagen_path = "imagenes_productos/" . basename($imagen_nombre);

        if (!empty($imagen_nombre)) {
            move_uploaded_file($imagen_temp, $imagen_path);
            $sql = "UPDATE productos SET nombre_producto = ?, descripcion = ?, precio_producto = ?, cantidad_disponible = ?, imagen = ? WHERE id_producto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssdiss', $nombre_producto, $descripcion, $precio_producto, $cantidad_disponible, $imagen_nombre, $id_producto);
        } else {
            $sql = "UPDATE productos SET nombre_producto = ?, descripcion = ?, precio_producto = ?, cantidad_disponible = ? WHERE id_producto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssdii', $nombre_producto, $descripcion, $precio_producto, $cantidad_disponible, $id_producto);
        }
        
        $stmt->execute();
        $stmt->close();

        if ($stmt) {
            echo '<script language="javascript">
                alert("Se actualizó la información correctamente, redireccionando");
                self.location = "index_empleado.php";
            </script>';
        } else {
            echo '<font color="red" style="font-size: 16px;">No se actualizó la información correctamente</font>';
        }
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($datos['id_producto']); ?>">
        <label for="nombre_producto">Nombre del Producto</label>
        <input type="text" name="nombre_producto" value="<?php echo htmlspecialchars($datos['nombre_producto']); ?>"> <br>
        <label for="descripcion">Descripción</label>
        <input type="text" name="descripcion" value="<?php echo htmlspecialchars($datos['descripcion']); ?>"> <br>
        <label for="cantidad_disponible">Cantidad Disponible</label>
        <input type="number" name="cantidad_disponible" value="<?php echo htmlspecialchars($datos['cantidad_disponible']); ?>"> <br>
        <label for="precio_producto">Precio</label>
        <input type="number" name="precio_producto" value="<?php echo htmlspecialchars($datos['precio_producto']); ?>"> <br>
        <label for="imagen">Seleccionar nueva imagen:</label>
        <input type="file" name="imagen" id="imagen"> <br>
        <input type="submit" name="Guardar" value="Guardar">
    </form>

    <a href="index_empleado.php">Volver al listado</a>
</body>
</html>
