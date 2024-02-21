<?php
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

// CRUD Operations
$action = isset($_POST['action']) ? $_POST['action'] : '';
$id = isset($_POST['id']) ? $_POST['id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '';

if ($action == 'add') {
    $stmt = $pdo->prepare("INSERT INTO product (name, description, price) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $price]);
} elseif ($action == 'edit' && $id) {
    $stmt = $pdo->prepare("UPDATE product SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $id]);
} elseif ($action == 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
    $stmt->execute([$id]);
}

// Fetch products from the database
$stmt = $pdo->query("SELECT * FROM product");
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">tot
<head>
    <meta charset="UTF-8">
    <title>Admin - CRUD Products</title>
</head>
<body>
<h2>Product List</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Actions
        </th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['id']); ?></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['description']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                </form>
                <a href="#edit" onclick='populateForm(<?php echo json_encode($product); ?>)'>Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<h2>Add/Edit Product</h2>
<form method="POST">
    <input type="hidden" name="action" id="formAction" value="add">
    <input type="hidden" name="id" id="productId">
    <label for="productName">Name:</label>
    <input type="text" name="name" id="productName" required><br>
    <label for="productDescription">Description:</label>
    <textarea name="description" id="productDescription" required></textarea><br>
    <label for="productPrice">Price:</label>
    <input type="number" step="0.01" name="price" id="productPrice" required><br>
    <input type="submit" value="Submit">
</form>

<script>
    function populateForm(product) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productDescription').value = product.description;
        document.getElementById('productPrice').value = product.price;
    }
</script>
</body>
</html>