<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto</title>
    <link rel="website icon" type="svg" href="android2.svg">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        form {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        a {
            text-decoration: none;
            color: #3498db;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Agregar Nuevo Producto</h1>

    <form action="insertar_producto.php" method="POST" enctype="multipart/form-data">
        <label for="nombre_producto">Nombre del Producto</label>
        <input type="text" name="nombre_producto" required> <br>

        <label for="descripcion">Descripción</label>
        <input type="text" name="descripcion" required> <br>

        <label for="cantidad_disponible">Cantidad Disponible</label>
        <input type="number" name="cantidad_disponible" required> <br>

        <label for="precio_producto">Precio</label>
        <input type="number" name="precio_producto" step="0.01" required> <br>

        <label for="imagen">Seleccionar imagen:</label>
        <input type="file" name="imagen" id="imagen"> <br>

        <input type="submit" name="Guardar" value="Guardar">

        <a href="listado_productos.php">Volver al listado</a>
    </form>
</body>
</html>
