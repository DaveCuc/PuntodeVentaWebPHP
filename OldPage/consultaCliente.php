<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Tienda";

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
        $id_cliente = $_POST['id_cliente'];

        // Validar ID
        if (!is_numeric($id_cliente)) {
            $resultado = "Error: El ID debe ser un número válido.";
        } else {
            // Consulta por ID
            $query = "SELECT * FROM Clientes WHERE IdCliente = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id_cliente);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $resultado = "<table border='1'>
                                <tr>
                                    <th>ID</th>
                                    <th>RFC</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Número de Cuenta</th>
                                </tr>";
                while ($row = $result->fetch_assoc()) {
                    $resultado .= "<tr>
                                    <td>{$row['IdCliente']}</td>
                                    <td>{$row['RFC']}</td>
                                    <td>{$row['Nombre']}</td>
                                    <td>{$row['Dirección']}</td>
                                    <td>{$row['Teléfono']}</td>
                                    <td>{$row['No_Cuenta']}</td>
                                  </tr>";
                }
                $resultado .= "</table>";
            } else {
                $resultado = "No se encontró ningún cliente con el ID proporcionado.";
            }

            $stmt->close();
        }
    }

    // Si se presionó "Mostrar Todos"
    if (isset($_POST['mostrar_todos'])) {
        // Consulta para obtener todos los clientes
        $query = "SELECT * FROM Clientes";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $resultado = "<table border='1'>
                            <tr>
                                <th>ID</th>
                                <th>RFC</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th>Número de Cuenta</th>
                            </tr>";
            while ($row = $result->fetch_assoc()) {
                $resultado .= "<tr>
                                <td>{$row['IdCliente']}</td>
                                <td>{$row['RFC']}</td>
                                <td>{$row['Nombre']}</td>
                                <td>{$row['Dirección']}</td>
                                <td>{$row['Teléfono']}</td>
                                <td>{$row['No_Cuenta']}</td>
                              </tr>";
            }
            $resultado .= "</table>";
        } else {
            $resultado = "No hay clientes registrados.";
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
    <title>Consultar Cliente</title>
</head>
<body>
    <h1>Consultar Cliente</h1>
    <form method="POST" action="">
        <label for="id_cliente">ID del Cliente:</label>
        <input type="text" id="id_cliente" name="id_cliente"><br><br>

        <button type="submit" name="buscar_id">Buscar por ID</button>
        <button type="submit" name="mostrar_todos">Mostrar Todos</button>
    </form>
    <div>
        <?php echo $resultado; ?>
    </div>
</body>
</html>
