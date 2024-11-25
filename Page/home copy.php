<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php"); // Redirige al inicio de sesión si no está autenticado
    exit();
}

// Obtener datos del usuario desde la sesión
$idUsuario = $_SESSION['idUsuario'];
$nombreUsuario = $_SESSION['nombreUsuario'];
$rolUsuario = $_SESSION['rol'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pretty Woman Boutique - Home</title>
    <link rel="stylesheet" href="css/styles.css"?v=<?php echo time(); ?>">"
    <script src="src/scripts.js" defer></script>
</head>
<body>
    <!-- Barra de navegación -->
<div class="navbar">
    <a href="home.php">
        <h1>PRETTY WOMAN Boutique</h1>
    </a>
    <div class="search-bar">
        <input type="text" placeholder="Buscar...">
        <span class="search-icon">🔍</span>
    </div>
    <div class="icons">
        <!-- Botón del carrito -->

        <button class="icon" onclick="window.location='carrito.php'">
            🛒 <span>0</span>
</button>

        <!-- Botón del usuario -->
        <div>
            <button class="icon" onclick="toggleDropdown()">
                <?php echo $nombreUsuario; ?> 👤
            </button>
            <div id="dropdown" class="dropdown">
                <ul>
                    <?php if ($rolUsuario === 'Administrador'): ?>
                        <li><a href="productos.php">Gestionar Productos</a></li>
                        <li><a href="c_clientes.php">Gestionar Clientes</a></li>
                        <li><a href="ventas.php">Ver Ventas</a></li>
                        <li><a href="moni.php">Moni</a></li>
                    <?php else: ?>
                        <li><a href="carrito.php">Carrito de Compras</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


    <!-- Contenido principal -->
    <div class="container">
        <h2>Bienvenido, <?php echo $nombreUsuario; ?>!</h2>
        
        <!-- Contenido Dinámico Según el Rol -->
        <?php if ($rolUsuario === 'Administrador'): ?>
            <p>Este es tu panel de administrador. Aquí puedes gestionar productos, clientes y ventas.</p>
            <!-- Opciones específicas para el Administrador -->
            <ul>
                <li><a href="productos.php">Gestionar Productos</a></li>
                <li><a href="c_clientes.php">Gestionar Clientes</a></li>
                <li><a href="ventas.php">Ver Ventas</a></li>
                <li><a href="moni.php">moni</a></li>
            </ul>
        <?php else: ?>
            <p>Este es tu portal de cliente.</p>
        <?php endif; ?>
    </div>
    <!----------------------------------------------------------------------------------------------------------------------->
    <!-- seccion de productos en venta -->



    <!-- Pie de página -->
    <footer>
        <p>© 2024 Pretty Woman Boutique. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
