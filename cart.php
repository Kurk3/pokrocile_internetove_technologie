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

// If a product ID is posted, add it to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];

    // Retrieve the current cart from the cookie
    $cart = isset($_COOKIE['shopping_cart']) ? json_decode($_COOKIE['shopping_cart'], true) : [];

    // Add the product to the cart if it's not already in there
    if (!in_array($productId, $cart)) {
        $cart[] = $productId;
    }

    // Save the updated cart back to the cookie
    setcookie('shopping_cart', json_encode($cart), time() + 3600 * 24 * 30, '/'); // 30-day cookie
    header('Location: cart.php');
    exit;
}

// Retrieve products from the cart
$cartItems = isset($_COOKIE['shopping_cart']) ? json_decode($_COOKIE['shopping_cart'], true) : [];
$cartProducts = [];
$totalPrice = 0;

foreach ($cartItems as $itemId) {
    $stmt = $pdo->prepare("SELECT id, name, price FROM product WHERE id = ?");
    $stmt->execute([$itemId]);
    $product = $stmt->fetch();

    if ($product) {
        $product['price_with_vat'] = $product['price'] * 1.20;
        $totalPrice += $product['price_with_vat'];
        $cartProducts[] = $product;
    }
}

// Empty the cart
if (isset($_POST['empty_cart'])) {
    setcookie('shopping_cart', '', time() - 3600, '/');
    header('Location: cart.php');
    exit;
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Shopping Cart</title>
        <!-- Add your CSS links here -->
    </head>
<body>
    <h1>Your Shopping Cart</h1>
    <form action="cart.php" method="POST">
        <input type="submit" name="empty_cart" value="Empty Cart">
    </form>
    <ul>
        <?php foreach ($cartProducts as $product): ?>
            <li>
                <?php echo htmlspecialchars($product['name']); ?> -
                <?php echo htmlspecialchars(number_format($product['price'], 2)); ?> + VAT:
                <?php echo htmlspecialchars(number_format($product['price_with_vat'], 2)); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <p>Total Price with VAT: <?php echo htmlspecialchars(number_format); ?>
