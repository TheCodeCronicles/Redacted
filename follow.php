<?php
session_start();
require 'db/db.php';

$current_user = $_SESSION['user_id'];
$follow_id = (int)$_POST['follow_id'];
$action = $_POST['action'];

if ($action === 'follow') {
    $stmt = $conn->prepare("INSERT IGNORE INTO followers (follower_id, following_id, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $current_user, $follow_id);
    $stmt->execute();
} elseif ($action === 'unfollow') {
    $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->bind_param("ii", $current_user, $follow_id);
    $stmt->execute();
}

header("Location: profile.php?user=" . urlencode($_POST['username'] ?? ''));
exit;
