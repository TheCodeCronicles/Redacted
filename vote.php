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
    $vote = intval($_POST['vote']); // 1 or -1
    $user_id = $_SESSION['user_id'];

    // Insert or update the vote
    $stmt = $conn->prepare("INSERT INTO post_votes (user_id, post_id, vote) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE vote = VALUES(vote)");
    $stmt->bind_param("iii", $user_id, $post_id, $vote);

    if ($stmt->execute()) {
        echo "Voted!";
    } else {
        http_response_code(500);
        echo "Failed to vote.";
    }
}
?>
