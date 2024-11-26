<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario desde la sesión
$idUsuario = $_SESSION['idUsuario'];
$nombreUsuario = $_SESSION['nombreUsuario'];
$rolUsuario = $_SESSION['rol'];

// Incluir la conexión a la base de datos
include('db_connection2.php');

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProducto']) && isset($_POST['cantidad'])) {
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];

    $producto_query = "SELECT idProducto, Articulo, Precio, linkImagen, stock FROM productos WHERE idProducto = ?";
    $stmt = $conn->prepare($producto_query);
    $stmt->bind_param("i", $idProducto);
    $stmt->execute();
    $producto_result = $stmt->get_result();
    $producto = $producto_result->fetch_assoc();

    if ($producto) {
        if (isset($_SESSION['carrito'][$idProducto])) {
            $cantidad_nueva = $_SESSION['carrito'][$idProducto]['cantidad'] + $cantidad;
            $_SESSION['carrito'][$idProducto]['cantidad'] = min($cantidad_nueva, $producto['stock']);
        } else {
            $_SESSION['carrito'][$idProducto] = [
                'nombre' => $producto['Articulo'],
                'precio' => $producto['Precio'],
                'imagen' => $producto['linkImagen'],
                'cantidad' => min($cantidad, $producto['stock']),
                'stock' => $producto['stock']
            ];
        }
    }
}

// Actualizar cantidad de un producto
if (isset($_POST['actualizar_cantidad'])) {
    $idProducto = $_POST['idProducto'];
    $nueva_cantidad = $_POST['nueva_cantidad'];

    if (isset($_SESSION['carrito'][$idProducto])) {
        $_SESSION['carrito'][$idProducto]['cantidad'] = max(1, min($nueva_cantidad, $_SESSION['carrito'][$idProducto]['stock']));
    }
}

// Eliminar producto del carrito
if (isset($_POST['eliminar_producto'])) {
    $idProducto = $_POST['idProducto'];
    unset($_SESSION['carrito'][$idProducto]);
}

// Realizar compra
if (isset($_POST['realizar_compra'])) {
    // Registrar cada producto del carrito como una venta
    foreach ($_SESSION['carrito'] as $idProducto => $producto) {
        $cantidad = $producto['cantidad'];
        $total = $producto['cantidad'] * $producto['precio'];
        $fecha = date('Y-m-d');

        $venta_query = "INSERT INTO ventas (idProducto, idUsuario, FechaVenta, Total) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($venta_query);
        $stmt->bind_param("iisd", $idProducto, $idUsuario, $fecha, $total);

        if (!$stmt->execute()) {
            echo "Error al registrar la venta: " . $stmt->error;
            exit();
        }

        // Reducir el stock
        $nuevo_stock = $producto['stock'] - $cantidad;
        $stock_query = "UPDATE productos SET stock = ? WHERE idProducto = ?";
        $stmt_stock = $conn->prepare($stock_query);
        $stmt_stock->bind_param("ii", $nuevo_stock, $idProducto);

        if (!$stmt_stock->execute()) {
            echo "Error al actualizar el stock: " . $stmt_stock->error;
            exit();
        }
    }

    // Vaciar el carrito después de la compra
    $_SESSION['carrito'] = [];
    echo "<script>alert('Compra realizada con éxito.');</script>";
}

