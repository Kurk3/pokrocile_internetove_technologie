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

// Fetch all products from the database
$stmt = $pdo->query('SELECT id, name, description FROM product');
$products = $stmt->fetchAll();

// Calculate the number of items in the cart
$cartItemCount = 0;
if (isset($_COOKIE['shopping_cart'])) {
    // Here we are using json_decode to read the JSON encoded cookie into an array
    $cartItems = json_decode($_COOKIE['shopping_cart'], true);
    $cartItemCount = count($cartItems);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <!-- Add your CSS links here -->
</head>
<body>
<div class="main-content">
    <h1>Our Products</h1>
    <div>
        <a href="cart.php">Cart (<?php echo $cartItemCount; ?>)</a>
    </div>
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <h2><a href="product_detail.php?id=<?php echo $product['id']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </a></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
