<?php
// Include database connection
include('db_connection.php');

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfc = $_POST['rfc'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $account_number = $_POST['account_number'];

    // Validate input
    if (strlen($rfc) !== 13) {
        echo "El RFC debe tener exactamente 13 caracteres.";
        exit();
    }

    if (!is_numeric($phone) || strlen($phone) > 15) {
        echo "El teléfono debe ser un número válido de hasta 15 dígitos.";
        exit();
    }

    // Check for duplicate RFC
    $check_rfc_query = "SELECT RFC FROM Clientes WHERE RFC = ?";
    $stmt = $conn->prepare($check_rfc_query);
    $stmt->bind_param("s", $rfc);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "El RFC ya está registrado.";
    } else {
        // Generate account number if not provided
        if (empty($account_number)) {
            $account_number = "CTE" . time(); // Simple auto-generated account number
        }

        // Insert user into database
        $insert_query = "INSERT INTO Clientes (RFC, Nombre, Dirección, Teléfono, No_Cuenta) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssss", $rfc, $name, $address, $phone, $account_number);

        if ($stmt->execute()) {
            echo "Registro exitoso.";
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}
$conn->close();
?>

<form method="POST" action="">
    <label for="rfc">RFC:</label>
    <input type="text" id="rfc" name="rfc" maxlength="13" required>

    <label for="name">Nombre:</label>
    <input type="text" id="name" name="name" maxlength="100" required>

    <label for="address">Dirección:</label>
    <input type="text" id="address" name="address" maxlength="200">

    <label for="phone">Teléfono:</label>
    <input type="text" id="phone" name="phone" maxlength="15">

    <label for="account_number">Número de Cuenta (opcional):</label>
    <input type="text" id="account_number" name="account_number" maxlength="20">

    <button type="submit">Registrar</button>
</form>
