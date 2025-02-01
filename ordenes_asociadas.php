<?php
// Conexión a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Verificar si se recibió el RUT
if (isset($_GET['rut']) && !empty($_GET['rut'])) {
    $rut = $_GET['rut'];

    // Consulta para obtener las órdenes asociadas al RUT
    $query = "SELECT order_number, numero_factura, prod_o_serv, 
                     (SELECT status FROM Order_Status OS WHERE OS.order_id = O.id ORDER BY OS.timestamp DESC LIMIT 1) AS estado,
                     created_at AS fecha
              FROM Orders O
              WHERE rut = :rut";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':rut', $rut, PDO::PARAM_STR);
    $stmt->execute();
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("<p>Error: No se recibió un RUT válido.</p>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes Asociadas</title>
    <!-- Enlace al mismo CSS que usa ver_registros.php -->
    <link rel="stylesheet" href="ver_registros.css?v=1.0">
</head>
<body>
    <div class="table-container table-scroll">
    <?php if (!empty($ordenes)): ?>
        <table>
            <thead>
                <tr>
                    <th>N° Orden</th>
                    <th>N° Factura</th>
                    <th>Producto o Servicio</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($ordenes as $orden): ?>
        <tr>
            <td><?= htmlspecialchars($orden['order_number']) ?></td>
            <td><?= htmlspecialchars($orden['numero_factura']) ?></td>
            <td><?= htmlspecialchars($orden['prod_o_serv']) ?></td>
            <td><?= htmlspecialchars($orden['estado']) ?></td>
            <td>
                <?php 
                $fecha = new DateTime($orden['fecha']);
                echo htmlspecialchars($fecha->format('d/m/Y H:i:s')); 
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>


        </table>
    <?php else: ?>
        <p>No se encontraron órdenes asociadas a este RUT.</p>
    <?php endif; ?>
</div>
</body>
</html>
