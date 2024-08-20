<?php
session_start();
require 'paypal_config.php';

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// Obtener el ID del pago y el Payer ID de la URL
$paymentId = $_GET['paymentId'];
$PayerID = $_GET['PayerID'];

// Crear el pago
$payment = Payment::get($paymentId, $apiContext);

// Crear la ejecución del pago
$paymentExecution = new PaymentExecution();
$paymentExecution->setPayerId($PayerID);

// Ejecutar el pago
try {
    $result = $payment->execute($paymentExecution, $apiContext);

    // Verificar el estado del pago
    if ($result->getState() == 'approved') {
        echo "Pago completado con éxito.";
        // Aquí puedes registrar la venta en la base de datos
    } else {
        echo "El pago no fue aprobado.";
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
