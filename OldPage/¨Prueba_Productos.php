<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <style>
        body {
            background-color: #FFE4C4;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            max-width: 400px;
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }
        thead {
            background-color: pink;
            color: black;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Agregar Producto</h1>
    <form action="" method="post">
        <label for="articulo">Artículo:</label>
        <input type="text" id="articulo" name="articulo" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"></textarea>

        <label for="unidad">Unidad:</label>
        <input type="text" id="unidad" name="unidad">

        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo">

        <label for="talla">Talla:</label>
        <input type="text" id="talla" name="talla">

        <label for="color">Color:</label>
        <input type="text" id="color" name="color">

        <label for="linkImagen">Enlace de la Imagen:</label>
        <input type="url" id="linkImagen" name="linkImagen" required>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" required>

        <button type="submit" name="submity">Guardar</button>
    </form>

    <?php
    if (isset($_POST['submity'])) {
        // Configuración de la conexión a la base de datos
        $link = mysqli_connect("localhost", "root", "Monik123", "tienda2");

        // Verificar conexión
        if (!$link) {
            die("<div class='message error'>Error conectando a la base de datos: " . mysqli_connect_error() . "</div>");
        }

        // Recibir los datos del formulario
        $articulo = $_POST['articulo'];
        $descripcion = $_POST['descripcion'];
        $unidad = $_POST['unidad'];
        $modelo = $_POST['modelo'];
        $talla = $_POST['talla'];
        $color = $_POST['color'];
        $linkImagen = $_POST['linkImagen'];
        $precio = $_POST['precio'];

        // Consulta para insertar los datos
        $sql = "INSERT INTO Productos (Articulo, Descripcion, Unidad, Modelo, Talla, Color, linkImagen, Precio) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la consulta
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssssd", $articulo, $descripcion, $unidad, $modelo, $talla, $color, $linkImagen, $precio);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                echo "<div class='message success'>Producto agregado correctamente con el ID: " . mysqli_insert_id($link) . "</div>";
            } else {
                echo "<div class='message error'>Error al agregar el producto: " . mysqli_stmt_error($stmt) . "</div>";
            }

            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        } else {
            echo "<div class='message error'>Error en la preparación de la consulta: " . mysqli_error($link) . "</div>";
        }

        // Cerrar conexión
        mysqli_close($link);
    }
    ?>

    <!-- Tabla de productos -->
    <?php
    $link = mysqli_connect("localhost", "root", "Monik123", "tienda2");

    if ($link) {
        $sql = "SELECT * FROM Productos";
        $result = mysqli_query($link, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<h2 style='text-align: center;'>Productos Registrados</h2>";
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID Producto</th>
                            <th>Artículo</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Modelo</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Imagen</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['idProducto']}</td>
                        <td>{$row['Articulo']}</td>
                        <td>{$row['Descripcion']}</td>
                        <td>{$row['Unidad']}</td>
                        <td>{$row['Modelo']}</td>
                        <td>{$row['Talla']}</td>
                        <td>{$row['Color']}</td>
                        <td><img src='{$row['linkImagen']}' alt='{$row['Articulo']}' style='max-width:100px; max-height:100px;'></td>
                        <td>$" . number_format($row['Precio'], 2) . "</td>
                      </tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p style='text-align: center;'>No se encontraron productos registrados.</p>";
        }

        mysqli_close($link);
    }
    ?>
</body>
</html>
