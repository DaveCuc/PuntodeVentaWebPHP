<?php
// Configuración de la conexión a la base de datos
$servername = "localhost"; // Cambia según tu servidor
$username = "root"; // Usuario por defecto en XAMPP
$password = ""; // Contraseña por defecto en XAMPP
$dbname = "Tienda"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar la solicitud POST para buscar clientes
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $client_id = $_POST['client_id'];

    // Validar que el ID sea numérico
    if (!is_numeric($client_id)) {
        echo "Error: El ID del cliente debe ser un número válido.";
        exit();
    }

    // Consulta a la base de datos
    $query = "SELECT * FROM Clientes WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mostrar resultados
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>RFC</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Número de Cuenta</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['ID']}</td>
                    <td>{$row['RFC']}</td>
                    <td>{$row['Nombre']}</td>
                    <td>{$row['Dirección']}</td>
                    <td>{$row['Teléfono']}</td>
                    <td>{$row['No_Cuenta']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron resultados para el ID proporcionado.";
    }

    $stmt->close();
}

$conn->close();
?>
