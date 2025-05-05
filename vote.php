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
    $post_id = $_POST['post_id'];
    $vote = (int)$_POST['vote']; // 1 or -1

    if (!in_array($vote, [1, -1])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid vote value']);
        exit;
    }

    // Check existing vote
    $stmt = $conn->prepare("SELECT vote FROM post_votes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['vote'] == $vote) {
            // Same vote → remove it
            $stmt = $conn->prepare("DELETE FROM post_votes WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $user_id);
            $stmt->execute();
        } else {
            // Different vote → update it
            $stmt = $conn->prepare("UPDATE post_votes SET vote = ? WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("iii", $vote, $post_id, $user_id);
            $stmt->execute();
        }
    } else {
        // New vote
        $stmt = $conn->prepare("INSERT INTO post_votes (post_id, user_id, vote) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $post_id, $user_id, $vote);
        $stmt->execute();
    }

    // Get updated vote count and user vote
    $stmt = $conn->prepare("
        SELECT
            (SELECT SUM(vote) FROM post_votes WHERE post_id = ?) AS vote_count,
            (SELECT vote FROM post_votes WHERE post_id = ? AND user_id = ?) AS user_vote
    ");
    $stmt->bind_param("iii", $post_id, $post_id, $user_id);
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
