<?php
// Inicia la sesión
session_start();

// Destruye todos los datos de la sesión
session_destroy();

// Redirige al usuario al index general
header("Location: index.php");
exit();
?>
