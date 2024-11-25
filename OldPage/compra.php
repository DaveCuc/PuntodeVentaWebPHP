<?php
// Incluir la conexión a la base de datos
include('db_connection2.php');

// Inicializar la sesión para manejar el carrito
session_start();
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Vaciar el carrito
if (isset($_POST['vaciar_carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProducto']) && isset($_POST['cantidad'])) {
    $idProducto = $_POST['idProducto'];
    $cantidad = $_POST['cantidad'];

    $producto_query = "SELECT idProducto, Articulo, Precio, linkImagen, stock FROM productos WHERE idProducto = $idProducto";
    $producto_result = $conn->query($producto_query);
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

// Buscar productos para el autocompletado
if (isset($_GET['term'])) {
    $term = $conn->real_escape_string($_GET['term']);
    $query = "SELECT idProducto, Articulo FROM productos WHERE Articulo LIKE '%$term%' LIMIT 10";
    $result = $conn->query($query);

    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'idProducto' => $row['idProducto'],
            'Articulo' => $row['Articulo']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($productos);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .products, .cart-summary {
            background: linear-gradient(90deg, #ff8fa0, #ffd77e);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .products {
            flex: 2;
            max-width: 70%;
        }

        .cart-summary {
            flex: 1;
            max-width: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-box input {
            width: 80%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            margin-top: 5px;
        }

        .suggestions div {
            padding: 10px;
            cursor: pointer;
        }

        .suggestions div:hover {
            background-color: #f0f0f0;
        }

        .product-block {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-block img {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            margin-right: 10px;
        }

        .product-block-info {
            flex: 1;
            padding: 0 15px;
        }

        .product-block-actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .product-block-actions button {
            background-color: #ff8fa0;
            border: none;
            color: white;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .product-block-actions button:hover {
            background-color: #ff687c;
        }

        .product-block-actions input {
            width: 50px;
            text-align: center;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .delete-button {
            background-color: #ff4d4d;
            color: white;
            font-size: 14px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #e60000;
        }

        .cart-summary ul {
            width: 100%;
            text-align: left;
            padding: 0;
            list-style-type: none;
        }

        .cart-summary ul li {
            margin-bottom: 10px;
        }

        .cart-summary .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        .cart-summary button {
            background-color: #ffe600;
            color: black;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }

        .cart-summary button:hover {
            background-color: #ffcc00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="products">
            <div class="title">Buscar Productos</div>
            <div class="search-box">
                <input type="text" placeholder="Buscar producto por nombre o ID" id="search">
                <div id="suggestions" class="suggestions"></div>
            </div>
            <?php foreach ($_SESSION['carrito'] as $idProducto => $producto) { ?>
                <div class="product-block">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen del producto">
                    <div class="product-block-info">
                        <p><strong><?php echo htmlspecialchars($producto['nombre']); ?></strong></p>
                        <p>Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
                    </div>
                    <div class="product-block-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                            <input type="hidden" name="nueva_cantidad" value="<?php echo $producto['cantidad'] - 1; ?>">
                            <button type="submit" name="actualizar_cantidad" <?php echo $producto['cantidad'] <= 1 ? 'disabled' : ''; ?>>-</button>
                        </form>
                        <input type="number" value="<?php echo $producto['cantidad']; ?>" readonly>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                            <input type="hidden" name="nueva_cantidad" value="<?php echo $producto['cantidad'] + 1; ?>">
                            <button type="submit" name="actualizar_cantidad" <?php echo $producto['cantidad'] >= $producto['stock'] ? 'disabled' : ''; ?>>+</button>
                        </form>
                    </div>
                    <p>Total: $<?php echo number_format($producto['cantidad'] * $producto['precio'], 2); ?></p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
                        <button type="submit" name="eliminar_producto" class="delete-button">Eliminar</button>
                    </form>
                </div>
            <?php } ?>
        </div>
        <div class="cart-summary">
            <div class="title">Carrito de Compras</div>
            <ul>
                <?php if (empty($_SESSION['carrito'])) { ?>
                    <li>Tu carrito está vacío.</li>
                <?php } else { ?>
                    <?php foreach ($_SESSION['carrito'] as $producto) { ?>
                        <li><?php echo htmlspecialchars($producto['nombre']); ?> (<?php echo $producto['cantidad']; ?>)</li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <p class="total">Total: $<?php echo number_format(array_sum(array_map(function ($producto) {
                return $producto['cantidad'] * $producto['precio'];
            }, $_SESSION['carrito'])), 2); ?></p>
            <form method="POST">
                <button type="submit" name="vaciar_carrito">Vaciar Carrito</button>
            </form>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search');
        const suggestionsBox = document.getElementById('suggestions');

        searchInput.addEventListener('input', async () => {
            const term = searchInput.value.trim();

            if (term.length > 0) {
                const response = await fetch(`?term=${term}`);
                const products = await response.json();

                suggestionsBox.innerHTML = '';
                products.forEach(product => {
                    const div = document.createElement('div');
                    div.textContent = `${product.Articulo} (ID: ${product.idProducto})`;
                    div.dataset.idProducto = product.idProducto;
                    suggestionsBox.appendChild(div);
                });

                suggestionsBox.style.display = 'block';
            } else {
                suggestionsBox.style.display = 'none';
            }
        });

        suggestionsBox.addEventListener('click', (event) => {
            const selectedProduct = event.target;
            const idProducto = selectedProduct.dataset.idProducto;

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="idProducto" value="${idProducto}">
                <input type="hidden" name="cantidad" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
