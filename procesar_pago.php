<?php
require 'config.php'; // Incluye la configuración de Stripe

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['stripeToken'];
    $total = $_POST['total_venta']; // Asegúrate de enviar el total de la venta

    try {
        // Crear el cargo en Stripe
        $charge = \Stripe\Charge::create([
            'amount' => $total * 100, // Stripe usa centavos
            'currency' => 'usd', // Cambia según tu moneda
            'description' => 'Pago por productos',
            'source' => $token,
        ]);

        // Procesa el resultado del cargo
        if ($charge->status == 'succeeded') {
            echo "Pago realizado con éxito.";
            // Aquí puedes guardar la información de la venta en tu base de datos

            // Redirige a una página de éxito o muestra un mensaje
            header("Location: confirmacion_pago.php");
        } else {
            echo "Error en el pago.";
        }
    } catch (\Stripe\Exception\CardException $e) {
        // Maneja errores de tarjeta
        echo "Error con la tarjeta: " . $e->getError()->message;
    } catch (\Stripe\Exception\ApiConnectionException $e) {
        // Maneja errores de conexión con la API
        echo "Error de conexión con Stripe: " . $e->getError()->message;
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        // Maneja errores de solicitud inválida
        echo "Error en la solicitud: " . $e->getError()->message;
    } catch (Exception $e) {
        // Maneja errores generales
        echo "Error: " . $e->getMessage();
    }
}
?>
