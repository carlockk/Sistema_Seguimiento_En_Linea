<?php
// Conexión a la base de datos
$pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$rut = $_GET['rut'] ?? '';

$stmt = $pdo->prepare("SELECT nombre, apellido FROM Orders WHERE rut = ?");
$stmt->execute([$rut]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['exists' => true, 'nombre' => $user['nombre'], 'apellido' => $user['apellido']]);
} else {
    echo json_encode(['exists' => false]);
}
?>
