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

// Manejar la solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $rfc = $_POST['rfc'];
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $account_number = $_POST['account_number'] ?? '';

    // Validar datos
    if (strlen($rfc) !== 13) {
        echo "Error: El RFC debe tener exactamente 13 caracteres.";
        exit();
    }

    if (!empty($phone) && (!is_numeric($phone) || strlen($phone) > 15)) {
        echo "Error: El teléfono debe ser un número válido de hasta 15 dígitos.";
        exit();
    }

    // Verificar duplicados en RFC
    $query = "SELECT RFC FROM Clientes WHERE RFC = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $rfc);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: El RFC ya está registrado.";
        $stmt->close();
        exit();
    }

    // Generar número de cuenta automáticamente si está vacío
    if (empty($account_number)) {
        $account_number = "CTE" . time(); // Número único basado en la marca de tiempo
    }

    // Insertar los datos en la base de datos
    $query = "INSERT INTO Clientes (RFC, Nombre, Dirección, Teléfono, No_Cuenta) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $rfc, $name, $address, $phone, $account_number);

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
