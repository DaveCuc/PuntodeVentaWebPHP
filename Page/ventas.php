<?php
// Incluir la conexión a la base de datos
include('db_connection.php');

// Obtener los productos desde la base de datos
$productos_query = "SELECT idProducto, Articulo, Precio FROM Productos";
$productos_result = $conn->query($productos_query);

// Obtener los clientes desde la base de datos
$clientes_query = "SELECT IdCliente, Nombre FROM Clientes";
$clientes_result = $conn->query($clientes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Ventas</title>
    <style>
        /* Estilo igual que antes */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f4d9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff7e6;
            border: 2px solid #d1c4b2;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ff8fa0;
            padding: 10px;
            border-radius: 12px 12px 0 0;
        }
        .header h1 {
            font-size: 20px;
            color: white;
            margin: 0;
        }
        .menu {
            display: flex;
            gap: 10px;
        }
        .menu button {
            background-color: #b2e1e5;
            border: none;
            padding: 8px 15px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 14px;
        }
        .menu button:hover {
            background-color: #8ec9cb;
        }
        .form-title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        .buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .buttons button {
            background-color: #ff8fa0;
            border: none;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
        }
        .buttons button:hover {
            background-color: #ff687c;
        }
        .buttons .delete {
            background-color: #b2e1e5;
        }
        .buttons .delete:hover {
            background-color: #8ec9cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nombre de la Tienda</h1>
            <div class="menu">
                <button>Productos</button>
                <button>Clientes</button>
                <button>Ventas</button>
            </div>
        </div>
        <div class="form-title">Registrar Ventas</div>
        <form method="POST" action="register_sales.php">
            <!-- Menú desplegable para seleccionar el producto -->
            <div class="form-group">
                <label for="idProducto">Producto:</label>
                <select id="idProducto" name="idProducto" required>
                    <option value="">Selecciona un producto</option>
                    <?php
                    // Generar opciones dinámicas para productos
                    while ($row = $productos_result->fetch_assoc()) {
                        echo "<option value='" . $row['idProducto'] . "' data-precio='" . $row['Precio'] . "'>" . $row['Articulo'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <!-- Menú desplegable para seleccionar el cliente -->
            <div class="form-group">
                <label for="idCliente">Cliente:</label>
                <select id="idCliente" name="idCliente" required>
                    <option value="">Selecciona un cliente</option>
                    <?php
                    // Generar opciones dinámicas para clientes
                    while ($row = $clientes_result->fetch_assoc()) {
                        echo "<option value='" . $row['IdCliente'] . "'>" . $row['Nombre'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha de Venta:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" required>
            </div>
            <div class="form-group">
                <label for="total">Total:</label>
                <input type="number" id="total" name="total" step="0.01" readonly>
            </div>
            <div class="buttons">
                <button type="submit">Registrar</button>
                <button type="reset" class="delete">Borrar</button>
            </div>
        </form>
    </div>
    <script>
        const cantidadInput = document.getElementById('cantidad');
        const totalInput = document.getElementById('total');
        const idProductoSelect = document.getElementById('idProducto');

        idProductoSelect.addEventListener('change', calcularTotal);
        cantidadInput.addEventListener('input', calcularTotal);

        function calcularTotal() {
            const selectedOption = idProductoSelect.options[idProductoSelect.selectedIndex];
            const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
            const cantidad = parseInt(cantidadInput.value) || 0;
            totalInput.value = (precio * cantidad).toFixed(2);
        }
    </script>
</body>
</html>
<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
