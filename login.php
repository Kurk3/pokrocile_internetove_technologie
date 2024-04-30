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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginAction'])) {
    $loginEmail = filter_input(INPUT_POST, 'loginEmail', FILTER_VALIDATE_EMAIL);
    $loginPassword = $_POST['loginPassword'];

    if (!$loginEmail) {
        $login_err = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM user WHERE email = ?");
        $stmt->execute([$loginEmail]);
        $user = $stmt->fetch();

        if ($user && hash('sha512', $loginPassword) === $user['password_hash']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['loggedin'] = true;  // Ensure this session variable is set for logged-in check
            header("Location: product_management.php");
            exit;
        } else {
            $login_err = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>

        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div>
                        <label for="loginEmail" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1">
                            <input id="loginEmail" name="loginEmail" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="loginPassword" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input id="loginPassword" name="loginPassword" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <input type="hidden" name="loginAction" value="login">

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Sign in</button>
                    </div>

                    <?php if(isset($login_err)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $login_err; ?>
                </div>
                     <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
