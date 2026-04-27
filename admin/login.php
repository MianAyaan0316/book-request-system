<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();

if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header("Location: dashboard.php"); exit();
}

require '../config/db.php';
require '../includes/header.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND role = 'admin'");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            $_SESSION['admin_role'] = 'admin';
            header("Location: dashboard.php"); exit();
        } else {
            $error = "Invalid admin credentials.";
        }
    } catch (PDOException $e) {
        $error = "Something went wrong.";
    }
}
?>
<div class="container">
    <div class="card" style="max-width:450px;margin:60px auto;">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Login as Admin</button>
        </form>
        <p style="margin-top:15px;text-align:center;">
            <a href="http://localhost:8081/book-request-system/user/login.php" class="link">← Back to User Login</a>
        </p>
    </div>
</div>
<?php require '../includes/footer.php'; ?>