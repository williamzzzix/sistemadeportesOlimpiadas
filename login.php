<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe en la tabla clientes
    $sql = "SELECT id_cliente, nombre, email, contraseña, rol FROM clientes WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nombre'] = $usuario['nombre'];

            // Determinar el tipo de usuario según el rol
            if ($usuario['rol'] === 'cliente') {
                $_SESSION['usuario_tipo'] = 'cliente';
                $_SESSION['cliente_id'] = $usuario['id_cliente'];
                header("Location: index.php");
            } elseif ($usuario['rol'] === 'empleado') {
                $_SESSION['usuario_tipo'] = 'empleado';
                $_SESSION['empleado_id'] = $usuario['id_cliente']; // o usa otro identificador si es necesario
                header("Location: index_empleado.php");
            } else {
                $error = "Rol de usuario no reconocido.";
            }
            exit();
        } else {
            $error = "Credenciales incorrectas. Inténtalo de nuevo.";
        }
    } else {
        $error = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="inisesion.css">
</head>
<body>
    <div class="formulario">
        <h1>Iniciar Sesión</h1>
        <?php
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
        <form action="login.php" method="post">
            <div class="username">
                <input type="email" id="email" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="username">
                <input type="password" id="password" name="password" required>
                <label for="password">Contraseña</label>
            </div>
            <input type="submit" value="Iniciar Sesión">
        </form>
        <div class="registrarse">
            <p>¿No tienes una cuenta? <a href="registro_cliente.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>
