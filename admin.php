<?php
session_start();

// Conexión a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Función para generar un número de orden único
function generateOrderNumber() {
    return rand(1000000000, 9999999999);
}

// Función para cerrar sesión y redirigir a login.php
function cerrar_sesion_y_redirigir() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Verificar si el usuario solicitó cerrar sesión
if (isset($_GET['logout'])) {
    cerrar_sesion_y_redirigir();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Crear un nuevo estado
if (isset($_POST['create_status'])) {
    $newStatus = $_POST['new_status'];

    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO Order_Status (status) VALUES (?)");
        $stmt->execute([$newStatus]);

        // Guardar mensaje de éxito en la sesión
        $_SESSION['message'] = "Estado '$newStatus' creado exitosamente.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        // Guardar mensaje de error en la sesión
        $_SESSION['message'] = "Error al crear estado: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_POST['create_order'])) {
    $orderNumber = generateOrderNumber();
    $initialStatus = $_POST['initial_status'];
    $rutNumero = $_POST['rut_numero'];
    $rutDv = $_POST['rut_dv'];
    $rut = trim($rutNumero) . '-' . trim($rutDv);
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $numeroFactura = $_POST['numero_factura'];
    $prodOServ = $_POST['prod_o_serv'];

    // Verificar si el RUT ya existe en la base de datos
    $stmt = $pdo->prepare("SELECT nombre, apellido FROM Orders WHERE rut = ?");
    $stmt->execute([$rut]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Si el RUT existe, cargar nombre y apellido
        $nombre = $user['nombre'];
        $apellido = $user['apellido'];
    }

   try {
    // Crear un nuevo pedido
    if (empty($rutNumero) || empty($rutDv)) {
        throw new Exception("El campo RUT no puede estar vacío.");
    }

    $stmt = $pdo->prepare("INSERT INTO Orders (order_number, rut, nombre, apellido, numero_factura, prod_o_serv, current_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$orderNumber, $rut, $nombre, $apellido, $numeroFactura, $prodOServ, $initialStatus]);

    $orderId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("INSERT INTO Order_Status (order_id, status, timestamp) VALUES (?, ?, NOW())");
    $stmt->execute([$orderId, $initialStatus]);

    // Mensaje de éxito en la sesión
    $_SESSION['message'] = "Pedido creado con número: $orderNumber, estado inicial: $initialStatus, y producto/servicio: $prodOServ";
    $_SESSION['order_details'] = [
        'order_number' => $orderNumber,
        'initial_status' => $initialStatus,
        'prod_o_serv' => $prodOServ
    ];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();  // Detener ejecución para evitar duplicados
} catch (Exception $e) {
    // Mensaje de error en la sesión
    $_SESSION['message'] = "Error al crear pedido: " . $e->getMessage();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
}


// Actualizar el estado de un pedido existente
if (isset($_POST['update_status'])) {
    $orderNumber = $_POST['order_number'];
    $newStatus = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE Orders SET current_status = ? WHERE order_number = ?");
        $stmt->execute([$newStatus, $orderNumber]);

        $stmt = $pdo->prepare("INSERT INTO Order_Status (order_id, status, timestamp) VALUES ((SELECT id FROM Orders WHERE order_number = ?), ?, NOW())");
        $stmt->execute([$orderNumber, $newStatus]);

        // Guardar mensaje en la sesión
        $_SESSION['message'] = "Estado actualizado a '$newStatus' para el pedido número $orderNumber.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        // Guardar mensaje de error en la sesión
        $_SESSION['message'] = "Error al actualizar el estado: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}


// Obtener lista de estados existentes
$statuses = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT status FROM Order_Status");
    $statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "<div class='alert'>Error al obtener estados: " . $e->getMessage() . "</div>";
}

// Obtener lista de números de pedido existentes
$orderNumbers = [];
try {
    $stmt = $pdo->query("SELECT order_number FROM Orders");
    $orderNumbers = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "<div class='alert'>Error al obtener números de pedido: " . $e->getMessage() . "</div>";
}

// Búsqueda de pedidos por RUT o nombre
if (isset($_GET['search_query'])) {
    $searchQuery = $_GET['search_query'];
    $stmt = $pdo->prepare("SELECT order_number, rut, nombre, apellido, numero_factura FROM Orders WHERE rut LIKE ? OR nombre LIKE ?");
    $stmt->execute(["%$searchQuery%", "%$searchQuery%"]);
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($searchResults);
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área del Administrador</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        
//VALIDAR RUT

 // Función para validar el RUT chileno
function validarRut() {
    const rutNumero = document.getElementById("rut_numero").value;
    const rutDv = document.getElementById("rut_dv").value.toUpperCase(); // Convertimos a mayúsculas para verificar con "K"
    const formulario = document.querySelector("form");
    const campos = Array.from(formulario.elements).filter(
        campo => campo.id !== "rut_numero" && campo.id !== "rut_dv"
    );

    // Validación del formato básico (solo números en el campo del RUT)
    if (!/^\d+$/.test(rutNumero)) {
        alert("El RUT debe contener solo números en la primera parte.");
        bloquearCampos(campos, true);
        return false;
    }

    // Cálculo del dígito verificador
    let suma = 0;
    let multiplicador = 2;

    for (let i = rutNumero.length - 1; i >= 0; i--) {
        suma += parseInt(rutNumero[i]) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }

    const dvCalculado = 11 - (suma % 11);
    const dvEsperado = dvCalculado === 11 ? "0" : dvCalculado === 10 ? "K" : dvCalculado.toString();

    // Verifica si el dígito verificador ingresado coincide con el esperado
    if (rutDv === dvEsperado) {
        alert("El RUT ingresado es válido.");
        bloquearCampos(campos, false);

        // Realizar una solicitud AJAX para verificar si el RUT existe
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "verificar_rut.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.existe) {
                    document.getElementById("nombre").value = response.nombre;
                    document.getElementById("apellido").value = response.apellido;
                    alert("RUT encontrado. Nombre: " + response.nombre + ", Apellido: " + response.apellido);
                } else {
                    alert("El RUT no existe en la base de datos.");
                }
            }
        };
        xhr.send("rut_numero=" + rutNumero + "&rut_dv=" + rutDv);
        return true;
    } else {
        alert("El RUT ingresado no es válido.");
        bloquearCampos(campos, true);
        return false;
    }
}

// Función para habilitar o deshabilitar los campos bloqueados
function bloquearCampos(campos, bloquear) {
    campos.forEach(campo => {
        campo.disabled = bloquear;
    });
}

// Deshabilitar los campos al cargar la página
window.onload = function() {
    const formulario = document.querySelector("form");
    const campos = Array.from(formulario.elements).filter(
        campo => campo.id !== "rut_numero" && campo.id !== "rut_dv"
    );
    bloquearCampos(campos, true);
};


// Escuchar el evento blur en el campo de dígito verificador para validar al salir del campo
document.addEventListener("DOMContentLoaded", function() {
    const rutDvField = document.getElementById("rut_dv");

    rutDvField.addEventListener("blur", function() {
        validarRut(); // Ejecuta la validación al salir del campo
    });
});


        
         function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        }

        // Mantener activa la primera sección al cargar
        document.addEventListener('DOMContentLoaded', () => {
            showSection('create-order');
        });
        
    // Función para alternar la visibilidad del menú deslizante
