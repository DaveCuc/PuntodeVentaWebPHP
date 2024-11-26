<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda en Línea</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .product-card {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.7);
            border-radius: 8px;
            width: 22%;
            margin-bottom: 30px;
            padding: 15px;
            text-align: center;
            overflow: hidden;
        }
        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .product-card p {
            font-size: 14px;
            color: #555;
            height: 60px;
            overflow: hidden;
        }
        .price {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        .add-to-cart-btn {
            background-color: pink;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .add-to-cart-btn:hover {
            background-color: pink;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h1>Bienvenidos a PRETTY WOMAN Boutique</h1>
    <table>
    <th>Compra </th>
    <th><form action="https://www.freecodecamp.org/" text-align: left> <button type="button" text-align = "center"> <img src="https://cdn-icons-png.flaticon.com/512/3144/3144456.png" height ="40" width="30" /></button> </form>
        
    </th>
    </table>
                <th></th>
                <th></th>
         
         
    <div class="container">
        <?php
        // Configuración de la conexión a la base de datos

       

        $host = "localhost";
        $usuario = "root";
        $password = "Monik123";
        $base_de_datos = "tienda2";

        $conn = new mysqli($host, $usuario, $password, $base_de_datos);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Error en la conexión: " . $conn->connect_error);
        }

        // Consulta SQL para obtener los productos
        $sql = "SELECT * FROM Productos";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            
            // Mostrar los productos
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<img src='" . $row['linkImagen'] . "' alt='" . $row['Articulo'] . "' />";
                echo "<h3>" . $row['Articulo'] . "</h3>";
                echo "<p>" . (strlen($row['Descripcion']) > 100 ? substr($row['Descripcion'], 0, 100) . "..." : $row['Descripcion']) . "</p>";
                echo "<div class='price'> $" . number_format($row['Precio'], 2) . " </div>";
                //echo "<button class='add-to-cart-btn onclick='window.open('http://www.tecnm.mx'''>Agregar al carrito</button>";
               // echo "<input class='add-to-cart-btn name='btnLogOut' type='button' value='Agregar al carrito' OnClick = 'location.href='http://www.tecnm.mx'''>";

                // echo'<br /><button location.href="page2.php">page 2</button>';
                
                echo '<form action="https://www.freecodecamp.org/"> <button class="add-to-cart-btn" type="submit">Agregar al carrito</button> </form>';

                //echo'<button onClick="redirect2()">abc</button>'; javascript 

                  //echo '<form action="https://www.freecodecamp.org/"> <button class="add-to-cart-btn" type="submit">Agregar al carrito</button> </form>';

                echo "</div>";
            }
        } else {
            echo "<p>No hay productos disponibles en la tienda.</p>";
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </div>

</body>
</html>
