<?php
session_start();
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $message = 'Contraseña incorrecta.';
        }
    } else {
        $message = 'Usuario no encontrado.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav>
        <ul class="navbar">
            <li><a href="index.php">Inicio</a></li>
        </ul>
    </nav>

    <main>
        <h1>Iniciar Sesión</h1>
        <?php if ($message): ?>
            <p class="error-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php" class="login-form">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required />
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required />
            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
