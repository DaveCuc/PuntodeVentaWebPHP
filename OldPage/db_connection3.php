<?php
// Datos de conexión a la base de datos (ajusta según tu configuración)
$servidor = "localhost";
$usuarioBD = "root";
$contraseñaBD = "";
$nombreBD = "newtienda";

// Crear conexión
$conn = new mysqli($servidor, $usuarioBD, $contraseñaBD, $nombreBD);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión a la base de datos: " . $conn->connect_error);
}

// Configurar el juego de caracteres
$conn->set_charset("utf8");

?>
