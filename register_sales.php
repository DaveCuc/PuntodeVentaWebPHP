<?php
// Incluir la conexión a la base de datos
include('db_connection.php');

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProducto = $_POST['idProducto'];
    $idCliente = $_POST['idCliente'];
    $fechaVenta = $_POST['fecha'];
    $cantidad = $_POST['cantidad'];
    $total = $_POST['total'];

    // Validar campos obligatorios
    if (empty($idProducto) || empty($idCliente) || empty($fechaVenta) || empty($cantidad) || empty($total)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    // Validar que cantidad y total sean números válidos
    if (!is_numeric($cantidad) || $cantidad <= 0) {
        echo "La cantidad debe ser un número válido mayor a 0.";
        exit();
    }

    if (!is_numeric($total) || $total <= 0) {
        echo "El total debe ser un número válido mayor a 0.";
        exit();
    }

    // Insertar venta en la base de datos
    $query = "INSERT INTO Ventas (idProducto, idCliente, FechaVenta, Cantidad, Total) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisid", $idProducto, $idCliente, $fechaVenta, $cantidad, $total);

    if ($stmt->execute()) {
        echo "Venta registrada exitosamente.";
    } else {
        echo "Error al registrar la venta: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
