<?php
session_start();
require_once 'db/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch only video posts
$sql = "SELECT posts.*, users.username, 
    IFNULL(SUM(post_votes.vote), 0) AS votes,
    (SELECT vote FROM post_votes WHERE post_id = posts.id AND user_id = ?) AS user_vote
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    LEFT JOIN post_votes ON posts.id = post_votes.post_id
    WHERE posts.image_path LIKE '%.mp4' OR posts.image_path LIKE '%.webm' OR posts.image_path LIKE '%.ogg'
    GROUP BY posts.id
    ORDER BY RAND()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redacts - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="loopbody">

<?php include 'navbar.php'; ?>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="video-post">
        <div class="reel-frame">
            <video class="reel-video" autoplay loop playsinline>
                <source src="<?php echo htmlspecialchars($row['image_path']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <div class="overlay">
                <a href="profile.php?user=<?php echo urlencode($row['username']); ?>">
                    <h3>@<?php echo htmlspecialchars($row['username']); ?></h3>
                </a>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                <small><?php echo $row['created_at']; ?></small>
            </div>

            <div class="vote-container-loop">

                <?php
                $user_vote = $row['user_vote'];
                $up_icon = $user_vote == 1 ? 'assets/images/upVote-arrow.png' : 'assets/images/up-arrow.png';
                $down_icon = $user_vote == -1 ? 'assets/images/downVote-arrow.png' : 'assets/images/down-arrow.png';
                ?>


                <button class="vote" onclick="vote(<?php echo $row['id']; ?>, 1)">
                    <img src="<?php echo $up_icon; ?>" alt="Upvote" width="24" height="24">
                </button>

                <span class="vote-count" id="votes-<?php echo $row['id']; ?>" width="24" height="24"><?php echo $row['votes']; ?></span>

                <button class="vote" onclick="vote(<?php echo $row['id']; ?>, -1)">
                    <img src="<?php echo $down_icon; ?>" alt="Downvote" width="24" height="24">
                </button>

                <button class="vote" onclick="toggleCommentPanel(<?php echo $row['id']; ?>)">
                    <img src="assets/images/comment.png" alt="Upvote" width="24" height="24">
                </button>
            </div>

            <?php
            // Fetch comments for the post
            $post_id = $row['id'];
            $comment_query = $conn->prepare("SELECT comments.*, users.username FROM comments
                JOIN users ON comments.user_id = users.id
                WHERE comments.post_id = ?
                ORDER BY comments.created_at ASC");

            $comment_query->bind_param("i", $post_id);
            $comment_query->execute();
            $comments = $comment_query->get_result();
            ?>

            <!-- Comment Panel -->
            <div id="comment-panel-<?php echo $row['id']; ?>" class="comment-panel">
                <h4>Comments:</h4>
                <button class="close-btn" onclick="closeCommentPanel(<?php echo $row['id']; ?>)">
                    X
                </button>

                <div id="comment-list-<?php echo $row['id']; ?>" class="comment-list">
                    <div class="comments">
                        <?php while ($comment = $comments->fetch_assoc()): ?>

                            <?php
                            // Get current vote count
                            $vote_query = $conn->prepare("SELECT SUM(vote) as votes, 
                                (SELECT vote FROM comment_votes WHERE comment_id = ? AND user_id = ?) AS user_vote
                                FROM comment_votes WHERE comment_id = ?");
                            $vote_query->bind_param("iii", $comment['id'], $user_id, $comment['id']);
                            $vote_query->execute();
                            $vote_result = $vote_query->get_result();
                            $vote_data = $vote_result->fetch_assoc();
                            $vote_count = $vote_data['votes'] ?? 0;


                            $user_vote = $vote_data['user_vote'];
                            $up_icon = $user_vote == 1 ? 'assets/images/upVote-arrow.png' : 'assets/images/up-arrow.png';
                            $down_icon = $user_vote == -1 ? 'assets/images/downVote-arrow.png' : 'assets/images/down-arrow.png';
                            ?>

                            <div class="comment-redacts">

                                <div class="cooment-content">
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
                                    <button class="vote" onclick="voteComment(<?php echo $comment['id']; ?>, 1)">
                                        <img src="<?php echo $up_icon; ?>" alt="Upvote" width="16" height="16">
                                    </button>

                                    <span class="vote-count"><?php echo $vote_count; ?></span>

                                    <button class="vote" onclick="voteComment(<?php echo $comment['id']; ?>, -1)">
                                        <img src="<?php echo $down_icon; ?>" alt="Downvote" width="16" height="16">
                                    </button>

                                </div>

                            </div>

                        <?php endwhile; ?>
                    </div>
                </div>

                <form class="comment-input" onsubmit="submitComment(event, <?php echo $row['id']; ?>)">
                    <input type="text" name="content" placeholder="Write a comment..." required>
                    <button type="submit">Post Comment</button>
                </form>
            </div>

        </div>
    </div>
<?php endwhile; ?>

<script>
function toggleCommentPanel(postId) {
    const panel = document.getElementById(`comment-panel-${postId}`);
    const allPanels = document.querySelectorAll('.comment-panel');

    const isVisible = panel.classList.contains('show');

    // Hide all panels first
    allPanels.forEach(p => p.classList.remove('show'));

    if (!isVisible) {
        panel.classList.add('show');
        document.body.style.overflow = 'hidden'; // Lock scroll
    } else {
        document.body.style.overflow = 'auto'; // Unlock scroll
    }
}

function closeCommentPanel(postId) {
    const panel = document.getElementById(`comment-panel-${postId}`);
    panel.classList.remove('show');
    document.body.style.overflow = 'auto';
}


// Submit a new comment
function submitComment(event, postId) {
    event.preventDefault();
    const commentInput = document.getElementById(`comment-input-${postId}`);
    const content = commentInput.value.trim();

    if (content) {
        fetch('comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}&content=${encodeURIComponent(content)}`
        })
        .then(response => response.text())
        .then(data => {
            commentInput.value = ''; // Clear the input field
            loadComments(postId); // Reload the comments
        })
        .catch(error => alert('Failed to post comment'));
    }
}

// Function to reset all comment panels to their closed state (with class toggle)
function resetCommentPanels() {
    const allPanels = document.querySelectorAll('.comment-panel');
    allPanels.forEach(panel => {
        panel.classList.remove('show'); // Remove the animation class
    });
    document.body.style.overflow = 'scroll'; // Re-enable scrolling on the body
}

// Ensure comment panels are reset and closed when page loads
window.addEventListener('load', () => {
    resetCommentPanels();
});

// Close all open comment panels when switching tabs or navigating between pages
window.addEventListener('popstate', () => {
    resetCommentPanels();
});

// Example: Reset the comment panels when switching between feed and loop tabs
document.querySelectorAll('.loop-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        resetCommentPanels();
    });
});

