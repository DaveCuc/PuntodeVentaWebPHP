<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php"); // Redirige al inicio de sesión si no está autenticado
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

    $producto_query = "SELECT idProducto, Articulo, Descripcion, Precio, linkImagen, stock FROM productos WHERE idProducto = $idProducto";
    $producto_result = $conn->query($producto_query);
    $producto = $producto_result->fetch_assoc();

    if ($producto && $producto['stock'] > 0) {
        if (isset($_SESSION['carrito'][$idProducto])) {
            $cantidad_nueva = $_SESSION['carrito'][$idProducto]['cantidad'] + $cantidad;
            $_SESSION['carrito'][$idProducto]['cantidad'] = min($cantidad_nueva, $producto['stock']);
        } else {
            $_SESSION['carrito'][$idProducto] = [
                'nombre' => $producto['Articulo'],
                'descripcion' => $producto['Descripcion'],
                'precio' => $producto['Precio'],
                'imagen' => $producto['linkImagen'],
                'cantidad' => min($cantidad, $producto['stock']),
                'stock' => $producto['stock']
            ];
        }
    }
}

// Obtener todos los productos en stock
$productos_query = "SELECT idProducto, Articulo, Descripcion, Precio, linkImagen, stock FROM productos WHERE stock > 0";
$productos_result = $conn->query($productos_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pretty Woman Boutique - Catálogo</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="src/scripts.js" defer></script>

    <style>
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background: linear-gradient(90deg, #ff8fa0, #ffd77e);
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .product-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 16px;
            margin: 5px 0;
        }

        .product-description {
            font-size: 14px;
            color: #555;
            margin: 10px 0;
        }

        .quantity-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .quantity-container input {
            width: 50px;
            text-align: center;
        }

        .add-to-cart-btn {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }

        .add-to-cart-btn:hover {
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
    <div class="search-bar">
        <input type="text" placeholder="Buscar...">
        <span class="search-icon">🔍</span>
    </div>
    <div class="icons">
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


    <!-- Contenido principal -->
    <div class="container">
        <h2>Bienvenido, <?php echo $nombreUsuario; ?>!</h2>
        <div class="product-container">
            <?php while ($producto = $productos_result->fetch_assoc()) { ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($producto['linkImagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['Articulo']); ?>">
                    <h3><?php echo htmlspecialchars($producto['Articulo']); ?></h3>
                    <p class="product-description"><?php echo htmlspecialchars($producto['Descripcion']); ?></p>
                    <p>Precio: $<?php echo number_format($producto['Precio'], 2); ?></p>
                    <p>Stock: <?php echo $producto['stock']; ?></p>
                    <form method="POST">
                        <div class="quantity-container">
                            <button type="button" onclick="changeQuantity(this, -1)">-</button>
                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>">
                            <button type="button" onclick="changeQuantity(this, 1)">+</button>
                        </div>
                        <input type="hidden" name="idProducto" value="<?php echo $producto['idProducto']; ?>">
                        <button type="submit" class="add-to-cart-btn">Agregar al Carrito</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
    <footer>
        <p>© 2024 Pretty Woman Boutique. Todos los derechos reservados.</p>
    </footer>
    <script>
        function changeQuantity(button, change) {
            const container = button.parentElement;
            const input = container.querySelector('input[name="cantidad"]');
            let currentValue = parseInt(input.value);
            const max = parseInt(input.max);

            currentValue += change;
            if (currentValue > max) currentValue = max;
            if (currentValue < 1) currentValue = 1;

            input.value = currentValue;
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
