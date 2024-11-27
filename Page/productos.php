<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos del usuario desde la sesión
$idUsuario = $_SESSION['idUsuario'];
$nombreUsuario = $_SESSION['nombreUsuario'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'Cliente';

// Incluir la conexión a la base de datos
include('db_connection2.php');

// Manejo de solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['eliminar_producto'])) {
        // Eliminación lógica del producto
        $idProducto = $_POST['idProducto'];
        $query = "UPDATE productos SET estado = 'eliminado' WHERE idProducto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $idProducto);
        if ($stmt->execute()) {
            $mensaje = "Producto eliminado correctamente.";
        } else {
            $mensaje = "Error al eliminar el producto: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['actualizar_producto'])) {
        // Actualización del producto
        $idProducto = $_POST['idProducto'];
        $articulo = $_POST['articulo'];
        $descripcion = $_POST['descripcion'];
        $unidad = $_POST['unidad'];
        $modelo = $_POST['modelo'];
        $talla = $_POST['talla'];
        $color = $_POST['color'];
        $precio = $_POST['precio'];
        $linkImagen = $_POST['linkImagen'];
        $stock = $_POST['stock'];

        $query = "UPDATE productos SET Articulo = ?, Descripcion = ?, Unidad = ?, Modelo = ?, Talla = ?, Color = ?, Precio = ?, linkImagen = ?, stock = ? WHERE idProducto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssdssi", $articulo, $descripcion, $unidad, $modelo, $talla, $color, $precio, $linkImagen, $stock, $idProducto);
        if ($stmt->execute()) {
            $mensaje = "Producto actualizado correctamente.";
        } else {
            $mensaje = "Error al actualizar el producto: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Consultar productos con búsqueda
$productos = [];
$busqueda = $_GET['busqueda'] ?? '';
$query = "SELECT * FROM productos WHERE estado = 'activo' AND idProducto LIKE ?";
$stmt = $conn->prepare($query);
$likeBusqueda = "%$busqueda%";
$stmt->bind_param("s", $likeBusqueda);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="src/scripts.js" defer></script>


    <style>
        .container {
            display: flex;
            gap: 20px;
            justify-content: space-between;
        }

        .form-container, .product-list {
            background: linear-gradient(90deg, #ff8fa0, #ffd77e);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            flex: 1;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            margin-top: 10px;
        }

        .form-container input, .form-container textarea, .form-container select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .product-list {
            flex: 2;
        }

        .product-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-list th, .product-list td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .product-list th {
            background-color: #f2f2f2;
        }

        .product-list button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
        }

        .btn-update {
            background-color: #ffa726;
            color: white;
        }

        .btn-update:hover {
            background-color: #f57c00;
        }
    </style>

</head>
<body>
    <!-- Barra de navegación -->
    <div class="navbar">
    <a href="index.php">
        <h1>PRETTY WOMAN Boutique</h1>
    </a>
 
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
        <!-- Formulario de gestión -->
        <div class="form-container">
            <h2>Gestión de Productos</h2>
            <form method="POST" action="">
                <input type="hidden" name="idProducto" value="<?php echo $_GET['idProducto'] ?? ''; ?>">
                <label for="articulo">Artículo:</label>
                <input type="text" id="articulo" name="articulo" value="<?php echo $_GET['articulo'] ?? ''; ?>" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $_GET['descripcion'] ?? ''; ?></textarea>

                <label for="unidad">Unidad:</label>
                <input type="text" id="unidad" name="unidad" value="<?php echo $_GET['unidad'] ?? ''; ?>">

                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" value="<?php echo $_GET['modelo'] ?? ''; ?>">

                <label for="talla">Talla:</label>
                <input type="text" id="talla" name="talla" value="<?php echo $_GET['talla'] ?? ''; ?>">

                <label for="color">Color:</label>
                <input type="text" id="color" name="color" value="<?php echo $_GET['color'] ?? ''; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" value="<?php echo $_GET['precio'] ?? ''; ?>" required>

                <label for="linkImagen">Enlace de la Imagen:</label>
                <input type="url" id="linkImagen" name="linkImagen" value="<?php echo $_GET['linkImagen'] ?? ''; ?>" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo $_GET['stock'] ?? ''; ?>" required>

                <button type="submit" name="actualizar_producto">Guardar Producto</button>
            </form>
        </div>

        <!-- Lista de productos -->
        <div class="product-list">
            <h2>Lista de Productos</h2>
            <form method="GET" action="">
                <input type="text" name="busqueda" placeholder="Buscar por ID..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Artículo</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) { ?>
                        <tr>
                            <td><?php echo $producto['idProducto']; ?></td>
                            <td><?php echo $producto['Articulo']; ?></td>
                            <td><?php echo substr($producto['Descripcion'], 0, 50) . '...'; ?></td>
                            <td>$<?php echo number_format($producto['Precio'], 2); ?></td>
                            <td><?php echo $producto['stock']; ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="idProducto" value="<?php echo $producto['idProducto']; ?>">
                                    <button type="submit" name="eliminar_producto" class="btn-delete">Eliminar</button>
                                </form>
                                <a href="?idProducto=<?php echo $producto['idProducto']; ?>&articulo=<?php echo $producto['Articulo']; ?>&descripcion=<?php echo $producto['Descripcion']; ?>&unidad=<?php echo $producto['Unidad']; ?>&modelo=<?php echo $producto['Modelo']; ?>&talla=<?php echo $producto['Talla']; ?>&color=<?php echo $producto['Color']; ?>&precio=<?php echo $producto['Precio']; ?>&linkImagen=<?php echo $producto['linkImagen']; ?>&stock=<?php echo $producto['stock']; ?>">Editar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>© 2024 Pretty Woman Boutique. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
