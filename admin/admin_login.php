<?php
include '../includes/db.php';
include '../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['user'] ?? '');
    $password = $_POST['pass'] ?? '';
    $passwordHash = hash('sha256', $password);

    $stmt = $conn->prepare('SELECT id, username FROM admin WHERE username = ? AND password = ?');
    $stmt->bind_param('ss', $username, $passwordHash);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin) {
        $_SESSION['admin'] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header('Location: admin_dashboard.php');
        exit;
    }
    $error = 'Invalid manager credentials.';
}

renderHeader($conn, 'Admin Login');
?>

<h2>Administrator Login</h2>
<?php if ($error !== ''): ?>
    <p class="notice"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" onsubmit="return validateAdminLogin();">
    <label for="user">Username</label>
    <input id="user" name="user" required>

    <label for="pass">Password</label>
    <input id="pass" name="pass" type="password" minlength="6" required>

    <button class="btn" type="submit">Login</button>
</form>

<script>
function validateAdminLogin() {
    const user = document.getElementById('user').value.trim();
    const pass = document.getElementById('pass').value;

    if (user.length < 3) {
        alert('Username should be at least 3 characters.');
        return false;
    }
    if (pass.length < 6) {
        alert('Password should be at least 6 characters.');
        return false;
    }
    return true;
}
</script>

<?php renderFooter(); ?>
