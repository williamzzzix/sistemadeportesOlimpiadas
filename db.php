<?php
$host = 'localhost'; // Cambia esto si tu servidor es diferente
$user = 'root'; // Tu usuario de MySQL
$pass = ''; // Tu contraseña de MySQL
$db = 'deportesolimpiadas'; // El nombre de tu base de datos

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Error de Conexión (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error);
}
?>
