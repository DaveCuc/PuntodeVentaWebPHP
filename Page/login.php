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
                // Iniciar sesión y guardar datos del usuario
                session_start();
                $_SESSION['idUsuario'] = $usuario['idUsuario'];
                $_SESSION['nombreUsuario'] = $usuario['NombreUsuario'];
                $_SESSION['rol'] = $usuario['Rol'];

                // Redirigir al home general
                header("Location: home.php"); // Todos los usuarios van a `home.php`
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
    <link rel="stylesheet" href="css/styles.css"?v=<?php echo time(); ?>">"
    <script src="../src/scripts.js" defer></script>
</head>
<body>
    <div class="navbar">
        <a href="home.php">
            <h1>PRETTY WOMAN Boutique</h1>
        </a>
    </div>

    <table border="0" style="width: 100%; border-collapse: collapse;">
    <tr>
        <!-- Columna para el slideshow -->
        <td style="width: 65%; vertical-align: top;">
            <div class="container">
                <div id="mi_estilo" style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">
                    Bienvenido a mi Página Web
                </div>

                <!-- Slideshow -->
                <div class="slideshow-container" style="position: relative; max-width: 100%; margin: auto;">
                    <!-- Slides -->
                    <div class="mySlides fade">
                        <div class="numbertext">1 / 3</div>
                        <img src="image/1.jpg" style="width: 40%; height: auto; display: block; margin: auto;">
                        <div class="text">TCNM Veracruz</div>
                    </div>

                    <div class="mySlides fade">
                        <div class="numbertext">2 / 3</div>
                        <img src="image/2.jpg" style="width: 40%; height: auto; display: block; margin: auto;">
                        <div class="text">☝🏻</div>
                    </div>

                    <div class="mySlides fade">
                        <div class="numbertext">3 / 3</div>
                        <img src="image/3.jpg" style="width: 40%; height: auto; display: block; margin: auto;">
                        <div class="text">ING. SCP</div>
                    </div>

                    <!-- Navigation buttons -->
                    <a class="prev" onclick="plusSlides(-1)" style="left: 0;">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)" style="right: 0;">&#10095;</a>
                </div>

                <!-- Dots -->
                <div style="text-align:center; margin-top: 10px;">
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                    <span class="dot" onclick="currentSlide(3)"></span>
                </div>

                <!-- JavaScript -->
                <script>
                    let slideIndex = 1;
                    showSlides(slideIndex);

                    function plusSlides(n) {
                        showSlides(slideIndex += n);
                    }

                    function currentSlide(n) {
                        showSlides(slideIndex = n);
                    }

                    function showSlides(n) {
                        let i;
                        let slides = document.getElementsByClassName("mySlides");
                        let dots = document.getElementsByClassName("dot");
                        if (n > slides.length) { slideIndex = 1; }
                        if (n < 1) { slideIndex = slides.length; }
                        for (i = 0; i < slides.length; i++) {
                            slides[i].style.display = "none";
                        }
                        for (i = 0; i < dots.length; i++) {
                            dots[i].className = dots[i].className.replace(" active", "");
                        }
                        slides[slideIndex - 1].style.display = "block";
                        dots[slideIndex - 1].className += " active";
                    }
                </script>
            </div>
        </td>

        <td>
            <div class="container">
            <div class="login-box" style="max-width: 300px; margin: auto; padding: 20px;">
                <h2>Inicio de sesión</h2>
                <form method="POST" action="">
                    <input type="text" name="nombre_usuario" placeholder="Usuario" required>
                    <input type="password" name="contraseña" placeholder="Contraseña" required>
                    <button type="submit">Ingresar</button>
                </form>
                <a href="sign_in.php">Crear cuenta</a>
                <div class="result">
                    <?php echo $mensaje; ?>
                </div>
            </div>
        </div>
        </td>
      </table>

</body>
</html>
