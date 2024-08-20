<?php
session_start();

if (isset($_POST['indice_producto']) && isset($_POST['cantidad_eliminar'])) {
    $indice_producto = $_POST['indice_producto'];
    $cantidad_eliminar = $_POST['cantidad_eliminar'];

    if (isset($_SESSION["carrito"][$indice_producto])) {
        $_SESSION["carrito"][$indice_producto]['cantidad'] -= $cantidad_eliminar;
        if ($_SESSION["carrito"][$indice_producto]['cantidad'] <= 0) {
            unset($_SESSION["carrito"][$indice_producto]);
        }
    }
}

header("Location: ver_carrito.php");
exit();
?>
