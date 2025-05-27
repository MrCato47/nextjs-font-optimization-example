<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add') {
    $productId = intval($_GET['id']);
    // Add product to cart in session or DB
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
    echo json_encode(['message' => 'Producto agregado al carrito']);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'count') {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $count += $qty;
        }
    }
    echo json_encode(['count' => $count]);
    exit;
}

// Display cart page
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <nav>
        <ul class="navbar">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="cart.php">Carrito</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>

    <main>
        <h1>Carrito de Compras</h1>
        <div id="cart-items">
            <?php
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "<p>El carrito está vacío.</p>";
            } else {
                echo "<ul>";
                foreach ($_SESSION['cart'] as $productId => $qty) {
                    echo "<li>Producto ID: $productId - Cantidad: $qty</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </main>
</body>
</html>
