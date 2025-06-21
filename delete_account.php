<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch username before deleting
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$username = $user['username'];

// 1. Log into deleted_users
// Check if already logged as deleted
$check = $conn->prepare("SELECT 1 FROM deleted_users WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    // Only insert if not already there
    $insert = $conn->prepare("INSERT INTO deleted_users (user_id, username, deleted_at) VALUES (?, ?, NOW())");
    $insert->bind_param("is", $user_id, $username);
    $insert->execute();
}

// 2. Delete user from users table
$delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete->bind_param("i", $user_id);
$delete->execute();

// 3. (Optional) anonymize or delete related content here

// 4. Clear session
session_destroy();

// 5. Redirect
header("Location: goodbye.php");
exit();
?>
