<?php
require 'config.php'; // Archivo de configuración de Stripe

header('Content-Type: application/json');

$amount = 1000; // Monto en centavos (por ejemplo, 1000 centavos = 10 pesos)

// Crear un PaymentIntent
$paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => $amount,
    'currency' => 'ars', // Código de moneda para pesos argentinos
    'payment_method_types' => ['card'],
]);

echo json_encode([
    'clientSecret' => $paymentIntent->client_secret,
]);
?>