function toggleMenu() {
    const slidingMenu = document.getElementById('slidingMenu');
    slidingMenu.classList.toggle('visible');
}

// Detectar clics fuera del área del menú para cerrarlo
document.addEventListener('click', function (event) {
    const slidingMenu = document.getElementById('slidingMenu');
    const hamburgerButton = document.querySelector('.hamburger');

    // Verifica si el clic fue fuera del menú y del botón de hamburguesa
    if (
        slidingMenu.classList.contains('visible') && 
        !slidingMenu.contains(event.target) && 
        event.target !== hamburgerButton
    ) {
        slidingMenu.classList.remove('visible');
    }
});

function confirmarCerrarSesion() {
    return confirm("¿Estás seguro de que deseas cerrar sesión?");
}

        
    </script>
</head>
<body>
    <div style="background-color: #2a97e0; color: #fff; padding:4px; font-zise:10px; border-bottom: solid 3px #1f75ae;">Binenvenido Administrador:
                <!-- Botón de cerrar sesión -->
                <a style="text-align: right; float: right; padding-right:10px; text-decoration: none; color:#fff;" href="logout.php" 
   onclick="return confirmarCerrarSesion();">Cerrar sesión</a>
        </div>

    
<!-- Barra de navegación -->
<nav>
    <div class="logo"></div>
    <div class="hamburger" onclick="toggleMenu()">&#9776;</div> <!-- Icono de tres líneas -->
    <div class="button-group desktop-menu">
        <a class="btn-crear-pedido" onclick="showSection('create-order')">Crear Pedido</a>
        <a class="btn-actualizar-estado" onclick="showSection('update-order')">Actualizar Estado</a>
        <a class="btn-crear-estados" onclick="showSection('create-status')">Crear Estado</a>
        <a class="btn-registros-pedidos" onclick="window.open('ver_registros.php', '_blank')">Ver Registros</a>
        <a class="btn-seguimiento" onclick="window.open('track_order.php', '_blank')">Ir al seguimiento</a>
        <a class="btn-seguimiento" onclick="window.open('crear_usuario.php', '_blank')">Crear usuario</a>
        <!-- Nuevo botón añadido después de "Ir al seguimiento" -->
        <!--<a class="btn-seguimiento" onclick="window.open('ordenar_estados.php', '_blank')">Ordenar Estados</a>-->

    </div>
