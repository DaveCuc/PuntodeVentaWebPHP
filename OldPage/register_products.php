<?php
// Incluir la conexión a la base de datos
include('db_connection.php');

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articulo = $_POST['articulo'];
    $descripcion = $_POST['descripcion'];
    $unidad = $_POST['unidad'];
    $modelo = $_POST['modelo'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $precio = $_POST['precio'];

    // Validar campos obligatorios
    if (empty($articulo) || empty($precio)) {
        echo "El artículo y el precio son obligatorios.";
        exit();
    }

    // Validar que el precio sea un número válido
    if (!is_numeric($precio) || $precio <= 0) {
        echo "El precio debe ser un número válido mayor a 0.";
        exit();
    }

    // Insertar producto en la base de datos
    $query = "INSERT INTO Productos (Articulo, Descripcion, Unidad, Modelo, Talla, Color, Precio) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssd", $articulo, $descripcion, $unidad, $modelo, $talla, $color, $precio);

    if ($stmt->execute()) {
        echo "Producto registrado exitosamente.";
    } else {
        echo "Error al registrar el producto: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
