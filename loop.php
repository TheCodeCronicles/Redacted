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
    IFNULL(SUM(post_votes.vote), 0) AS votes
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    LEFT JOIN post_votes ON posts.id = post_votes.post_id
    WHERE posts.image_path LIKE '%.mp4' OR posts.image_path LIKE '%.webm' OR posts.image_path LIKE '%.ogg'
    GROUP BY posts.id
    ORDER BY posts.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loop - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: black;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
        }

        .video-post {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            scroll-snap-align: start;
            position: relative;
            overflow: hidden;
        }

        video {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: contain; /* show entire video */
            background: black; /* black bars */
            z-index: 0;
        }

        .overlay {
            z-index: 2;
            color: white;
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            width: 100%;
            box-sizing: border-box;
        }

        .back-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            background-color: #ff4444;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 10px;
            z-index: 5;
            cursor: pointer;
            font-weight: bold;
        }

        .vote-count {
            font-size: 1.2em;
            margin-top: 5px;
        }

        /* Styles for the comment section */
        .comment-panel {
            display: none;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 600px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            z-index: 100;
            height: 60vh;
            overflow: hidden; /* Hide scrollbars for the panel */
            border-radius: 10px;
            display: flex;
            flex-direction: column;
        }

        .comment-panel .close-btn {
            background-color: #ff4444;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .comment-list {
            list-style-type: none;
            padding: 0;
            flex-grow: 1;
            overflow-y: auto; /* Make only the comment list scrollable */
            margin-bottom: 10px;
        }

        .no-comments {
            text-align: center;
            color: #aaa;
            margin-top: 20px;
        }

        .comment-form {
            margin-top: auto; /* Push the form to the bottom */
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #333;
            height: 40px; /* Fixed height for the comment box */
            resize: none;
        }

        .comment-form button {
            background-color: #ff4444;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<a href="feed.php"><button class="back-btn">← Back to Feed</button></a>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="video-post">
        <video class="reel-video" autoplay loop playsinline>
            <source src="<?php echo htmlspecialchars($row['image_path']); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="overlay">
            <h3>@<?php echo htmlspecialchars($row['username']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
            <div class="vote-count">❤️ <?php echo $row['votes']; ?> votes</div>
            <small><?php echo $row['created_at']; ?></small>
            <button class="comment-btn" onclick="toggleCommentPanel(<?php echo $row['id']; ?>)">💬 Comments</button>
        </div>
    </div>

    <!-- Comment Panel -->
    <div id="comment-panel-<?php echo $row['id']; ?>" class="comment-panel">
        <button class="close-btn" onclick="closeCommentPanel(<?php echo $row['id']; ?>)">X</button>

        <div id="comment-list-<?php echo $row['id']; ?>" class="comment-list">
            <!-- Comments will be loaded here -->
            <div class="no-comments" id="no-comments-<?php echo $row['id']; ?>">Loading comments...</div>
        </div>

        <form class="comment-form" onsubmit="submitComment(event, <?php echo $row['id']; ?>)">
            <textarea id="comment-input-<?php echo $row['id']; ?>" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post Comment</button>
        </form>
    </div>

<?php endwhile; ?>

<script>
// Toggle comment panel visibility
function toggleCommentPanel(postId) {
    const panel = document.getElementById(`comment-panel-${postId}`);
    const allPanels = document.querySelectorAll('.comment-panel');
    
    // Close all panels before opening the selected one
    allPanels.forEach(p => {
        if (p !== panel) {
            p.style.display = 'none';
        }
    });

    panel.style.display = panel.style.display === 'block' ? 'none' : 'block';

    // Prevent scrolling through reels when comments are open
    if (panel.style.display === 'block') {
        document.body.style.overflow = 'hidden'; // Disable scrolling on the body
        loadComments(postId);
    } else {
        document.body.style.overflow = 'scroll'; // Re-enable scrolling on the body
    }
}

// Close the comment panel
function closeCommentPanel(postId) {
    const panel = document.getElementById(`comment-panel-${postId}`);
    panel.style.display = 'none';
    document.body.style.overflow = 'scroll'; // Re-enable scrolling on the body
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

// Load comments for a specific post
function loadComments(postId) {
    const commentList = document.getElementById(`comment-list-${postId}`);
    const noComments = document.getElementById(`no-comments-${postId}`);

    fetch(`comments.php?post_id=${postId}`)
        .then(response => response.json())
        .then(comments => {
            if (comments.length === 0) {
                noComments.textContent = 'No comments yet.';
            } else {
                noComments.style.display = 'none';
                commentList.innerHTML = '';
                comments.forEach(comment => {
                    const commentItem = document.createElement('div');
                    commentItem.classList.add('comment-item');
                    commentItem.innerHTML = `
                        <strong>@${comment.username}</strong>
                        <p>${comment.content}</p>
                    `;
                    commentList.appendChild(commentItem);
                });
            }
        })
        .catch(error => {
            noComments.textContent = 'Error loading comments.';
        });
}

// Function to reset all comment panels to their closed state
function resetCommentPanels() {
    const allPanels = document.querySelectorAll('.comment-panel');
    allPanels.forEach(panel => {
        panel.style.display = 'none'; // Hide all comment panels by default
    });
    document.body.style.overflow = 'scroll'; // Re-enable scrolling on the body
}

// Ensure comment panels are reset and closed when page loads
window.addEventListener('load', () => {
    resetCommentPanels(); // Close all comment panels on page load
});

// Close all open comment panels when switching tabs or navigating between pages
window.addEventListener('popstate', () => {
    resetCommentPanels(); // Close all comment panels on browser navigation
});

// Example: Reset the comment panels when switching between feed and loop tabs (replace with your actual tab navigation code)
document.querySelectorAll('.loop-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        resetCommentPanels(); // Close all comment panels on tab switch
    });
});

document.querySelectorAll('.feed-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        resetCommentPanels(); // Close all comment panels when switching to the feed
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
            video.style.objectFit = 'contain';  // Adjust object fit to prevent cut-off
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
</script>


</body>
</html>
