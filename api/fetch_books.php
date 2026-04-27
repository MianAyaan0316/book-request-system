<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

$category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : '';
$userId   = $_SESSION['user_id'];

$queryMap = [
    'App Development'    => 'web+development',
    'Mobile Development' => 'mobile+development',
    'AI'                 => 'artificial+intelligence'
];

if (!array_key_exists($category, $queryMap)) {
    echo json_encode(['error' => 'Invalid category']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM api_calls WHERE user_id = ? AND called_at > NOW() - INTERVAL 24 HOUR");
    $stmt->execute([$userId]);
    if ($stmt->fetchColumn() >= 5) {
        echo json_encode(['error' => 'Rate limit reached. Max 5 API calls per 24 hours.']);
        exit();
    }
    $pdo->prepare("INSERT INTO api_calls (user_id) VALUES (?)")->execute([$userId]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error.']);
    exit();
}

$query    = $queryMap[$category];
$apiUrl   = "https://www.googleapis.com/books/v1/volumes?q=$query&maxResults=10";
$response = @file_get_contents($apiUrl);

if ($response === false) {
    echo json_encode(['error' => 'Could not reach Google Books API. Check your internet.']);
    exit();
}

$data  = json_decode($response, true);
$books = [];

if (isset($data['items'])) {
    foreach ($data['items'] as $item) {
        $info   = $item['volumeInfo'] ?? [];
        $title  = $info['title'] ?? 'Unknown Title';
        $author = isset($info['authors']) ? implode(', ', $info['authors']) : 'Unknown Author';
        try {
            $pdo->prepare("INSERT IGNORE INTO books (title, author, category) VALUES (?, ?, ?)")
                ->execute([$title, $author, $category]);
        } catch (PDOException $e) {}
        $books[] = ['title' => $title, 'author' => $author];
    }
}

echo json_encode(['success' => true, 'books' => $books]);
exit();
?>