<?php
// Conexión a la base de datos
$pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$rutNumero = $_GET['rut_numero'] ?? '';

$stmt = $pdo->prepare("SELECT rut, nombre, apellido FROM Orders WHERE rut LIKE ? LIMIT 1");
$stmt->execute(["$rutNumero%"]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    list($rut_num, $rut_dv) = explode('-', $user['rut']);
    echo json_encode(['exists' => true, 'rut_dv' => $rut_dv, 'nombre' => $user['nombre'], 'apellido' => $user['apellido']]);
} else {
    echo json_encode(['exists' => false]);
}
?>
