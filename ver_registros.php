<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}


// Obtener registros de clientes
$query = "SELECT DISTINCT rut, nombre, apellido FROM Orders";
$stmt = $pdo->prepare($query);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Clientes</title>
    <link rel="stylesheet" href="ver_registros.css?v=1.0">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 20px;
        }

        .popup table {
            width: 100%;
            border-collapse: collapse;
        }

        .popup table th, .popup table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .popup-close {
            display: block;
            text-align: right;
            margin-bottom: 10px;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
<div style="background-color: #2a97e0; color: #fff; padding: 4px; border-bottom: solid 3px #1f75ae;">
    Ver registros
    <a style="text-align: right; float: right; padding-right:10px; text-decoration: none; color:#fff;" href="logout.php" onclick="return confirmarCerrarSesion();">Cerrar sesión</a>
</div>

<!-- Buscador -->
<div class="search-bar">
    <input type="text" id="busqueda" placeholder="Buscar por RUT, Nombre o Apellido..." onkeyup="filtrarResultados()">
    <button class="boton_actualizar" onclick="window.location.reload();">Actualizar</button>
</div>

<div class="table-container">
    <table id="tablaClientes">
        <thead>
            <tr>
                <th>RUT</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Órdenes</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?php echo htmlspecialchars($cliente['rut']); ?></td>
                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                <td><?php echo htmlspecialchars($cliente['apellido']); ?></td>
                <td>
                    <button class="boton" onclick="verOrdenes('<?php echo $cliente['rut']; ?>')">Ver Órdenes</button>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="popup-overlay" id="popupOverlay"></div>
<div class="popup" id="popup">
    <a href="#" class="popup-close" onclick="cerrarPopup()">Cerrar</a>
    <div id="popupContent"></div>
</div>

<script>
    // Filtrado en tiempo real
    function filtrarResultados() {
        const input = document.getElementById('busqueda');
        const filtro = input.value.toLowerCase();
        const tabla = document.getElementById('tablaClientes');
        const filas = tabla.getElementsByTagName('tr');

        for (let i = 1; i < filas.length; i++) { // Omitir encabezado
            const celdas = filas[i].getElementsByTagName('td');
            let mostrarFila = false;

            for (let j = 0; j < celdas.length; j++) {
                if (celdas[j]) {
                    const texto = celdas[j].textContent || celdas[j].innerText;
                    if (texto.toLowerCase().indexOf(filtro) > -1) {
                        mostrarFila = true;
                        break;
                    }
                }
            }

            filas[i].style.display = mostrarFila ? '' : 'none';
        }
    }

    function verOrdenes(rut) {
        const popup = document.getElementById('popup');
        const overlay = document.getElementById('popupOverlay');
        const popupContent = document.getElementById('popupContent');

        overlay.style.display = 'block';
        popup.style.display = 'block';

        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'ordenes_asociadas.php?rut=' + rut, true);
        xhr.onload = function() {
            if (this.status === 200) {
                popupContent.innerHTML = this.responseText;
            } else {
                popupContent.innerHTML = '<p>Error al cargar las órdenes.</p>';
            }
        };
        xhr.send();
    }

    function cerrarPopup() {
        const popup = document.getElementById('popup');
        const overlay = document.getElementById('popupOverlay');

        popup.style.display = 'none';
        overlay.style.display = 'none';
    }
    
    function confirmarCerrarSesion() {
    return confirm("¿Estás seguro de que deseas cerrar sesión?");
}
    
</script>
</body>
</html>
