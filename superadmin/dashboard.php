<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireSuperAdminLogin();
require '../includes/header.php';
?>
<div style="background:#4a148c;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Super Admin Panel</span>
    <span>
        <a href="manage_requests.php" style="color:#ce93d8;">Requests</a> |
        <a href="manage_users.php" style="color:#ce93d8;">Users</a> |
        <a href="manage_admins.php" style="color:#ce93d8;">Admins</a> |
        <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a>
    </span>
</div>
<div class="container">
    <div class="card">
        <h2>Super Admin Dashboard</h2>
        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>. You have full system control.</p>
        <br>
        <a href="manage_requests.php" class="btn btn-primary" style="margin-right:10px;">Manage Requests</a>
        <a href="manage_users.php" class="btn btn-warning" style="margin-right:10px;">Manage Users</a>
        <a href="manage_admins.php" class="btn btn-success">Manage Admins</a>
    </div>
</div>
<?php require '../includes/footer.php'; ?>