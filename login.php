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

session_start(); // Start the session at the beginning of the script

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginAction'])) {
    $loginEmail = isset($_POST['loginEmail']) ? $_POST['loginEmail'] : '';
    $loginPassword = isset($_POST['loginPassword']) ? $_POST['loginPassword'] : '';

    // Prepare a select statement
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM user WHERE email = ?");
    $stmt->execute([$loginEmail]);
    $user = $stmt->fetch();

    // Verify user and password
    if ($user && password_verify($loginPassword, $user['password_hash'])) {
        // Password is correct, so start a new session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        // Redirect user to welcome page
        header("location: welcome.php"); // Change 'welcome.php' to the page you want users to go to after login
        exit();
    } else {
        // Display an error message if password is not valid
        $login_err = "Invalid email or password.";
    }
}
?>

<!-- Place this HTML where you want the login form to appear -->
<?php if(isset($login_err)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $login_err; ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="loginEmail">Email:</label>
    <input type="email" name="loginEmail" id="loginEmail" required><br>

    <label for="loginPassword">Password:</label>
    <input type="password" name="loginPassword" id="loginPassword" required><br>

    <input type="hidden" name="loginAction" value="login">
    <input type="submit" value="Login">
</form>
