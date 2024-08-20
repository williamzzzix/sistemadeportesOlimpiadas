<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('db.php'); // Archivo de conexión a la base de datos
require_once('vendor/autoload.php'); // Stripe

// Verificar si el usuario es un cliente autenticado
if (!isset($_SESSION['cliente_id']) || !isset($_SESSION['email'])) {
    die('No se ha encontrado el email en la sesión. Asegúrate de haber iniciado sesión como cliente.');
}

// Procesar la venta si el método de pago es Stripe o PayPal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    $provincia_id = isset($_POST['provincia']) ? intval($_POST['provincia']) : 0;
    $sucursal = isset($_POST['sucursal']) ? $_POST['sucursal'] : '';

    // Verificar si el carrito está vacío
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo '
        <div class="empty-cart-message">
            <h2>Tu carrito está vacío</h2>
            <p>Parece que no tienes productos en tu carrito. ¡Explora nuestros productos y encuentra algo que te guste!</p>
            <a href="index.php" class="btn">Volver a la tienda</a>
        </div>
        ';
        exit(); // Detener el script aquí si el carrito está vacío
    }


    if ($metodo_pago === 'stripe') {
        \Stripe\Stripe::setApiKey('sk_test_51PmzkZAR5BxYuA7tXqhttgcbbLgiIfhFeemJxMhaVXcVknYoUkCeLE0GEZHGYXXpDYMwiDNvNGtOorBBE1Hpkv7T00zdXwSXIQ'); // Clave secreta de Stripe

        $total_pesos = 0;
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $id_producto = intval($id_producto);
            $cantidad = intval($cantidad);

            $sql = "SELECT precio_producto FROM productos WHERE id_producto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $producto = $resultado->fetch_assoc();

            if ($producto) {
                $total_pesos += $producto['precio_producto'] * $cantidad;
            }
        }

        $total_pesos *= 100; // Convertir a centavos para Stripe

        $token = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : '';
        $email = $_SESSION['email']; // Obtener el email del usuario autenticado

        if (empty($token) || empty($email)) {
            die('Faltan datos para procesar el pago.');
        }

        try {
            $charge = \Stripe\Charge::create([
                'amount' => $total_pesos,
                'currency' => 'ars', // Moneda en pesos argentinos
                'description' => 'Compra en DeportesOlimpiadas',
                'source' => $token,
                'receipt_email' => $email,
            ]);

            // Procesar el pago exitoso
            // Vaciar el carrito
            unset($_SESSION['carrito']);

            // Mostrar el mensaje de éxito con estilo
            echo '
            <div class="ticket">
                <h2>Pago realizado con éxito</h2>
                <p>¡Gracias por tu compra!</p>
                <p><strong>Cliente:</strong> ' . htmlspecialchars($_SESSION['nombre']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($_SESSION['email']) . '</p>
                <p><strong>Total:</strong> $' . number_format($total_pesos / 100, 2) . ' ARS</p>
                <p><strong>Descripción:</strong> Compra en DeportesOlimpiadas</p>
            </div>
            ';
        } catch (Exception $e) {
            echo "<p>Hubo un error en el procesamiento del pago: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } elseif ($metodo_pago === 'paypal') {
        $total_pesos = 0;
        foreach ($_SESSION['carrito'] as $id_producto => $cantidad) {
            $id_producto = intval($id_producto);
            $cantidad = intval($cantidad);

            $sql = "SELECT precio_producto FROM productos WHERE id_producto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $producto = $resultado->fetch_assoc();

            if ($producto) {
                $total_pesos += $producto['precio_producto'] * $cantidad;
            }
        }

        $total_pesos = number_format($total_pesos, 2); // Formato en pesos argentinos

        $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        $business_email = "tu_email_de_paypal@example.com"; // Cambia a tu email de PayPal
        $return_url = "http://tusitio.com/confirmar_venta_paypal.php"; // URL de retorno tras el pago
        $notify_url = "http://tusitio.com/ipn_paypal.php"; // URL de notificación IPN
        $cancel_url = "http://tusitio.com/cancelar_venta.php"; // URL en caso de cancelación

        // Redirigir al usuario a PayPal
        header("Location: $paypal_url?cmd=_xclick&business=$business_email&currency_code=ARS&amount=$total_pesos&return=$return_url&notify_url=$notify_url&cancel_return=$cancel_url");
        exit();
    } else {
        die('Método de pago no válido.');
    }
} else {
    header("Location: ver_carrito.php");
    exit();
}
?>

<style>
    .ticket, .empty-cart-message {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        max-width: 400px;
        margin: 20px auto;
        font-family: Arial, sans-serif;
        text-align: center;
    }

    .ticket h2, .empty-cart-message h2 {
        color: #dc3545;
        margin-bottom: 15px;
    }

    .ticket p, .empty-cart-message p {
        margin: 5px 0;
        font-size: 14px;
    }

    .ticket strong, .empty-cart-message strong {
        font-weight: bold;
    }

    .empty-cart-message .btn {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }

    .empty-cart-message .btn:hover {
        background-color: #0056b3;
    }
</style>