document.querySelectorAll('.feed-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        resetCommentPanels();
    });
});


// Intersection Observer to control when videos play based on visibility
const videoObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const video = entry.target;
        const videoContainer = video.closest('.video-post'); // Assuming your video is wrapped in a .video-post container

        // Check if the video is 90% visible
        if (entry.isIntersecting && entry.intersectionRatio >= 0.9) {
            // Ensure the video fills the entire screen
            video.play();  // Play the video when 90% is visible
        } else {
            video.pause();  // Pause video when less than 90% is visible
        }
    });
}, {
    threshold: 0.9  // Trigger when 90% of the video is visible
});

// Observe all videos on the page
document.querySelectorAll('video.reel-video').forEach(video => {
    videoObserver.observe(video);
});

function vote(postId, voteValue) {
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('vote', voteValue);

    fetch('vote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            alert("Vote failed!");
            return;
        }
        // Reload votes
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function submitComment(event, postId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    formData.append('post_id', postId);

    fetch('comment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            alert("Failed to comment!");
            return;
        }
        // Reload page to show new comment
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function voteComment(commentId, vote) {
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('vote', vote);
    
        fetch('comment_vote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            alert("Failed to vote.");
            return;
        }
        location.reload(); // reload page to update vote counts
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Toggle mute/unmute on video click
document.querySelectorAll('video.reel-video').forEach(video => {
    video.addEventListener('click', () => {
        video.muted = !video.muted;
    });
});

</script>

<?php include 'settings.php'; ?>

</body>
</html>
