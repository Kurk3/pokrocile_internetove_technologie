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

<div class="bg-white">
    <div class="pb-16 pt-6 sm:pb-24">
        <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="lg:grid lg:auto-rows-min lg:grid-cols-12 lg:gap-x-8">
                <div class="lg:col-span-5 lg:col-start-8">
                    <div class="flex justify-between">
                        <h1 class="text-xl font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="text-xl font-medium text-gray-900">$<?php echo htmlspecialchars(number_format($priceWithVAT, 2)); ?></p>
                    </div>
                </div>

                <div class="mt-8 lg:col-span-7 lg:col-start-1 lg:row-span-3 lg:row-start-1 lg:mt-0">
                    <h2 class="sr-only">Images</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 lg:grid-rows-3 lg:gap-8">
                        <img src="insert_image_here.png"  class="lg:col-span-2 lg:row-span-2 rounded-lg">
                    </div>
                </div>

                <div class="mt-8 lg:col-span-5">
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit"  class="mt-8 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Add to card</button>
                    </form>

                    <div class="mt-10">
                        <h2 class="text-sm font-medium text-gray-900">Description</h2>

                        <div class="prose prose-sm mt-4 text-gray-500">
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
<script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
