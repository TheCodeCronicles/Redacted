<?php
session_start();
require_once 'db/db.php';

if (!isset($_GET['user'])) {
    echo json_encode([]);
    exit();
}

$username = $_GET['user'];
$current_user_id = $_SESSION['user_id'] ?? 0;

// Get ID of the target user
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode([]);
    exit();
}
$target_user_id = $result->fetch_assoc()['id'];

// Get following
$query = "
    SELECT u.id, u.username, u.profile_pic,
        EXISTS (
            SELECT 1 FROM followers f2
            WHERE f2.follower_id = ? AND f2.following_id = u.id
        ) AS is_following
    FROM followers f
    JOIN users u ON f.following_id = u.id
    WHERE f.follower_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $current_user_id, $target_user_id);
$stmt->execute();
$result = $stmt->get_result();

$following = [];
while ($row = $result->fetch_assoc()) {
    $following[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'profile_pic' => $row['profile_pic'] ?: 'assets/images/default.png',
        'is_following' => (bool)$row['is_following']
    ];
}

echo json_encode($following);
