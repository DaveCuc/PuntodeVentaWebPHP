<?php
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
    // Si se presionó "Buscar por ID"
    if (isset($_POST['buscar_id'])) {
        $id_usuario = $_POST['id_usuario'];

        // Validar ID
        if (!is_numeric($id_usuario)) {
            $resultado = "<p class='error'>Error: El ID debe ser un número válido.</p>";
        } else {
            // Consulta por ID
            $query = "SELECT * FROM Usuarios WHERE idUsuario = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $resultado = "<table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre de Usuario</th>
                                        <th>Rol</th>
                                        <th>RFC</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Teléfono</th>
                                        <th>Número de Cuenta</th>
                                    </tr>
                                </thead>
                                <tbody>";
                while ($row = $result->fetch_assoc()) {
                    $resultado .= "<tr>
                                    <td>{$row['idUsuario']}</td>
                                    <td>{$row['NombreUsuario']}</td>
                                    <td>{$row['Rol']}</td>
                                    <td>{$row['RFC']}</td>
                                    <td>{$row['Nombre']}</td>
                                    <td>{$row['Dirección']}</td>
                                    <td>{$row['Teléfono']}</td>
                                    <td>{$row['No_Cuenta']}</td>
                                  </tr>";
                }
                $resultado .= "</tbody>
                              </table>";
            } else {
                $resultado = "<p class='error'>No se encontró ningún usuario con el ID proporcionado.</p>";
            }

            $stmt->close();
        }
    }

    // Si se presionó "Mostrar Todos"
    if (isset($_POST['mostrar_todos'])) {
        $query = "SELECT * FROM Usuarios";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $resultado = "<table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de Usuario</th>
                                    <th>Rol</th>
                                    <th>RFC</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Número de Cuenta</th>
                                </tr>
                            </thead>
                            <tbody>";
            while ($row = $result->fetch_assoc()) {
                $resultado .= "<tr>
                                <td>{$row['idUsuario']}</td>
                                <td>{$row['NombreUsuario']}</td>
                                <td>{$row['Rol']}</td>
                                <td>{$row['RFC']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Dirección']}</td>
                                <td>{$row['Teléfono']}</td>
                                <td>{$row['No_Cuenta']}</td>
                              </tr>";
            }
            $resultado .= "</tbody>
                          </table>";
        } else {
            $resultado = "<p class='error'>No hay usuarios registrados.</p>";
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
    <title>Consulta Usuarios</title>
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
        </div>
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

    <div class="container">
        <h1 class="title">Consulta Usuarios</h1>
        <form method="POST" action="" class="form">
            <div class="form-group">
                <label for="id_usuario">ID del Usuario:</label>
                <input type="text" id="id_usuario" name="id_usuario">
                <button type="submit" name="buscar_id" class="btn">🔍</button>
            </div>
            <div class="form-group">
                <button type="submit" name="mostrar_todos" class="btn">Mostrar Todos</button>
            </div>
        </form>
        <div class="table-container">
            <?php echo $resultado; ?>
        </div>
    </div>
</body>
</html>
