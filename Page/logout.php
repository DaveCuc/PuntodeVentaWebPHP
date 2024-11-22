<?php
// Inicia la sesión
session_start();

// Destruye todos los datos de la sesión
session_destroy();

// Redirige al usuario al home general
header("Location: home.php");
exit();
?>
