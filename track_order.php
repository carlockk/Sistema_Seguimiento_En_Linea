<?php
// Activar el reporte de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastrear Pedido</title>
    <link rel="stylesheet" href="track.css">
</head>
<body>
<div style="background-color: #4d4d4d; color: #fff; padding: 4px; border-bottom: solid 3px #000000; height: 18px;">El diseño se puede adaptar a como es tu sitio web</div>
    <!-- Barra superior -->
    <header class="top-bar">
        <div class="logo">El logo es el de tu web</div>
    </header>

    <!-- Contenedor principal con recuadro de color -->
    <div class="container">
        <div class="banner"></div>
        <h2>Puedes rastrear tu envío aquí</h2>
        <p style="color:#666;">Ingresa tus datos de seguimiento y podrás obtener toda la información necesaria de tu paquete</p>

        <!-- Formulario de búsqueda -->
        <form method="post">
            <div class="input-group">
                <input type="text" id="order_number" name="order_number" placeholder="Ingresa tu número de orden" required>
                <button type="submit" class="search-btn" aria-label="Buscar número">
                     <span class="btn-text">Buscar</span>
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none">
                        <circle cx="11" cy="11" r="8" stroke="white" stroke-width="2" fill="none"/>
                        <line x1="16" y1="16" x2="22" y2="22" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </form>



<?php
// Verificar si se ha enviado el formulario de rastreo
if (isset($_POST['order_number'])) {
    $orderNumber = trim($_POST['order_number']);

    try {
        // Consultar la orden en la base de datos
        $stmt = $pdo->prepare("
            SELECT O.order_number, O.current_status, O.nombre, O.apellido, O.prod_o_serv
            FROM Orders O 
            WHERE O.order_number = ?
        ");
        $stmt->execute([$orderNumber]);
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($orderData) {
            $nombreCompleto = htmlspecialchars($orderData['nombre'] . ' ' . $orderData['apellido']);
            $estadoActual = htmlspecialchars($orderData['current_status']);
            $productoServicio = htmlspecialchars($orderData['prod_o_serv']);

            echo "<div class='alert'>";
            echo "<h3>Hola, <strong>$nombreCompleto</strong></h3>";
            echo "<p><a style='color:#666;'>El estado actual de tu pedido número</a> <strong>$orderNumber</strong> es: <strong>$estadoActual</strong>.</p>";
            echo "<p>Producto o servicio solicitado: <strong>$productoServicio</strong></p>";
            echo "</div>";

            // Obtener historial de estados del pedido
            $stmt = $pdo->prepare("SELECT status, timestamp FROM Order_Status WHERE order_id = (SELECT id FROM Orders WHERE order_number = ?) ORDER BY timestamp");
            $stmt->execute([$orderNumber]);
            $statuses = $stmt->fetchAll();

            echo "<h3>Historial de Estado del Pedido:</h3>";

            // Línea de tiempo dinámica de estados del pedido
            echo "<div class='tracking'>";
            foreach ($statuses as $status) {
                $stateClass = ($status['status'] == $estadoActual) ? 'current' : 'completed';
                $formattedDate = date('d-m-Y', strtotime($status['timestamp']));
                echo "<div class='step $stateClass'>
                        <div class='circle'></div>
                        <div class='label'>
                            <span class='status'>Estado: <strong>" . htmlspecialchars($status['status']) . "</strong></span><br>
                            <span class='timestamp'>Con fecha: " . htmlspecialchars($formattedDate) . "</span>
                        </div>
                      </div>";
            }
            echo "</div>";

        } else {
            echo "<div class='alert'>No se encontró ningún pedido con el número <strong>$orderNumber</strong>.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert'>Error al consultar el pedido: " . $e->getMessage() . "</div>";
    }
}
?>

    </div>
</body>
</html>
