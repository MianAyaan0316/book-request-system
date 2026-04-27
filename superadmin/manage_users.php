<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireSuperAdminLogin();

$message = '';

if (isset($_GET['reset'])) {
    try {
        $newPass = password_hash('reset123', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$newPass, (int)$_GET['reset']]);
        $message = "Password reset to: reset123";
    } catch (PDOException $e) { $message = "Error resetting."; }
}

if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_GET['delete']]);
        $message = "User deleted.";
    } catch (PDOException $e) { $message = "Error deleting."; }
}

require '../includes/header.php';
try {
    $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $users = []; }
?>
<div style="background:#4a148c;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Super Admin</span>
    <span>
        <a href="dashboard.php" style="color:#ce93d8;">Dashboard</a> |
        <a href="manage_requests.php" style="color:#ce93d8;">Requests</a> |
        <a href="manage_admins.php" style="color:#ce93d8;">Admins</a> |
        <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a>
    </span>
</div>
<div class="container">
    <div class="card">
        <h2>Manage Users</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (empty($users)): ?>
            <p>No users yet.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $i => $user): ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="?reset=<?php echo $user['id'];?>" class="btn btn-warning" style="padding:4px 10px;font-size:12px;margin-right:5px;" onclick="return confirm('Reset password?')">Reset Password</a>
                        <a href="?delete=<?php echo $user['id'];?>" class="btn btn-danger" style="padding:4px 10px;font-size:12px;" onclick="return confirm('Delete user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php require '../includes/footer.php'; ?>