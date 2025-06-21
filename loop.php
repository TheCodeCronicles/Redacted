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
    <div class="video-post" data-post-id="<?php echo $row['id']; ?>" id="post-<?php echo $row['id']; ?>">
        <div class="reel-frame">
            <video class="reel-video" autoplay loop playsinline>
                <?php
                    $videoType = '';
                    $path = $row['image_path'];
                    if (str_ends_with($path, '.webm')) {
                        $videoType = 'video/webm';
                    } elseif (str_ends_with($path, '.ogg')) {
                        $videoType = 'video/ogg';
                    } else {
                        $videoType = 'video/mp4';
                    }
                    ?>
                    <source src="<?php echo htmlspecialchars($path); ?>" type="<?php echo $videoType; ?>">
            </video>

            <div class="overlay">
                <a href="profile.php?user=<?php echo urlencode($row['username']); ?>">
                    <h3>@<?php echo htmlspecialchars($row['username']); ?></h3>
                </a>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                <?php $topic_stmt = $conn->prepare("
                    SELECT t.name FROM topics t
                    JOIN post_topics pt ON pt.topic_id = t.id
                    WHERE pt.post_id = ?
                ");
                    
                $topic_stmt->bind_param("i", $row['id']);
                $topic_stmt->execute();
                $topics_result = $topic_stmt->get_result();
                $topics = [];
                    
                while ($rowtopic = $topics_result->fetch_assoc()) {
                    $topics[] = $rowtopic['name'];
                }?>

                <div class="tag-row">
                    <?php foreach ($topics as $tag): ?>
                        <a class="post-tag" href="topic.php?name=<?= urlencode($tag) ?>">#<?= htmlspecialchars($tag) ?></a>
                    <?php endforeach; ?>
                </div><br>
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
let isMuted = false; // Global mute state

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reel-video').forEach(video => {
        video.pause();
        video.currentTime = 0; // Optional: reset to beginning if needed
    });
});


function setMuteForAll(mute) {
    document.querySelectorAll('.reel-video').forEach(v => {
        v.muted = mute;
    });
}

function handleScrollStop() {
    const videos = document.querySelectorAll('.reel-video');
    let mostVisibleVideo = null;
    let maxRatio = 0;

    videos.forEach(video => {
        const rect = video.getBoundingClientRect();
        const height = window.innerHeight;
        const visibleHeight = Math.min(rect.bottom, height) - Math.max(rect.top, 0);
        const ratio = visibleHeight / rect.height;

        if (ratio > maxRatio) {
            maxRatio = ratio;
            mostVisibleVideo = video;
        }
    });

    if (mostVisibleVideo && maxRatio >= 0.5) {
        const container = mostVisibleVideo.closest('.video-post');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        videos.forEach(video => {
            video.pause();
            video.muted = isMuted;
        });

        mostVisibleVideo.muted = isMuted;
        mostVisibleVideo.play();
    } else {
        videos.forEach(video => video.pause());
    }
}

let scrollTimeout;
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(handleScrollStop, 150);
});

window.addEventListener('load', () => {
    const videos = document.querySelectorAll('.reel-video');
    videos.forEach((video, i) => {
        video.muted = isMuted;
    });
    handleScrollStop();
});

// Click-to-toggle-mute functionality
window.addEventListener('click', e => {
    const clickedVideo = e.target.closest('video.reel-video');
    if (clickedVideo) {
        isMuted = !isMuted;
        setMuteForAll(isMuted);
    }
});

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

// Observe all videos on the page
document.querySelectorAll('video.reel-video').forEach(video => {
    videoObserver.observe(video);
});

function vote(postId, vote) {
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('vote', vote);

    fetch('vote.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            const container = document.querySelector(`[data-post-id="${postId}"]`) || document.getElementById(`post-${postId}`);
            if (!container) return;

            // Update count
            const voteCountElem = container.querySelector(`#votes-${postId}`);
            if (voteCountElem) voteCountElem.textContent = data.vote_count;

            // Update icons
            const upImg = container.querySelector('button.vote:nth-of-type(1) img');
            const downImg = container.querySelector('button.vote:nth-of-type(2) img');

            if (data.user_vote == 1) {
                upImg.src = "assets/images/upVote-arrow.png";
                downImg.src = "assets/images/down-arrow.png";
            } else if (data.user_vote == -1) {
                upImg.src = "assets/images/up-arrow.png";
                downImg.src = "assets/images/downVote-arrow.png";
            } else {
                upImg.src = "assets/images/up-arrow.png";
                downImg.src = "assets/images/down-arrow.png";
            }
        } else {
            alert(data.message || "Error voting.");
        }
    })
    .catch(err => console.error('Vote error:', err));
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
    .then(response => response.text())
    .then(result => {
        if (result === 'success') {
            form.reset(); // Clear the form input
            loadComments(postId); // Reload comments dynamically
        } else {
            alert("Failed to post comment.");
        }
    })
    .catch(error => console.error("Error submitting comment:", error));
}


function loadComments(postId) {
    fetch(`comment.php?post_id=${postId}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById(`comment-list-${postId}`).innerHTML = html;
    })
    .catch(error => console.error("Failed to load comments:", error));
}


function voteComment(commentId, vote) {
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('vote', vote);

    fetch('comment_vote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update vote count
            const comment = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (comment) {
                const voteCount = comment.querySelector('.vote-count');
                const upButton = comment.querySelector('.vote-up img');
                const downButton = comment.querySelector('.vote-down img');

                voteCount.textContent = data.vote_count;

                // Update icons based on new vote
                if (data.user_vote == 1) {
                    upButton.src = "assets/images/upVote-arrow.png";
                    downButton.src = "assets/images/down-arrow.png";
                } else if (data.user_vote == -1) {
                    upButton.src = "assets/images/up-arrow.png";
                    downButton.src = "assets/images/downVote-arrow.png";
                } else {
                    upButton.src = "assets/images/up-arrow.png";
                    downButton.src = "assets/images/down-arrow.png";
                }
            }
        } else {
            alert(data.message || "Voting failed.");
        }
    })
    .catch(error => console.error("Voting error:", error));
}
</script>

<?php include 'settings.php'; ?>
</body>
</html>
