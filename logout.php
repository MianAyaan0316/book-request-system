<?php
session_start();
session_destroy();
header("Location: http://localhost:8081/book-request-system/user/login.php");
exit();
?>