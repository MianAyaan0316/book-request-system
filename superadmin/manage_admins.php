<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireSuperAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $newUsername = htmlspecialchars(trim($_POST['new_username']));
    $newPassword = $_POST['new_password'];
    if (empty($newUsername) || empty($newPassword)) {
        $error = "Both fields required.";
    } else {
        try {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO admins (username,password,role) VALUES (?,?,'admin')")->execute([$newUsername,$hashed]);
            $message = "Admin '$newUsername' added.";
        } catch (PDOException $e) { $error = "Username exists or error occurred."; }
    }
}

if (isset($_GET['delete'])) {
    $aid = (int)$_GET['delete'];
    try {
        $check = $pdo->prepare("SELECT role FROM admins WHERE id=?");
        $check->execute([$aid]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['role'] === 'superadmin') {
            $error = "Cannot delete Super Admin!";
        } else {
            $pdo->prepare("DELETE FROM admins WHERE id=? AND role!='superadmin'")->execute([$aid]);
            $message = "Admin deleted.";
        }
    } catch (PDOException $e) { $error = "Error deleting."; }
}

require '../includes/header.php';
try {
    $admins = $pdo->query("SELECT * FROM admins ORDER BY role DESC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $admins = []; }
?>
<div style="background:#4a148c;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Super Admin</span>
    <span>
        <a href="dashboard.php" style="color:#ce93d8;">Dashboard</a> |
        <a href="manage_requests.php" style="color:#ce93d8;">Requests</a> |
        <a href="manage_users.php" style="color:#ce93d8;">Users</a> |
        <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a>
    </span>
</div>
<div class="container">
    <div class="card">
        <h2>Add New Admin</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <form method="POST" style="display:flex;gap:15px;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="flex:1;min-width:180px;">
                <label>Username</label>
                <input type="text" name="new_username" required>
            </div>
            <div class="form-group" style="flex:1;min-width:180px;">
                <label>Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="add_admin" class="btn btn-success" style="margin-top:24px;">Add Admin</button>
            </div>
        </form>
    </div>
    <div class="card">
        <h2>All Admins</h2>
        <table>
            <thead><tr><th>#</th><th>Username</th><th>Role</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($admins as $i => $adm): ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo htmlspecialchars($adm['username']); ?></td>
                    <td><span style="background:<?php echo $adm['role']==='superadmin'?'#4a148c':'#1a237e';?>;color:white;padding:3px 10px;border-radius:20px;font-size:12px;"><?php echo ucfirst($adm['role']); ?></span></td>
                    <td>
                        <?php if ($adm['role']!=='superadmin'): ?>
                            <a href="?delete=<?php echo $adm['id'];?>" class="btn btn-danger" style="padding:4px 10px;font-size:12px;" onclick="return confirm('Delete admin?')">Delete</a>
                        <?php else: ?>
                            <span style="color:#999;font-size:13px;">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require '../includes/footer.php'; ?>