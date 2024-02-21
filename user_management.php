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



// Handle User CRUD Operations
$userAction = isset($_POST['userAction']) ? $_POST['userAction'] : '';
$userId = isset($_POST['userId']) ? $_POST['userId'] : '';
$userName = isset($_POST['userName']) ? $_POST['userName'] : '';
$userEmail = isset($_POST['userEmail']) ? $_POST['userEmail'] : '';
$userPassword = isset($_POST['userPassword']) ? $_POST['userPassword'] : '';

if ($userAction == 'addUser') {
    // Hash the password before storing it
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$userName, $userEmail, $hashedPassword]);
} elseif ($userAction == 'editUser' && $userId) {
    // Hash the password before storing it
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE user SET name = ?, email = ?, password_hash = ? WHERE id = ?");
    $stmt->execute([$userName, $userEmail, $hashedPassword, $userId]);
} elseif ($userAction == 'deleteUser' && $userId) {
    $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$userId]);
}

// Fetch users from the database
$stmt = $pdo->query("SELECT id, name, email FROM user");
$users = $stmt->fetchAll();

// User CRUD Form and Table (Place this in your HTML body where you want to display it)
?>
<h2>User List</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                    <input type="hidden" name="userAction" value="deleteUser">
                    <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                </form>
                <a href="#editUser" onclick='populateUserForm(<?php echo json_encode($user); ?>)'>Edit</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Add/Edit User</h2>
<form method="POST">
    <input type="hidden" name="userAction" id="userFormAction" value="addUser">
    <input type="hidden" name="userId" id="userId">
    <label for="userName">Name:</label>
    <input type="text" name="userName" id="userName" required><br>
    <label for="userEmail">Email:</label>
    <input type="email" name="userEmail" id="userEmail" required><br>
    <label for="userPassword">Password:</label>
    <input type="password" name="userPassword" id="userPassword" required><br>
    <input type="submit" value="Submit User">
</form>

<script>
    function populateUserForm(user) {
        document.getElementById('userFormAction').value = 'editUser';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        // Do not populate the password field for security reasons
    }
</script>

