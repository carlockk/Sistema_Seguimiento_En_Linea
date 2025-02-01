<?php

session_start();

require_once 'config.php'; // Incluir la configuración de la base de datos

// Procesamiento del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Intentar autenticación con la base de datos personalizada
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Autenticación exitosa en la base de datos personalizada
        $_SESSION['user_logged_in'] = true;
        header("Location: admin.php"); // Redirige a admin.php
        exit();
    }

    // Si la autenticación falla, muestra mensaje de error
    $error_message = "Nombre de usuario o contraseña incorrectos.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="login.css">
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleButton = document.getElementById('togglePasswordButton');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleButton.textContent = 'Ocultar';
            } else {
                passwordField.type = 'password';
                toggleButton.textContent = 'Mostrar';
            }
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Iniciar Sesión</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="username">Usuario</label>
                    <div class="input-icon">
                        <input id="username" name="username" type="text" placeholder="Ingrese su usuario" required>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A10.97 10.97 0 0112 14a10.97 10.97 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <div class="input-icon">
                        <input id="password" name="password" type="password" placeholder="Ingrese su contraseña" required>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v4m0 0h-3m3 0h3m-6-4v4m6-4v4m-6-4a2 2 0 104 0v-4a4 4 0 00-4-4v0a4 4 0 00-4 4v4zm0 4h-2a2 2 0 00-2 2v2a2 2 0 002 2h16a2 2 0 002-2v-2a2 2 0 00-2-2h-2m-6 0v-4a2 2 0 00-2-2v4" /></svg>
                        <button type="button" id="togglePasswordButton" onclick="togglePassword()">Mostrar</button>
                    </div>
                </div>
                <button type="submit">Iniciar Sesión</button>
                <?php if (!empty($error_message)): ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>
            </form>
            <p class="privacy-policy">Política de privacidad | Términos de uso</p>
        </div>
    </div>
</body>
</html>