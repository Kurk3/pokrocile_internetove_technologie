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

$userAction = filter_input(INPUT_POST, 'userAction', FILTER_SANITIZE_STRING);
$userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
$userName = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_STRING);
$userEmail = filter_input(INPUT_POST, 'userEmail', FILTER_VALIDATE_EMAIL);
$userPassword = $_POST['userPassword'] ? hash('sha512', $_POST['userPassword']) : null;

switch ($userAction) {
    case 'addUser':
        if ($userName && $userEmail && $userPassword) {
            $stmt = $pdo->prepare("INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$userName, $userEmail, $userPassword]);
        }
        break;
    case 'editUser':
        if ($userId && $userName && $userEmail && $userPassword) {
            $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$userName, $userEmail, $userPassword, $userId]);
        }
        break;
    case 'deleteUser':
        if ($userId) {
            $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
            $stmt->execute([$userId]);
        }
        break;
}

$users = $pdo->query("SELECT id, name, email FROM user")->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-xl font-semibold leading-6 text-gray-900">Users</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all the users in your account including their name, title, email, and role.</p>
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="javascript:void(0);" onclick="populateUserForm(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                            <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="userAction" value="deleteUser">
                                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this user?');" class="ml-4 text-red-600 hover:text-red-900">
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
        <h1 class="text-2xl bold font-gray-900">Manage users</h1>
        <input type="hidden" name="userAction" id="userFormAction" value="addUser">
        <input type="hidden" name="userId" id="userId">
        <div>
            <label for="userName" class="block text-sm font-medium text-gray-700">Name:</label>
            <input type="text" name="userName" id="userName" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left">
        </div>
        <div>
            <label for="userEmail" class="block text-sm font-medium text-gray-700">Email:</label>
            <input type of="email" name="userEmail" id="userEmail" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left">
        </div>
        <div>
            <label for="userPassword" class="block text-sm font-medium text-gray-700">Password:</label>
            <input type="password" name="userPassword" id="userPassword" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left">
        </div>
        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Submit User</button>
        </div>
    </form>
</div>

    <script>
    function populateUserForm(user) {
        document.getElementById('userFormAction').value = 'editUser';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userPassword').value = '';
    }

</script>
</body>
</html>
