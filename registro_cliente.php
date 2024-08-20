<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validar si el email ya está registrado
    $sql = "SELECT id_cliente FROM clientes WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Insertar el nuevo usuario
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO clientes (nombre, email, contraseña) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sss', $nombre, $email, $hashed_password);
        $stmt->execute();

        $_SESSION['user_id'] = $mysqli->insert_id;
        header('Location: index.php');
        exit();
    } else {
        $error = "El correo electrónico ya está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="registro_cliente.css">
</head>
<body>
    <h2>Registrarse</h2>
    <form action="registro_cliente.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Registrarse</button>

        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
    <a href="index.php">Volver a Inicio</a>
</body>
</html>
