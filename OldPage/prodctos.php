<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="src/scripts.js" defer></script>



</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
        <a href="home.php">
            <h1>PRETTY WOMAN Boutique</h1>
        </a>
        <div class="search-bar">
            <input type="text" placeholder="Buscar...">
            <span class="search-icon">🔍</span>
        </div>
        <div class="icons" style="margin-right: 20px;">
            <button class="icon" onclick="alert('Carrito: Actualmente no tienes artículos.')">🛒 <span>0</span></button>
            <div class="icons" style="margin-right: 20px;">
                <button class="icon" onclick="toggleDropdown()">👤</button>
                <div id="dropdown" class="dropdown" style="display: none;">
                    <ul>
                        <li><a href="productos.php">Productos</a></li>
                        <li><a href="c_clientes.php">Clientes</a></li>
                        <li><a href="ventas.php">Ventas</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



    <div class="container">
    <div class="create-box" style="width: 800px;">
    <table>
            <tr>
            <th> 
    <h1 text-align: center >Agregar Producto</h1>
    <form action="" method="post">

        <label for="articulo">Artículo:</label>
        <input type="text" id="articulo" name="articulo" >

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
        <input type="url" id="linkImagen" name="linkImagen" >

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" >
        <table>
            <tr>


            <th> <button type="submit" name="submity" class="create-box button">Guardar </button></th>
          
            <td>  <button type="submit2" name="submit2" class="create-box button">Consultar</button></td>
        </table>
        </div>



    </div>
        
    </form>

    
    <?php

    
    // Verificar si se envió el formulario
    if (isset($_POST['submity'])) {
        // Configuración de la conexión a la base de datos
        $host = "localhost";
        $usuario = "root";
        $password = "";
        $base_de_datos = "newtienda";

        // Crear la conexión
        $conn = new mysqli($host, $usuario, $password, $base_de_datos);

        // Verificar conexión
        if ($conn->connect_error) {
            echo "<div class='message error'>Error en la conexión: " . $conn->connect_error . "</div>";
            exit();
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
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssd", $articulo, $descripcion, $unidad, $modelo, $talla, $color, $linkImagen, $precio);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<div class='message success'>Producto agregado correctamente con el ID: " . $conn->insert_id . "</div>";
        } else {
            echo "<div class='message error'>Error al agregar el producto: " . $stmt->error . "</div>";
        }

        // Cerrar conexión
        $stmt->close();
        $conn->close();
    }
    ?>
    </th>
    <td> </td>
   
    <td></td>
    <td>
    <!--,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,-->

    <body>
    <?php
    if (isset($_POST['submit2'])) {
// Conexión a la base de datos
$link = mysqli_connect("localhost", "root", "", "newtienda");

// Verificar conexión
if (!$link) {
    die("Error conectando a la base de datos: " . mysqli_connect_error());
}

// Consulta SQL para obtener todos los productos
$sql = "SELECT * FROM Productos";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    // Crear la tabla HTML
    echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
    echo "<thead>
            <tr>
                <th>ID Producto</th>
                <th>Articulo</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Modelo</th>
                <th>Talla</th>
                <th>Color</th>
                <th>Imagen</th>
                <th>Precio</th>
            </tr>
          </thead>";
    echo "<tbody>";
    
    // Mostrar los registros de la base de datos
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['idProducto'] . "</td>";
        echo "<td>" . $row['Articulo'] . "</td>";
        echo "<td>" . (strlen($row['Descripcion']) > 100 ? substr($row['Descripcion'], 0, 100) . "..." : $row['Descripcion']) . "</td>";
        echo "<td>" . $row['Unidad'] . "</td>";
        echo "<td>" . $row['Modelo'] . "</td>";
        echo "<td>" . $row['Talla'] . "</td>";
        echo "<td>" . $row['Color'] . "</td>";
        echo "<td><img src='" . $row['linkImagen'] . "' alt='" . $row['Articulo'] . "' style='max-width:100px; max-height:100px;'></td>";
        echo "<td>$" . number_format($row['Precio'], 2) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "No se encontraron productos.";
}

// Cerrar la conexión
mysqli_close($link);
    }
?>


   </th>
   </td>
    <td> 


    <!-- Eliminar..........................-->
    <form method="POST" action="">
        <label for="ID">ID:</label>
        <input type="number" id="ID" name="ID" required>
        <button type="submit" name="submit" class="create-box button">Eliminar</button>
        </form>

    <?php
    // Conexión a la base de datos
    $link = mysqli_connect("localhost", "root", "", "newtienda");

    // Verificar conexión
    if (!$link) {
        die("Error conectando a la base de datos: " . mysqli_connect_error());
    }

    // Verificar si se ha enviado el ID del producto
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID'])) {
        // Obtener el ID del producto
        $idProducto = $_POST['ID'];

        // Consulta SQL para eliminar el producto con el ID dado
        $sql = "DELETE FROM Productos WHERE idProducto = ?";

        // Preparar la consulta
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Vincular el parámetro
            mysqli_stmt_bind_param($stmt, "i", $idProducto);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                echo "<p>Producto eliminado exitosamente.</p>";
            } else {
                echo "<p>Error al eliminar el producto.</p>";
            }

            // Cerrar la sentencia
            mysqli_stmt_close($stmt);
        } else {
            echo "<p>Error en la preparación de la consulta.</p>";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
    }

    // Cerrar la conexión
    mysqli_close($link);
    ?>



    </td>
    </table>
 </table>
</body>
    
</html>