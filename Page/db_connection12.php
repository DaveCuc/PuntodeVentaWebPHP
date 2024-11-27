<?php
// Datos de conexión a la base de datos (ajusta según tu configuración)
$servidor = "sql100.infinityfree.com";
$usuarioBD = "if0_37791634";
$contraseñaBD = "NElXlca6lq0owlF";
$nombreBD = "if0_37791634_newtienda";

// Crear conexión
$conn = new mysqli($servidor, $usuarioBD, $contraseñaBD, $nombreBD);

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión a la base de datos: " . $conn->connect_error);
}

// Configurar el juego de caracteres
$conn->set_charset("utf8");

?>
