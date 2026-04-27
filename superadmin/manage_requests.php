<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireSuperAdminLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reqId = (int)$_POST['request_id'];
    $newStatus = $_POST['new_status'];
    $allowed = ['pending','in_progress','completed'];
    if (in_array($newStatus, $allowed) && $reqId > 0) {
        try {
            $pdo->prepare("UPDATE book_requests SET status=?, notified=0 WHERE id=?")->execute([$newStatus, $reqId]);
            $message = "Status updated.";
        } catch (PDOException $e) { $message = "Error updating."; }
    }
}

if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM book_requests WHERE id=?")->execute([(int)$_GET['delete']]);
        $message = "Request deleted.";
    } catch (PDOException $e) { $message = "Error deleting."; }
}

require '../includes/header.php';
try {
    $requests = $pdo->query("SELECT * FROM book_requests ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $requests = []; }
?>
<div style="background:#4a148c;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Super Admin</span>
    <span>
        <a href="dashboard.php" style="color:#ce93d8;">Dashboard</a> |
        <a href="manage_users.php" style="color:#ce93d8;">Users</a> |
        <a href="manage_admins.php" style="color:#ce93d8;">Admins</a> |
        <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a>
    </span>
</div>
<div class="container">
    <div class="card">
        <h2>All Book Requests</h2>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (empty($requests)): ?>
            <p>No requests found.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>User</th><th>Book</th><th>Category</th><th>Status</th><th>Delete</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $i => $req): ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo htmlspecialchars($req['username']); ?></td>
                    <td><?php echo htmlspecialchars($req['book_title']); ?></td>
                    <td><?php echo htmlspecialchars($req['category']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                            <select name="new_status" style="padding:4px;border-radius:4px;">
                                <option value="pending"     <?php if($req['status']==='pending')     echo 'selected';?>>Pending</option>
                                <option value="in_progress" <?php if($req['status']==='in_progress') echo 'selected';?>>In Progress</option>
                                <option value="completed"   <?php if($req['status']==='completed')   echo 'selected';?>>Completed</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-success" style="padding:4px 10px;font-size:12px;">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $req['id'];?>" class="btn btn-danger" style="padding:4px 10px;font-size:12px;" onclick="return confirm('Delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php require '../includes/footer.php'; ?>