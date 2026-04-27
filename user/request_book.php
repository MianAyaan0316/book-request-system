<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';
require '../includes/auth.php';
requireUserLogin();
require '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_title'])) {
    $category  = htmlspecialchars(trim($_POST['category']));
    $bookTitle = htmlspecialchars(trim($_POST['book_title']));

    if (empty($category) || empty($bookTitle)) {
        $error = "Please select a category and book.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO book_requests (user_id, username, email, book_title, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $_SESSION['username'], $_SESSION['email'], $bookTitle, $category]);
            $success = "Your request for '$bookTitle' has been submitted!";
        } catch (PDOException $e) {
            $error = "Could not submit request. Please try again.";
        }
    }
}
?>

<div style="background:#1a237e;color:white;padding:15px 30px;display:flex;justify-content:space-between;">
    <span>Book Request System</span>
    <span>
        <a href="dashboard.php" style="color:#90caf9;">My Requests</a> |
        <a href="http://localhost:8081/book-request-system/logout.php" style="color:#ef9a9a;">Logout</a>
    </span>
</div>

<div class="container">
    <div class="card">
        <h2>Request a Book</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Step 1: Select Category to Load Books</label>
            <select id="categorySelect">
                <option value="">-- Select Category --</option>
                <option value="App Development">App Development</option>
                <option value="Mobile Development">Mobile Development</option>
                <option value="AI">AI</option>
            </select>
        </div>

        <div id="loadingMsg" style="display:none;color:#1565c0;margin-bottom:10px;">⏳ Loading books...</div>
        <div id="apiError" style="display:none;" class="alert alert-error"></div>

        <form method="POST" id="requestForm" style="display:none;">
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" id="categoryDisplay" name="category" readonly style="background:#f5f5f5;">
            </div>
            <div class="form-group">
                <label>Select Book Title</label>
                <select name="book_title" id="bookSelect" required>
                    <option value="">-- Select a Book --</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>
</div>

<script>
document.getElementById('categorySelect').addEventListener('change', function() {
    var category = this.value;
    if (!category) return;

    document.getElementById('loadingMsg').style.display = 'block';
    document.getElementById('requestForm').style.display = 'none';
    document.getElementById('apiError').style.display = 'none';

    var formData = new FormData();
    formData.append('category', category);

    fetch('http://localhost:8081/book-request-system/api/fetch_books.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('loadingMsg').style.display = 'none';
        if (data.error) {
            document.getElementById('apiError').style.display = 'block';
            document.getElementById('apiError').textContent = data.error;
            return;
        }
        var select = document.getElementById('bookSelect');
        select.innerHTML = '<option value="">-- Select a Book --</option>';
        data.books.forEach(function(book) {
            var opt = document.createElement('option');
            opt.value = book.title;
            opt.textContent = book.title + ' — ' + book.author;
            select.appendChild(opt);
        });
        document.getElementById('categoryDisplay').value = category;
        document.getElementById('requestForm').style.display = 'block';
    })
    .catch(function() {
        document.getElementById('loadingMsg').style.display = 'none';
        document.getElementById('apiError').style.display = 'block';
        document.getElementById('apiError').textContent = 'Network error. Could not reach API.';
    });
});
</script>
<?php require '../includes/footer.php'; ?>