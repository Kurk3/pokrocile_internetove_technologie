<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

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

$action = isset($_POST['action']) ? $_POST['action'] : '';
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if (!empty($action)) {
    switch ($action) {
        case 'add':
            if ($name && $description && $price) {
                $stmt = $pdo->prepare("INSERT INTO product (name, description, price) VALUES (?, ?, ?)");
                $stmt->execute([$name, $description, $price]);
            }
            break;
        case 'edit':
            if ($id && $name && $description && $price) {
                $stmt = $pdo->prepare("UPDATE product SET name = ?, description = ?, price = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $id]);
            }
            break;
        case 'delete':
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
    }
}

$stmt = $pdo->query("SELECT * FROM product");
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CRUD Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold leading-6 text-gray-900">Products</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all the products in your inventory, including names, descriptions, and prices.</p>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['price']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="javascript:void(0);" onclick="populateForm(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this product?');" class="ml-4 text-red-600 hover:text-red-900">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 max-w-md mx-auto">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6 bg-white shadow px-4 py-5 sm:p-6">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="productId">
            <div>
                <label for="productName" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" name="name" id="productName" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="productDescription" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" id="productDescription" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label for="productPrice" class="block text-sm font-medium text-gray-700">Price:</label>
                <input type="number" name="price" id="productPrice" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Submit Product</button>
            </div>
        </form>
    </div>

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
</html>?>
