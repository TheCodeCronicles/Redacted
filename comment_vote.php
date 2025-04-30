<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Not logged in.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = intval($_POST['comment_id']);
    $vote = intval($_POST['vote']);
    $user_id = $_SESSION['user_id'];

    if ($vote !== 1 && $vote !== -1) {
        http_response_code(400);
        echo "Invalid vote.";
        exit();
    }

    // Check if user already voted
    $check = $conn->prepare("SELECT id FROM comment_votes WHERE comment_id = ? AND user_id = ?");
    $check->bind_param("ii", $comment_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing vote
        $stmt = $conn->prepare("UPDATE comment_votes SET vote = ? WHERE comment_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $vote, $comment_id, $user_id);
    } else {
        // Insert new vote
        $stmt = $conn->prepare("INSERT INTO comment_votes (comment_id, user_id, vote) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $comment_id, $user_id, $vote);
    }

    if ($stmt->execute()) {
        echo "Vote recorded.";
    } else {
        http_response_code(500);
        echo "Failed to record vote.";
    }
}
?>
