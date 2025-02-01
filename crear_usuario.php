<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    body{
    background-image: url(dis.png) ;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    margin: 0;
    padding: 0;
    height: 96vh;
}

</style>

</head>
<body class="flex justify-center items-center h-screen bg-gray-100">

    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-3xl font-bold text-indigo-500 mb-4">Crear Usuario</h2>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Usuario</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500"
                    placeholder="Ingrese el usuario"
                    required
                />
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-indigo-500"
                    placeholder="Ingrese la contraseña"
                    required
                />
            </div>

            <button
                type="submit"
                class="w-full p-2 text-white bg-indigo-500 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-500"
            >
                Crear Usuario
            </button>
        </form>

        <?php
        // Conexión a la base de datos
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=coffeewa_seg', 'coffeewa_seg', 'Irios.,._1A');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error en la conexión a la base de datos: " . $e->getMessage());
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibir y sanitizar los datos del formulario
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // Encriptar la contraseña
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insertar usuario en la base de datos
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (:username, :password)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashedPassword);
                
                if ($stmt->execute()) {
                    echo "<p class='text-green-500 mt-4'>Usuario creado con éxito.</p>";
                } else {
                    echo "<p class='text-red-500 mt-4'>Error al crear el usuario.</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='text-red-500 mt-4'>Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>

</body>
</html>
