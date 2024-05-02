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
    die('Connection failed: ' . $e->getMessage());
}

// Handling the addition to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    // Check if the product exists in the database
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->execute([$productId]);
    if ($stmt->fetch()) {
        $cart = isset($_COOKIE['shopping_cart']) ? json_decode($_COOKIE['shopping_cart'], true) : [];
        $cart[] = $productId; // Always add new item
        setcookie('shopping_cart', json_encode($cart), time() + 86400, '/'); // 1 day expiration
        header('Location: cart.php');
        exit;
    }
}

// Fetch products from the cart if any
$cartProducts = [];
$totalPrice = 0;
$totalPriceVAT = 0;
if (isset($_COOKIE['shopping_cart'])) {
    $cartItems = json_decode($_COOKIE['shopping_cart'], true);
    foreach ($cartItems as $itemId) {
        $stmt = $pdo->prepare("SELECT id, name, price FROM product WHERE id = ?");
        $stmt->execute([$itemId]);
        $product = $stmt->fetch();
        if ($product) {
            $product['price_with_vat'] = $product['price'] * 1.20; // Calculating VAT
            $totalPrice += $product['price'];
            $totalPriceVAT += $product['price_with_vat'];
            $cartProducts[] = $product;
        }
    }
}

// Empty the cart
if (isset($_POST['empty_cart'])) {
    setcookie('shopping_cart', '', time() - 3600, '/'); // Expire the cookie
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
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
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-5 text-center">Your Shopping Cart</h1>
        <form action="cart.php" method="POST" class="text-center mb-4">
            <input type="submit" name="empty_cart" value="Empty Cart" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded cursor-pointer">
        </form>
        <div class="flex justify-center">
            <ul class="bg-white rounded shadow-md p-6 w-full max-w-3xl">
                <?php foreach ($cartProducts as $product): ?>
                    <li class="border-b border-gray-200 last:border-b-0 py-4 flex justify-between items-center">
                        <?php echo htmlspecialchars($product['name']); ?> -
                        <span class="text-gray-600">Price: $<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span>
                        <span class="text-green-600">Price with VAT: $<?php echo htmlspecialchars(number_format($product['price_with_vat'], 2)); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="text-center mt-6">
            <p class="text-xl">Total Price: <span class="font-semibold">$<?php echo htmlspecialchars(number_format($totalPrice, 2)); ?></span></p>
            <p class="text-xl">Total Price with VAT: <span class="font-semibold">$<?php echo htmlspecialchars(number_format($totalPriceVAT, 2)); ?></span></p>
        </div>
    </div>
</body>
</html>