</nav>

<!-- Menú deslizante para móviles -->
<div class="sliding-menu" id="slidingMenu">
    
    <div class="button-group">
       
        <button class="btn-crear-pedido2" onclick="showSection('create-order')">Crear Pedido</button>
        <button class="btn-actualizar-estado2" onclick="showSection('update-order')">Actualizar Estado</button>
        <button class="btn-crear-estados2" onclick="showSection('create-status')">Crear Estado</button>
        <button class="btn-registros-pedidos2" onclick="window.open('ver_registros.php', '_blank')">Ver Registros</button>
        <button class="btn-seguimiento2" onclick="window.open('track_order.php', '_blank')">Ver seguimiento</button>
        <button class="btn-actualizar-estado2" onclick="window.open('crear_usuario.php', '_blank')">Crear usuario</button>
        <!-- Nuevo botón añadido después de "Ir al seguimiento" -->
        <!--<button class="btn-actualizar-estado2" onclick="window.open('ordenar_estados.php', '_blank')">Ordenar Estados</button>-->

    </div>
</div>

<!--CREAR NUEVOS PEDIDOS-->
<div class="container">
    <div id="create-order" class="section">
  
        <form method="post" class="form-grid2">
            <!-- Primera columna -->
            <div class="form-column">
                <h3><img src="crear.png" width="45" height="40" style="vertical-align: middle; margin-right: 10px;">Crear Nuevo Pedido</h3>
                <div class="rut-container">
                    <label for="rut_numero">RUT:</label>
                    <div class="rut-inputs">
                        <input type="text" name="rut_numero" id="rut_numero" placeholder="12345678" required>
                        <span>-</span>
                        <input type="text" name="rut_dv" id="rut_dv" placeholder="DV" required>
                    </div>
                </div>
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>

                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" required>
            </div>

            <!-- Segunda columna -->
            <div class="form-column2">
                <label for="numero_factura">Número de Factura:</label>
                <input type="text" name="numero_factura" id="numero_factura" required>

                <label for="prod_o_serv">Nuevo Producto o Servicio:</label>
                <input type="text" name="prod_o_serv" id="prod_o_serv" required>

                <label for="initial_status">Estado Inicial:</label>
                <select name="initial_status" id="initial_status" required>
                    <?php foreach ($statuses as $status) : ?>
                        <option value="<?php echo htmlspecialchars($status); ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botón en la parte inferior -->
            <div class="form-button">
                <button type="submit" name="create_order">Crear Pedido</button>
            </div>
        </form>
    </div>
