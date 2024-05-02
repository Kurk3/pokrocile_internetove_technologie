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

// Get the product ID from the query string
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the product from the database
$stmt = $pdo->prepare('SELECT * FROM product WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

// If the product doesn't exist, redirect to main page or show an error
if (!$product) {
    header('Location: index.php');
    exit;
}

// Calculate price with VAT
$priceWithVAT = $product['price'] * 1.20;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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
<div class="container mx-auto px-4 py-6">
    <h1 class="text-xl font-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p>Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
    <p>Price with VAT: $<?php echo htmlspecialchars(number_format($priceWithVAT, 2)); ?></p>

    <!-- Form for adding product to cart -->
    <form action="cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add to Cart
        </button>
    </form>
</div>
</body>
</html>
