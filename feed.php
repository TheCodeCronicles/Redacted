<?php
session_start();
require_once 'db/db.php';

// If not logged in, kick to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest posts with user vote
$sort = $_GET['sort'] ?? 'new';
$orderBy = ($sort === 'hot') ? 'votes DESC, posts.created_at DESC' : 'posts.created_at DESC';

$sql = "SELECT posts.*, users.username, 
    IFNULL(SUM(post_votes.vote), 0) AS votes,
    (SELECT vote FROM post_votes WHERE post_id = posts.id AND user_id = ?) AS user_vote
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    LEFT JOIN post_votes ON posts.id = post_votes.post_id
    GROUP BY posts.id
    ORDER BY $orderBy";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feed - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>[REDACTED] Feed</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="post.php">Create Post</a> | <a href="logout.php">Logout</a>
    </header>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" title="Go to top">↑</button>

    <div class="container">
    <section>
    <div style="margin-top: 40px; margin-bottom: 20px;">
    <a href="?sort=new">
        <button class="btn <?php echo ($sort === 'new') ? 'active' : ''; ?>">New</button>
    </a>
    <a href="?sort=hot">
        <button class="btn <?php echo ($sort === 'hot') ? 'active' : ''; ?>">Hot</button>
    </a>
</div>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <h3>@<?php echo htmlspecialchars($row['username']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            
            <?php if (!empty($row['image_path'])): ?>
            <?php
            $ext = strtolower(pathinfo($row['image_path'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', "webp"])):
            ?>
            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Post image" style="max-width: 100%; margin-top: 10px; border-radius: 10px;">
            <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                <video class="click-toggle-mute" autoplay loop muted style="max-width: 100%; margin-top: 10px; border-radius: 10px;">
                <source src="<?php echo htmlspecialchars($row['image_path']); ?>" type="video/<?php echo $ext; ?>">
                Your browser does not support the video tag.
                </video>

        <?php endif; ?>
    <?php endif; ?>
            <!--<hr style="opacity: 25%;">-->
            <div class="vote-container">

                <?php
                $user_vote = $row['user_vote'];
                $up_icon = $user_vote == 1 ? 'assets/images/upVote-arrow.png' : 'assets/images/up-arrow.png';
                $down_icon = $user_vote == -1 ? 'assets/images/downVote-arrow.png' : 'assets/images/down-arrow.png';
                ?>


                <button class="vote" onclick="vote(<?php echo $row['id']; ?>, 1)">
                    <img src="<?php echo $up_icon; ?>" alt="Upvote" width="24" height="24">
                </button>

                
                <button class="vote" onclick="vote(<?php echo $row['id']; ?>, -1)">
                    <img src="<?php echo $down_icon; ?>" alt="Downvote" width="24" height="24">
                </button>
                <span class="vote-count" id="votes-<?php echo $row['id']; ?>" width="24" height="24"><?php echo $row['votes']; ?></span>

            </div>
            <!--<hr style="opacity: 25%;"> -->
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
            
            <div class="comments">
                <h4>Comments:</h4>
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

                    <div class="comment">
                        <strong>@<?php echo htmlspecialchars($comment['username']); ?>:</strong> 
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>

                        <div class="vote-container">
                            <button class="vote" onclick="voteComment(<?php echo $comment['id']; ?>, 1)">
                                <img src="<?php echo $up_icon; ?>" alt="Upvote" width="24" height="24">
                            </button>

                            <button class="vote" onclick="voteComment(<?php echo $comment['id']; ?>, -1)">
                                <img src="<?php echo $down_icon; ?>" alt="Downvote" width="24" height="24">
                            </button>
                            <span class="vote-count"><?php echo $vote_count; ?></span>
                        </div>

                        <small>(<?php echo $comment['created_at']; ?>)</small>
                    </div>
                    
                <?php endwhile; ?>
            </div>  

            <!-- Add Comment Form -->
            <form onsubmit="submitComment(event, <?php echo $row['id']; ?>)">
                <input type="text" name="content" placeholder="Write a comment..." required style="width:70%;">
                <button class="btn" type="submit">Post</button>
            </form>
            
            <small><?php echo $row['created_at']; ?></small>
        </div>

    <?php endwhile; ?>
    </section>
    </div>

    <script>
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
    </script>

<script>
let lastScroll = 0;
const header = document.querySelector('header');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > lastScroll && currentScroll > 100) {
        // Scrolling down
        header.style.top = "-160px";
    } else {
        // Scrolling up
        header.style.top = "0";
    }

    lastScroll = currentScroll;
});
</script>

<script>
// Toggle mute/unmute on video click
document.querySelectorAll('video.click-toggle-mute').forEach(video => {
    video.addEventListener('click', () => {
        video.muted = !video.muted;
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const videos = document.querySelectorAll('video.click-toggle-mute');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            if (entry.isIntersecting) {
                // Play the video (don't unmute)
                video.play().catch(e => {}); 
            } else {
                // Pause and mute the video when out of view
                video.pause();
                video.muted = true;
            }
        });
    }, {
        threshold: 0.25 // At least 25% of the video must be visible
    });

    videos.forEach(video => {
        observer.observe(video);
    });
});
</script>

<script>
// Scroll to Top button functionality
const scrollToTopBtn = document.getElementById('scrollToTopBtn');

// Show the button when scrolling down
window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {  // Show the button when scrolled more than 300px
        scrollToTopBtn.style.display = 'block';
    } else {
        scrollToTopBtn.style.display = 'none';
    }
});

// Scroll to top functionality
scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});
</script>

</body>
</html>
