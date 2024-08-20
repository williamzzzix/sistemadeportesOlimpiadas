<?php
// Configuración para PayPal
define('PAYPAL_CLIENT_ID', 'AUsZkRCNu6RSsPV_88xlZF8H2wBgjkbe_ZMSNn5ElCvRq2Zw5ALnVUfNws1MdRMY5h_P0cG9RjRnm22N');
define('PAYPAL_SECRET', 'EDlj2cBBggNeYapzyudJdF-Br-MZlkXQAxPM5z-XEinczxHrmFChnVhKVH1vhyHdxMno2PH3xpdEID1L');

// Configuración para Stripe
require_once 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
define('STRIPE_API_KEY', 'sk_test_51PmzkZAR5BxYuA7tXqhttgcbbLgiIfhFeemJxMhaVXcVknYoUkCeLE0GEZHGYXXpDYMwiDNvNGtOorBBE1Hpkv7T00zdXwSXIQ');
\Stripe\Stripe::setApiKey(STRIPE_API_KEY);
?>
