<?php
session_start();

$host = '127.0.0.1';
$db = 'ecom_db';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';
$port = '8889';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$stmt = $pdo->query('SELECT id, name, description, price FROM product');
$products = $stmt->fetchAll();

$cartItemCount = 0;
if (isset($_COOKIE['shopping_cart'])) {
    $cartItems = json_decode($_COOKIE['shopping_cart'], true);
    $cartItemCount = count($cartItems);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="main-content">
    <nav class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16">
                <div class="flex items-center px-2 lg:px-0">
                    <div class="hidden lg:block lg:ml-6">
                        <div class="flex space-x-4">
                            <a href="index.php" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium">Home</a>
                            <a href="product_management.php" class="text-gray-800 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Product Management</a>
                            <a href="user_management.php" class="text-gray-800 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">User Management</a>
                            <a href="cart.php" class="text-gray-800 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Cart (<?php echo $cartItemCount; ?>)</a>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <div class="lg:hidden">
                    <button type="button" class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="bg-white">
        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <h2 class="text-4xl my-8">List of Items</h2>
            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-10 lg:grid-cols-3 lg:gap-x-8">
                <?php foreach ($products as $product): ?>
                <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white hover:bg-gray-100">
                    <div class="aspect-h-4 aspect-w-3 bg-gray-200 sm:aspect-none group-hover:opacity-75 sm:h-96">
                        <img src="insert_image_here.png" alt="<?php echo htmlspecialchars($product['description']); ?>" class="h-full w-full object-cover object-center sm:h-full sm:w-full">
                    </div>
                    <div class="flex flex-1 flex-col space-y-2 p-4">
                        <h3 class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="flex flex-1 flex-col justify-end">
                            <p class="text-base font-medium text-black">$<?php echo htmlspecialchars($product['price']); ?></p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
