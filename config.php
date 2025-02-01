<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>