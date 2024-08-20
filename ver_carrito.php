
<?php
session_start();
include('db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['empleado_id']) && !isset($_SESSION['cliente_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario
$usuario_nombre = '';
if (isset($_SESSION['cliente_id'])) {
    $id_cliente = intval($_SESSION['cliente_id']);
    $sql_usuario = "SELECT nombre FROM clientes WHERE id_cliente = ?";
    $stmt = $mysqli->prepare($sql_usuario);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    if ($usuario) {
        $usuario_nombre = $usuario['nombre'];
    }
} elseif (isset($_SESSION['empleado_id'])) {
    $id_empleado = intval($_SESSION['empleado_id']);
    // Aquí deberías hacer una consulta similar para obtener el nombre del empleado si es necesario
}

// Inicializar variables
$total_pesos = 0;
$productos = [];

// Obtener el contenido del carrito
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
        $id_producto = intval($id_producto);
        $cantidad = intval($cantidad);

        $sql = "SELECT * FROM productos WHERE id_producto = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $producto = $resultado->fetch_assoc();

        if ($producto) {
            $producto['cantidad'] = $cantidad;
            $productos[] = $producto;
            $total_pesos += $producto['precio_producto'] * $cantidad;
        }
    }
}

// Manejar eliminación de producto
if (isset($_POST['eliminar'])) {
    $id_producto_eliminar = intval($_POST['id_producto']);
    if (isset($_SESSION['carrito'][$id_producto_eliminar])) {
        unset($_SESSION['carrito'][$id_producto_eliminar]);
    }
    header("Location: ver_carrito.php");
    exit;
}

// Manejar vaciado de carrito
if (isset($_POST['vaciar_carrito'])) {
    unset($_SESSION['carrito']);
    header("Location: ver_carrito.php");
    exit;
}

// Obtener provincias y sucursales
$sql_provincias = "SELECT * FROM provincias";
$result_provincias = $mysqli->query($sql_provincias);
$provincias = [];
while ($row = $result_provincias->fetch_assoc()) {
    $provincias[] = $row;
}

