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

// Manejar la solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rfc = $_POST['rfc'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $no_cuenta = $_POST['no_cuenta'] ?? 'CTE' . time();

    // Validar datos
    if (strlen($rfc) !== 13) {
        echo "Error: El RFC debe tener exactamente 13 caracteres.";
        exit();
    }

    // Insertar en la base de datos
    $query = "INSERT INTO Clientes (RFC, Nombre, Dirección, Teléfono, No_Cuenta) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $rfc, $nombre, $direccion, $telefono, $no_cuenta);

    if ($stmt->execute()) {
        echo "Cliente registrado con éxito.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente</title>
</head>
<body>
    <h1>Registrar Cliente</h1>
    <form method="POST" action="">
        <label for="rfc">RFC:</label>
        <input type="text" id="rfc" name="rfc" maxlength="13" required><br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion"><br><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" maxlength="15"><br><br>

        <label for="no_cuenta">Número de Cuenta:</label>
        <input type="text" id="no_cuenta" name="no_cuenta" maxlength="20"><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
