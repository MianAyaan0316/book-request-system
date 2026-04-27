<?php
function requireUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: http://localhost:8081/book-request-system/user/login.php");
        exit();
    }
}

function requireAdminLogin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
        header("Location: http://localhost:8081/book-request-system/admin/login.php");
        exit();
    }
}

function requireSuperAdminLogin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
        header("Location: http://localhost:8081/book-request-system/superadmin/login.php");
        exit();
    }
}
?>