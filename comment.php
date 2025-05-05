<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Not logged in');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new comment
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);

    if ($content !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        $stmt->execute();
        echo 'success';
    } else {
        echo 'empty';
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['post_id'])) {
    // Return comment list HTML for the post
    $post_id = $_GET['post_id'];

    $stmt = $conn->prepare("SELECT comments.*, users.username,
        (SELECT SUM(vote) FROM comment_votes WHERE comment_id = comments.id) AS votes,
        (SELECT vote FROM comment_votes WHERE comment_id = comments.id AND user_id = ?) AS user_vote
        FROM comments
        JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at ASC");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    ob_start();
    while ($comment = $result->fetch_assoc()):
        $vote = $comment['user_vote'];
        $vote_count = $comment['votes'] ?? 0;

        $up_icon = $vote == 1 ? 'assets/images/upVote-arrow.png' : 'assets/images/up-arrow.png';
        $down_icon = $vote == -1 ? 'assets/images/downVote-arrow.png' : 'assets/images/down-arrow.png';
    ?>
    <div class="comment-redacts" data-comment-id="<?php echo $comment['id']; ?>">
        <div class="comment-content">
            <div class="comment-username">
                <a href="profile.php?user=<?php echo urlencode($comment['username']); ?>">
                    <strong>@<?php echo htmlspecialchars($comment['username']); ?></strong>
                </a>
            </div>

            <div class="comment-text">
                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
            </div>

            <div class="comment-date">
                <small>(<?php echo $comment['created_at']; ?>)</small>
            </div>
        </div>

        <div class="vote-buttons">
            <button class="vote vote-up" onclick="voteComment(<?php echo $comment['id']; ?>, 1)">
                <img src="<?php echo $up_icon; ?>" alt="Upvote" width="16" height="16">
            </button>
            
            <span class="vote-count"><?php echo $vote_count; ?></span>
            
            <button class="vote vote-down" onclick="voteComment(<?php echo $comment['id']; ?>, -1)">
                <img src="<?php echo $down_icon; ?>" alt="Downvote" width="16" height="16">
            </button>
        </div>

    </div>
    <?php endwhile;
    echo ob_get_clean();
}
?>
