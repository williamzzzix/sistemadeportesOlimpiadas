<?php
session_start();
require 'db.php'; // Conexión a la base de datos

// Lógica para mostrar un mensaje de éxito al usuario
echo "<h1>Compra realizada con éxito</h1>";
echo "<p>Gracias por su compra. Hemos recibido su pago y estamos procesando su pedido.</p>";

// Aquí puedes incluir más detalles, como información del pedido o la posibilidad de imprimir un recibo.

// Vaciar el carrito después de la compra exitosa
unset($_SESSION['carrito']);
?>
