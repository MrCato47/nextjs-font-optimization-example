<?php
session_start();
require_once 'db.php';

// Security: Function to validate admin status
function validateAdminAccess($conn, $userId) {
    try {
        $stmt = $conn->prepare("SELECT is_admin, username FROM users WHERE id = :user_id AND status = 'active'");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['is_admin'] == 1 ? $user : false;
    } catch (PDOException $e) {
        error_log("Error validating admin access: " . $e->getMessage());
        return false;
    }
}

// Security: Check for session hijacking
if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_destroy();
    header('Location: login.php?error=security');
    exit;
}

// Security: Validate admin session
if (!isset($_SESSION['user_id']) || !validateAdminAccess($conn, $_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Security: Set session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();
$_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];

// Fetch admin user details
try {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: error.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - LinkMex Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-semibold text-gray-800">
                        Panel de Administrador
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($admin['username']); ?>
                    </span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-medium text-gray-900">Usuarios Totales</h3>
                            <div class="mt-1 text-3xl font-semibold text-blue-600">
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
                                    echo $stmt->fetch()['count'];
                                } catch (PDOException $e) {
                                    echo "N/A";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-medium text-gray-900">Pedidos Activos</h3>
                            <div class="mt-1 text-3xl font-semibold text-green-600">
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT COUNT(*) as count FROM cart_items");
                                    echo $stmt->fetch()['count'];
                                } catch (PDOException $e) {
                                    echo "N/A";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-user-shield text-white text-2xl"></i>
                        </div>
                        <div class="ml-5">
                            <h3 class="text-lg font-medium text-gray-900">Usuarios Activos</h3>
                            <div class="mt-1 text-3xl font-semibold text-purple-600">
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
                                    echo $stmt->fetch()['count'];
                                } catch (PDOException $e) {
                                    echo "N/A";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="admin-users.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-user-cog text-2xl text-blue-500 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Gestionar Usuarios</h4>
                            <p class="text-sm text-gray-500">Administrar cuentas y permisos</p>
                        </div>
                    </a>
                    
                    <a href="admin-products.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-box text-2xl text-green-500 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Gestionar Productos</h4>
                            <p class="text-sm text-gray-500">Administrar inventario</p>
                        </div>
                    </a>
                    
                    <a href="admin-orders.php" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-clipboard-list text-2xl text-purple-500 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Gestionar Pedidos</h4>
                            <p class="text-sm text-gray-500">Ver y procesar pedidos</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
