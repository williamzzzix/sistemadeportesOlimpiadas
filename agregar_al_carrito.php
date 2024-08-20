<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto']) && isset($_POST['cantidad'])) {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);

    if ($cantidad <= 0) {
        echo json_encode(['success' => false, 'message' => 'Cantidad no válida']);
        exit();
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto] += $cantidad;
    } else {
        $_SESSION['carrito'][$id_producto] = $cantidad;
    }

    echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Datos del producto no válidos']);
}
?>
