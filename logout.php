<?php
session_start(); // Inicia la sesi車n si no se ha hecho antes

// Destruye todas las variables de sesi車n
$_SESSION = array();

// Destruye la sesi車n por completo
session_destroy();

// Redirige al usuario a la p芍gina de login o a cualquier p芍gina de tu elecci車n
header("Location: login.php");
exit();
