<?php
require_once 'db/db.php';
session_start();

if (!isset($_GET['u'])) {
    echo json_encode(['available' => false]);
    exit;
}

$username = trim($_GET['u']);
$userId = $_SESSION['user_id'] ?? 0;

// Check if username exists but is not owned by current user
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $userId);
$stmt->execute();
$stmt->store_result();

echo json_encode(['available' => $stmt->num_rows === 0]);
?>
