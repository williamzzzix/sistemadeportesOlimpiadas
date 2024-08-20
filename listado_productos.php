<?php
session_start();
require_once 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['empleado_id']) && !isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario de la sesión
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';

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
        <a href="ver_carrito.php" id="cart-button">Ver Carrito</a>
        <div id="cart-notification" class="added-to-cart">
            Producto agregado al carrito!
        </div>
        <a href="index.php" class="btn-back">Volver al Inicio</a>
    </header>
    <div class="form-container">
        <form action="listado_productos.php" method="post">
            <legend>Búsqueda de Productos</legend>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" placeholder="Buscar" name="nombre_de_producto" style="margin-right: 10px;">
                <button class="botonbusqueda" type="submit" title="Buscar"><img src="search.svg" alt="Buscar"></button>
            </div>
        </form>
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
                    
                    // Formulario para agregar al carrito (solo para clientes)
                    if (isset($_SESSION['cliente_id'])) {
                        echo "<form class='add-to-cart-form' id='form".$row['id_producto']. "'  action='agregar_al_carrito.php' method='post'>";
                        echo "<input type='hidden' name='id_producto' value='{$row['id_producto']}'>";
                        echo "<input type='hidden' name='nombre_producto' value='{$row['nombre_producto']}'>";
                        echo "<input type='hidden' name='precio_producto' value='{$row['precio_producto']}'>";
                        echo "<label for='cantidad'>Cantidad:</label>";
                        echo "<input type='number' name='cantidad' min='1' max='{$row['cantidad_disponible']}' required>";
                        echo "<button id='".$row['id_producto']."' onclick='enviarformulario(".$row['id_producto'].")'>Agregar al Carrito</button>";
                        echo "</form>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No hay productos disponibles.</p>";
            }
            ?>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartForms = document.querySelectorAll('.add-to-cart-form');

        addToCartForms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevenir el envío del formulario

                const formData = new FormData(form);
                
                fetch('agregar_al_carrito.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Opcionalmente, puedes actualizar la interfaz para reflejar el cambio en el carrito
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
    </script>
</body>
</html>