// Buscar productos para el autocompletado
if (isset($_GET['term'])) {
    $term = $conn->real_escape_string($_GET['term']);
    $query = "SELECT idProducto, Articulo FROM productos WHERE Articulo LIKE ? LIMIT 10";
    $likeTerm = "%$term%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $likeTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'idProducto' => $row['idProducto'],
            'Articulo' => $row['Articulo']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($productos);
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="src/scripts.js" defer></script>

    <style>
        .container {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: flex-start;
        }

        .products {
            display: block;            
            flex: 7;
            background: linear-gradient(90deg, #ff8fa0, #ffd77e);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-summary {
            flex: 3;
            background: linear-gradient(90deg, #ffd77e, #ff8fa0);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: left;
        }

        .product-block {
            position: relative;
            display: flex;
            background: white;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-block img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            margin-bottom: 10px;
            aling: left;
        }

        .product-info {
            felx: 1 1 auto;
            text-align: left;
            margin: 10px; 
        }

        .product-actions {
            display: flex;
            justify-content: center;
            align-items: left;
            gap: 10px;
            margin: 30px;
            
        }

        .delete-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            font-size: 20px;
            color: #e60000;
            cursor: pointer;
        }

        .delete-button:hover {
            color: #ff4d4d;
        }

        .checkout-button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .checkout-button:hover {
            background-color: #218838;
        }

    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
    <a href="home.php">
        <h1>PRETTY WOMAN Boutique</h1>
    </a>
    
    <div class="icons">
        <!-- Botón del carrito -->
        <!-- Botón del carrito -->
        <button class="icon" onclick="window.location='carrito.php'">
            🛒 <span>
                <?php
                // Calcular la cantidad total de productos en el carrito
                $totalProductosEnCarrito = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
                echo $totalProductosEnCarrito;
                ?>
            </span>
        </button>
        <!-- Botón del usuario -->
        <div>
            <button class="icon" onclick="toggleDropdown()">
                <?php echo $nombreUsuario; ?> 👤
            </button>
            <div id="dropdown" class="dropdown">
                <ul>
                    <?php if ($rolUsuario === 'Administrador'): ?>
                        <li><a href="productos.php">Gestionar Productos</a></li>
                        <li><a href="c_clientes.php">Gestionar Clientes</a></li>
                        <li><a href="ventas.php">Ver Ventas</a></li>
                    <?php else: ?>
                        <li><a href="carrito.php">Carrito de Compras</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
    </div>

    <div class="container">
        <!-- Productos en el carrito -->
        <div class="products">
            <h2>Productos en el Carrito</h2>
            <?php foreach ($_SESSION['carrito'] as $idProducto => $producto) { ?>
                <div class="product-block">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                        <button type="submit" name="eliminar_producto" class="delete-button">X</button>
                    </form>
                    
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen del producto">

                    <div class="product-info">
                        <p><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
                        <div class="product-info">
                        <p>Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                    </div>
                    
                    

                    <div class="product-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                            <input type="hidden" name="nueva_cantidad" value="<?php echo $producto['cantidad'] - 1; ?>">
                            <button type="submit" name="actualizar_cantidad" <?php echo $producto['cantidad'] <= 1 ? 'disabled' : ''; ?>>-</button>
                        </form>
                        <span><?php echo $producto['cantidad']; ?></span>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                            <input type="hidden" name="nueva_cantidad" value="<?php echo $producto['cantidad'] + 1; ?>">
                            <button type="submit" name="actualizar_cantidad" <?php echo $producto['cantidad'] >= $producto['stock'] ? 'disabled' : ''; ?>>+</button>
                        </form>
                    </div>

                    <div class="product-info">
                        <?php 
                            $totalAmount = $producto['cantidad'] * $producto['precio'];
                            $formattedTotal = number_format($totalAmount, 2);
                        ?>
                        <p style="font-weight: bold; font-size: 1.2em;">Total: $<?php echo $formattedTotal; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Resumen de compra -->
        <div class="cart-summary">
            <h2>Resumen de Compra</h2>

            <div class="product-info">
            <ul>
                <?php foreach ($_SESSION['carrito'] as $producto) { ?>
                    <li><?php echo htmlspecialchars($producto['nombre']); ?> (<?php echo $producto['cantidad']; ?>)</li>
                <?php } ?>
            </ul>
            </div>
            
            <div class="product-info" style="text-align: right; font-weight: bold; font-size: 24px;">
            <p class="total">Total: $<?php echo number_format(array_sum(array_map(function ($producto) {
                return $producto['cantidad'] * $producto['precio'];
            }, $_SESSION['carrito'])), 2); ?></p>
            </div>        

            
            <?php if (!empty($_SESSION['carrito'])) { ?>
                <form method="POST">
                    <button type="submit" name="realizar_compra" class="checkout-button">Realizar Compra</button>
                </form>
            <?php } ?>
        </div>
    </div>
    <footer>
        <p>© 2024 Pretty Woman Boutique. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
<?php
$conn->close();
?>
