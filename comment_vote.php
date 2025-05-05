<?php
session_start();
require_once 'db/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['comment_id'];
    $vote = (int)$_POST['vote']; // should be 1 or -1

    if (!in_array($vote, [1, -1])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid vote']);
        exit;
    }

    // Check if user already voted
    $stmt = $conn->prepare("SELECT vote FROM comment_votes WHERE comment_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($existing = $result->fetch_assoc()) {
        if ($existing['vote'] == $vote) {
            // Remove the vote (toggle off)
            $stmt = $conn->prepare("DELETE FROM comment_votes WHERE comment_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $comment_id, $user_id);
            $stmt->execute();
        } else {
            // Change the vote
            $stmt = $conn->prepare("UPDATE comment_votes SET vote = ? WHERE comment_id = ? AND user_id = ?");
            $stmt->bind_param("iii", $vote, $comment_id, $user_id);
            $stmt->execute();
        }
    } else {
        // New vote
        $stmt = $conn->prepare("INSERT INTO comment_votes (comment_id, user_id, vote) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $comment_id, $user_id, $vote);
        $stmt->execute();
    }

    // Return updated vote count and current user's vote
    $stmt = $conn->prepare("
        SELECT
            (SELECT SUM(vote) FROM comment_votes WHERE comment_id = ?) AS vote_count,
            (SELECT vote FROM comment_votes WHERE comment_id = ? AND user_id = ?) AS user_vote
    ");
    $stmt->bind_param("iii", $comment_id, $comment_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($vote_count, $user_vote);
    $stmt->fetch();

    echo json_encode([
        'status' => 'success',
        'vote_count' => $vote_count ?? 0,
        'user_vote' => $user_vote ?? 0
    ]);
}
?>
