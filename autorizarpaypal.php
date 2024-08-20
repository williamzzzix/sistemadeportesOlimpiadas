<?php
session_start();
require 'paypal_config.php';

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

// Crear el pagador
$payer = new Payer();
$payer->setPaymentMethod('paypal');

// Crear una transacción
$amount = new Amount();
$amount->setCurrency('USD')
    ->setTotal($_POST['total_venta']);

$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setDescription('Compra en DeportesOlimpiadas');

// Crear la URL de redirección después del pago
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('http://yourdomain.com/execute_payment.php')
    ->setCancelUrl('http://yourdomain.com/cancel_payment.php');

// Crear el pago
$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction])
    ->setRedirectUrls($redirectUrls);

// Ejecutar el pago
try {
    $payment->create($apiContext);
    // Redirigir al cliente a PayPal
    header('Location: ' . $payment->getApprovalLink());
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
