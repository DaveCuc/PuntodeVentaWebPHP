<?php
// Incluir la conexión a la base de datos
include('db_connection2.php');

$resultado = "";

// Manejar la solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];
    $rfc = $_POST['rfc'] ?? null;
    $nombre = $_POST['nombre_cliente'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $no_cuenta = $_POST['no_cuenta'] ?? null;

    // Validar datos
    if (empty($nombre_usuario) || empty($contraseña) || empty($rol)) {
        $resultado = "Error: Todos los campos obligatorios deben completarse.";
    } else {
        // Hash de la contraseña
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar en la base de datos
        $query = "INSERT INTO Usuarios (NombreUsuario, Contraseña, Rol, RFC, Nombre, Dirección, Teléfono, No_Cuenta) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $nombre_usuario, $contraseña_hash, $rol, $rfc, $nombre, $direccion, $telefono, $no_cuenta);

        if ($stmt->execute()) {
            // Iniciar sesión automáticamente
            session_start();
            $_SESSION['idUsuario'] = $conn->insert_id; // Usar el ID del usuario recién insertado
            $_SESSION['nombreUsuario'] = $nombre_usuario;
            $_SESSION['rol'] = $rol;

            // Redirigir a la página principal
            header("Location: home.php");
            exit();
        } else {
            $resultado = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pretty Woman Boutique - Crear Cuenta</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="../src/scripts.js" defer></script>
</head>
<body>
    <div class="navbar">
        <a href="home.php">
            <h1>PRETTY WOMAN Boutique</h1>
        </a>
    </div>

   

    </table>

    <div class="container">
        <div class="create-box">
            <h2>Crear cuenta</h2>
            <form method="POST" action="">
                <input type="text" name="nombre_usuario" placeholder="Nombre de Usuario" required>
                <input type="text" name="nombre_cliente" placeholder="Nombre de Cliente" required>
                <label for="role">Selecciona tu rol:</label>
                <select id="role" name="rol" style="width: 70%; padding: 10px; margin-bottom: 15px; border: 2px solid #ff99ff; border-radius: 5px; font-size: 1em;" required>
                    <option value="Cliente">Cliente</option>
                    <option value="Administrador">Administrador</option>
                </select>
                <input type="text" name="rfc" placeholder="RFC (Opcional)">
                <input type="text" name="direccion" placeholder="Dirección">
                <input type="text" name="telefono" placeholder="Teléfono">
                <input type="text" name="no_cuenta" placeholder="Número de Cuenta">
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit" class="icon">Crear</button>
            </form>
            <div class="result">
                <?php echo $resultado; ?>
            </div>
        </div>
    </div>
</body>
</html>
