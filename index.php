<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tienda Ugreen</title>
    <link rel="stylesheet" href="styles.css" />
    <script>
        async function fetchProducts() {
            const response = await fetch('api.php');
            const products = await response.json();
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            if (products.message) {
                container.innerHTML = '<p>Error: ' + products.message + '</p>';
                return;
            }
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.innerHTML = `
                    <h3>${product.nombre}</h3>
                    <p>Precio: $${product.precio}</p>
                    <button onclick="addToCart(${product.id})">Agregar al carrito</button>
                `;
                container.appendChild(productCard);
            });
        }

        async function addToCart(productId) {
            const response = await fetch('cart.php?action=add&id=' + productId, { method: 'POST' });
            const result = await response.json();
            alert(result.message);
            updateCartCount();
        }

        async function updateCartCount() {
            const response = await fetch('cart.php?action=count');
            const result = await response.json();
            document.getElementById('cart-count').textContent = result.count;
        }

        window.onload = () => {
            fetchProducts();
            updateCartCount();
        };
    </script>
</head>
<body>
    <nav>
        <ul class="navbar">
            <li><a href="index.php">Inicio</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="cart.php">Carrito (<span id="cart-count">0</span>)</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h1>Productos Ugreen</h1>
        <div id="products-container" class="products-grid">
            <!-- Productos cargados aquí -->
        </div>
    </main>
</body>
</html>
