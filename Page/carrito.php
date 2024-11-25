<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newtienda";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit();
}

// Obtener los datos enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);
$idProducto = $data['idProducto'];
$idUsuario = $_SESSION['idUsuario'];

// Verificar si ya existe un carrito para este usuario
$sqlCarrito = "SELECT id_carrito FROM CarritoCompras WHERE id_usuario = $idUsuario";
$resultCarrito = $conn->query($sqlCarrito);

if ($resultCarrito->num_rows > 0) {
    $row = $resultCarrito->fetch_assoc();
    $idCarrito = $row['id_carrito'];
} else {
    // Crear un nuevo carrito si no existe
    $sqlNuevoCarrito = "INSERT INTO CarritoCompras (id_usuario) VALUES ($idUsuario)";
    if ($conn->query($sqlNuevoCarrito) === TRUE) {
        $idCarrito = $conn->insert_id;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el carrito']);
        exit();
    }
}

// Agregar el producto al carrito
$sqlAgregar = "INSERT INTO DetalleCarrito (id_carrito, id_producto, cantidad, subtotal) 
               VALUES ($idCarrito, $idProducto, 1, 
               (SELECT precio FROM Productos WHERE id_producto = $idProducto))";

if ($conn->query($sqlAgregar) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar el producto al carrito']);
}
?>
