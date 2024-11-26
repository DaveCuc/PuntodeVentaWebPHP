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

// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newtienda";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$resultado = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Si se presionó "Buscar por ID de Venta"
    if (isset($_POST['buscar_id'])) {
        $id_venta = $_POST['id_venta'];

        // Validar ID
        if (!is_numeric($id_venta)) {
            $resultado = "<p class='error'>Error: El ID debe ser un número válido.</p>";
        } else {
            // Consulta por ID de venta
            $query = "SELECT ventas.idVenta, ventas.FechaVenta, ventas.Total, usuarios.NombreUsuario, 
                             productos.Articulo, productos.Precio 
                      FROM ventas
                      INNER JOIN usuarios ON ventas.idUsuario = usuarios.idUsuario
                      INNER JOIN productos ON productos.idProducto = ventas.idProducto
                      WHERE ventas.idVenta = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_venta);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $resultado = "<table>
                                <thead>
                                    <tr>
                                        <th>ID Venta</th>
                                        <th>Fecha de Venta</th>
                                        <th>Total</th>
                                        <th>Usuario</th>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>";
                while ($row = $result->fetch_assoc()) {
                    $resultado .= "<tr>
                                    <td>{$row['idVenta']}</td>
                                    <td>{$row['FechaVenta']}</td>
                                    <td>\${$row['Total']}</td>
                                    <td>{$row['NombreUsuario']}</td>
                                    <td>{$row['Articulo']}</td>
                                    <td>\${$row['Precio']}</td>
                                  </tr>";
                }
                $resultado .= "</tbody>
                              </table>";
            } else {
                $resultado = "<p class='error'>No se encontraron ventas con el ID proporcionado.</p>";
            }

            $stmt->close();
        }
    }

    // Si se presionó "Mostrar Todos"
    if (isset($_POST['mostrar_todos'])) {
        $query = "SELECT ventas.idVenta, ventas.FechaVenta, ventas.Total, usuarios.NombreUsuario, 
                         productos.Articulo, productos.Precio 
                  FROM ventas
                  INNER JOIN usuarios ON ventas.idUsuario = usuarios.idUsuario
                  INNER JOIN productos ON productos.idProducto = ventas.idProducto";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $resultado = "<table>
                            <thead>
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Fecha de Venta</th>
                                    <th>Total</th>
                                    <th>Usuario</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>";
            while ($row = $result->fetch_assoc()) {
                $resultado .= "<tr>
                                <td>{$row['idVenta']}</td>
                                <td>{$row['FechaVenta']}</td>
                                <td>\${$row['Total']}</td>
                                <td>{$row['NombreUsuario']}</td>
                                <td>{$row['Articulo']}</td>
                                <td>\${$row['Precio']}</td>
                              </tr>";
            }
            $resultado .= "</tbody>
                          </table>";
        } else {
            $resultado = "<p class='error'>No hay datos de ventas registrados.</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Ventas</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="src/scripts.js" defer></script>

    <style>
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(90deg, #ff8fa0, #ffd77e);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 80px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }
        .form-group button {
            padding: 10px;
            margin-top: 10px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        table th {
            background-color: #f4f4f4;
        }
        .error {
            color: red;
            font-weight: bold;
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
        <h1>Consulta de Ventas</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_venta">ID de Venta:</label>
                <input type="text" id="id_venta" name="id_venta">
                <button type="submit" name="buscar_id">Buscar</button>
            </div>
            <div class="form-group">
                <button type="submit" name="mostrar_todos">Mostrar Todos</button>
            </div>
        </form>
        <div class="table-container">
            <?php echo $resultado; ?>
        </div>
    </div>
    <footer>
        <p>© 2024 Pretty Woman Boutique. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
