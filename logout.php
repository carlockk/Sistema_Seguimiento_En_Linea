<?php
session_start(); // Inicia la sesi��n si no se ha hecho antes

// Destruye todas las variables de sesi��n
$_SESSION = array();

// Destruye la sesi��n por completo
session_destroy();

// Redirige al usuario a la p��gina de login o a cualquier p��gina de tu elecci��n
header("Location: login.php");
exit();
