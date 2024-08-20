<?php 
require_once 'db.php';

// Capturar los datos del formulario
$nombre_producto = $_POST['nombre_producto'];
$descripcion = $_POST['descripcion'];
$cantidad_disponible = $_POST['cantidad_disponible'];
$precio_producto = $_POST['precio_producto'];

// Manejar la carga de la imagen
$imagen_nombre = $_FILES['imagen']['name'];
$imagen_temp = $_FILES['imagen']['tmp_name'];
$imagen_path = "imagenes_productos/" . basename($imagen_nombre);

// Verificar si se ha seleccionado una imagen
if (!empty($imagen_nombre)) {
    // Mover la imagen a la ubicación deseada
    if (move_uploaded_file($imagen_temp, $imagen_path)) {
        $insert = "INSERT INTO productos (nombre_producto, descripcion, cantidad_disponible, precio_producto, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert);
        $stmt->bind_param('ssids', $nombre_producto, $descripcion, $cantidad_disponible, $precio_producto, $imagen_nombre);
    } else {
        echo "Error al subir la imagen.";
        exit();
    }
} else {
    $insert = "INSERT INTO productos (nombre_producto, descripcion, cantidad_disponible, precio_producto) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($insert);
    $stmt->bind_param('ssid', $nombre_producto, $descripcion, $cantidad_disponible, $precio_producto);
}

// Ejecutar la consulta
if ($stmt->execute()) {
    echo '<script language="javascript">
            alert("Se agregó la información correctamente, redireccionando");
            self.location = "listado_productos.php";
          </script>';
} else {
    echo "Error al insertar datos: " . $stmt->error;
}

// Cerrar la declaración
$stmt->close();

// Cerrar la conexión
$mysqli->close();
?>