</div>


<script>
document.getElementById('rut_dv').addEventListener('blur', function () {
    const rutNumero = document.getElementById('rut_numero').value;
    const rutDv = document.getElementById('rut_dv').value;

    if (rutNumero && rutDv) {
        fetch('check_rut.php?rut=' + encodeURIComponent(rutNumero + '-' + rutDv))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    const confirmLoad = confirm(
                        "El RUT ya existe. ¿Quieres cargar el nombre y apellido?"
                    );
                    if (confirmLoad) {
                        document.getElementById('nombre').value = data.nombre;
                        document.getElementById('apellido').value = data.apellido;
                    }
                }
            })
            .catch(error => console.error('Error al verificar el RUT:', error));
    }
});
</script>


<?php if (isset($_SESSION['message'])): ?>
    <script>
        // Mostrar el mensaje como alert en JavaScript
        alert("<?php echo $_SESSION['message']; ?>");

        // Redirigir o refrescar la página después del alert
        setTimeout(function() {
            window.location.reload();  // Refrescar la página
        }, 2000);  // Esperar 2 segundos antes de refrescar
    </script>
    <?php unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo ?>
<?php endif; ?>

<?php if (isset($_SESSION['order_details'])): ?>
    <script>
        // Opcional: Mostrar los detalles del pedido recién creado
        console.log("Detalles del pedido:", <?php echo json_encode($_SESSION['order_details']); ?>);
    </script>
    <?php unset($_SESSION['order_details']); // Limpiar detalles después de usarlos ?>
<?php endif; ?>


<!--ACTUALIZAR ESTADO DEL PEDIDO-->
<div id="update-order" class="section">
    <form method="post" class="form-grid2">
        <div class="form-column2">
            <h3>
                <img src="actualizar.png" width="45" height="40" style="vertical-align: middle; margin-right: 10px;">
                Actualizar Estado de Pedido
            </h3>
            <label>Número de Pedido:</label>
            <select name="order_number" required>
                <?php foreach ($orderNumbers as $orderNumber) : ?>
                    <option value="<?php echo htmlspecialchars($orderNumber); ?>">
                        <?php echo htmlspecialchars($orderNumber); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-column">
            <label>Nuevo Estado:</label>
            <select name="status" required>
                <?php foreach ($statuses as $status) : ?>
                    <option value="<?php echo htmlspecialchars($status); ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-button">
            <button type="submit" name="update_status">Actualizar Estado</button>
        </div>
    </form>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <script>
        // Mostrar el mensaje como un alert
        alert("<?php echo $_SESSION['message']; ?>");

        // Recargar la página después de mostrar el mensaje
        window.location.reload();
    </script>
    <?php unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo ?>
<?php endif; ?>


        
<!--CREAR NUEVO ESTADO DE PEDIDO-->        
        <div id="create-status" class="section">
            <form method="post" class="form-grid">
                <div class="form-column2">
<h3><img src="new.png" width="45" height="40" style="vertical-align: middle; margin-right: 10px;">Crear Nuevo Estado</h3>
                <label>Nombre del Estado:</label>
                <input type="text" name="new_status" required></div>
                <div class="form-button"><button type="submit" name="create_status">Crear Estado</button></div>
            </form>
        </div></div>
        
<?php if (isset($_SESSION['message'])): ?>
    <script>
        // Mostrar el mensaje como un alert
        alert("<?php echo $_SESSION['message']; ?>");

        // Recargar la página después de mostrar el mensaje
        window.location.reload();
    </script>
    <?php unset($_SESSION['message']); // Limpiar el mensaje después de mostrarlo ?>
<?php endif; ?>


</body>
</html>
