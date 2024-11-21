<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newtienda";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['contraseña'];

    // Verificar si los campos están completos
    if (empty($nombre_usuario) || empty($contraseña)) {
        $mensaje = "<p class='error'>Por favor, complete todos los campos.</p>";
    } else {
        // Consulta para verificar credenciales
        $query = "SELECT * FROM Usuarios WHERE NombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verificar la contraseña
            if (password_verify($contraseña, $usuario['Contraseña'])) {
                // Iniciar sesión y redirigir según el rol
                session_start();
                $_SESSION['idUsuario'] = $usuario['idUsuario'];
                $_SESSION['nombreUsuario'] = $usuario['NombreUsuario'];
                $_SESSION['rol'] = $usuario['Rol'];

                if ($usuario['Rol'] === 'Administrador') {
                    header("Location: admin_dashboard.html");
                } elseif ($usuario['Rol'] === 'Vendedor') {
                    header("Location: vendedor_dashboard.html");
                } else {
                    header("Location: cliente_dashboard.html");
                }
                exit();
            } else {
                $mensaje = "<p class='error'>Contraseña incorrecta.</p>";
            }
        } else {
            $mensaje = "<p class='error'>El usuario no existe.</p>";
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
    <title>Pretty Woman Boutique - Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <a href="home.html">
            <h1>PRETTY WOMAN Boutique</h1>
        </a>
    </div>
    <div class="container">
        <div class="login-box">
            <h2>Inicio de sesión</h2>
            <form method="POST" action="">
                <input type="text" name="nombre_usuario" placeholder="Usuario" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button>
            </form>
            <a href="reg_clientes.php">Crear cuenta</a>
            <div class="result">
                <?php echo $mensaje; ?>
            </div>
        </div>
    </div>
</body>
</html>
