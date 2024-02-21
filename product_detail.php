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
    <!-- Add your CSS links here -->
</head>
<body>
<div class="product-detail">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <p><?php echo htmlspecialchars($product['description']); ?></p>
    <p>Price: <?php echo htmlspecialchars(number_format($product['price'], 2)); ?> (Excl. VAT)</p>
    <p>Price with VAT: <?php echo htmlspecialchars(number_format($priceWithVAT, 2)); ?></p>
    <form action="cart.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="submit" value="Add to Cart">
    </form>
</div>
</body>
</html>