$sucursales = [];
foreach ($provincias as $provincia) {
    $provincia_id = $provincia['id'];
    $sql_sucursales = "SELECT * FROM sucursales WHERE provincia_id = ?";
    $stmt = $mysqli->prepare($sql_sucursales);
    $stmt->bind_param("i", $provincia_id);
    $stmt->execute();
    $result_sucursales = $stmt->get_result();
    $sucursales[$provincia['id']] = [];
    while ($row = $result_sucursales->fetch_assoc()) {
        $sucursales[$provincia['id']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carrito</title>
    <style>
        /* Incluye el CSS aquí */
        .cart-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-name {
            font-size: 1.2em;
            font-weight: bold;
        }
        .cart-item-description {
            color: #555;
            margin: 5px 0;
        }
        .cart-item-price {
            color: #e60000;
            font-size: 1.1em;
        }
        .cart-item-quantity {
            font-size: 0.9em;
            color: #333;
        }
        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
            color: #e60000;
        }
        #checkout-form {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input[type="radio"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button.checkout-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.1em;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button.checkout-button:hover {
            background-color: #218838;
        }
        #stripe-form {
            margin-top: 20px;
        }
        #card-element {
            background-color: white;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #card-errors {
            color: red;
            margin-top: 10px;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .clear-cart-button {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 10px 20px;
            font-size: 1.1em;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .clear-cart-button:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <?php if ($usuario_nombre): ?>
            <p>Hola, <?php echo htmlspecialchars($usuario_nombre); ?>!</p>
        <?php endif; ?>

        <?php if (empty($productos)): ?>
            <p>El carrito está vacío.</p>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <div class="cart-item">
                    <div class="cart-item-details">
                        <div class="cart-item-name"><?php echo htmlspecialchars($producto["nombre_producto"]); ?></div>
                        <div class="cart-item-description"><?php echo htmlspecialchars($producto["descripcion"]); ?></div>
                        <div class="cart-item-price">$<?php echo number_format($producto["precio_producto"], 2); ?></div>
                        <div class="cart-item-quantity">Cantidad: <?php echo htmlspecialchars($producto["cantidad"]); ?></div>
                    </div>
                    <form method="post" action="ver_carrito.php">
                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id_producto']); ?>">
                        <button type="submit" name="eliminar" class="delete-button">Eliminar</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div class="total-price">Total a pagar: $<?php echo number_format($total_pesos, 2); ?></div>

            <form method="post" action="ver_carrito.php">
                <button type="submit" name="vaciar_carrito" class="clear-cart-button">Vaciar Carrito</button>
            </form>

            <form id="checkout-form" action="confirmar_venta.php" method="post">
                <div class="form-group">
                    <label for="provincia">Selecciona una provincia</label>
                    <select id="provincia" name="provincia_id" required>
                        <option value="">Selecciona una provincia</option>
                        <?php foreach ($provincias as $provincia): ?>
                            <option value="<?php echo htmlspecialchars($provincia['id']); ?>"><?php echo htmlspecialchars($provincia['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sucursal">Selecciona una sucursal</label>
                    <select id="sucursal" name="sucursal_id" required>
                        <option value="">Selecciona una sucursal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="metodo_pago">Selecciona el método de pago</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="">Selecciona un método de pago</option>
                        <option value="stripe">Tarjeta de Crédito (Stripe)</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <div id="stripe-form" style="display: none;">
                    <label for="card-element">Tarjeta de Crédito</label>
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                </div>
                <button type="submit" class="checkout-button">Confirmar Venta</button>
            </form>
        <?php endif; ?>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.getElementById('metodo_pago').addEventListener('change', function() {
                var metodoPago = this.value;
                var stripeForm = document.getElementById('stripe-form');
                if (metodoPago === 'stripe') {
                    stripeForm.style.display = 'block';
                } else {
                    stripeForm.style.display = 'none';
                }
            });

            var stripe = Stripe('pk_test_51PmzkZAR5BxYuA7tF3qKykWKOsMBaXN0r1MetbW8VKHcVyqI2pdYOMGp9xrNEqG2T6cHqFtXhbqniMrVyAk6c1Gz00nfbUiUso'); // Reemplaza con tu clave pública de Stripe
            var elements = stripe.elements();
            var cardElement = elements.create('card');
            cardElement.mount('#card-element');

            var form = document.getElementById('checkout-form');
            form.addEventListener('submit', function(event) {
                if (document.getElementById('metodo_pago').value === 'stripe') {
                    event.preventDefault();
                    stripe.createToken(cardElement).then(function(result) {
                        if (result.error) {
                            var errorElement = document.getElementById('card-errors');
                            errorElement.textContent = result.error.message;
                        } else {
                            var tokenInput = document.createElement('input');
                            tokenInput.setAttribute('type', 'hidden');
                            tokenInput.setAttribute('name', 'stripeToken');
                            tokenInput.setAttribute('value', result.token.id);
                            form.appendChild(tokenInput);
                            form.submit();
                        }
                    });
                }
            });

            document.getElementById('provincia').addEventListener('change', function() {
                var provincia_id = this.value;
                var sucursalSelect = document.getElementById('sucursal');
                sucursalSelect.innerHTML = '<option value="">Selecciona una sucursal</option>'; // Limpiar opciones

                if (provincia_id) {
                    var sucursales = <?php echo json_encode($sucursales); ?>;
                    var opciones = sucursales[provincia_id] || [];
                    for (var i = 0; i < opciones.length; i++) {
                        var sucursal = opciones[i];
                        var option = document.createElement('option');
                        option.value = sucursal.id;
                        option.textContent = sucursal.nombre;
                        sucursalSelect.appendChild(option);
                    }
                }
            });
        </script>
    </div>
</body>
</html>
