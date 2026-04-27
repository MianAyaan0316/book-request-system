<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireAdminLogin();
require '../includes/header.php';

try {
    $totalUsers    = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM book_requests")->fetchColumn();
    $totalRequests = $pdo->query("SELECT COUNT(*) FROM book_requests")->fetchColumn();
    $inProgress    = $pdo->query("SELECT COUNT(*) FROM book_requests WHERE status='in_progress'")->fetchColumn();
    $completed     = $pdo->query("SELECT COUNT(*) FROM book_requests WHERE status='completed'")->fetchColumn();
} catch (PDOException $e) {
    $totalUsers = $totalRequests = $inProgress = $completed = 0;
}
?>
<div style="background:#1a237e;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Admin Panel</span>
    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> |
    <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a></span>
</div>
<div class="container">
    <h2 style="margin-bottom:20px;">Dashboard Overview</h2>
    <div class="stat-grid">
        <div class="stat-card"><div class="number"><?php echo $totalUsers; ?></div><div class="label">Total Unique Users</div></div>
        <div class="stat-card"><div class="number"><?php echo $totalRequests; ?></div><div class="label">Total Book Requests</div></div>
        <div class="stat-card"><div class="number" style="color:#1565c0;"><?php echo $inProgress; ?></div><div class="label">Requests In Progress</div></div>
        <div class="stat-card"><div class="number" style="color:#2e7d32;"><?php echo $completed; ?></div><div class="label">Completed Requests</div></div>
    </div>
    <div class="card">
        <p style="color:#777;">Admins can only view statistics. Contact Super Admin for full control.</p>
    </div>
</div>
<?php require '../includes/footer.php'; ?>