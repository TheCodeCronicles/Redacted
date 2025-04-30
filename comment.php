<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Not logged in.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = intval($_POST['post_id']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        http_response_code(400);
        echo "Comment cannot be empty.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $content);

    if ($stmt->execute()) {
        echo "Comment added!";
    } else {
        http_response_code(500);
        echo "Failed to add comment.";
    }
}
?>
