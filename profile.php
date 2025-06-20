<?php
session_start();
require_once 'db/db.php';

// If not logged in, kick to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['user'])) {
    header("Location: feed.php");
    exit();
}

$username = $_GET['user'];
$user_id = $_SESSION['user_id'];

// Fetch user info
$user_stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user_data = $user_result->fetch_assoc();
$is_own_profile = ($_SESSION['username'] === $username);

// Count followers and following
$follower_count = $conn->query("SELECT COUNT(*) FROM followers WHERE following_id = {$user_data['id']}")->fetch_row()[0];
$following_count = $conn->query("SELECT COUNT(*) FROM followers WHERE follower_id = {$user_data['id']}")->fetch_row()[0];

// Fetch user posts
// Fetch user posts with vote info
$sql = "SELECT posts.*, users.username,
    IFNULL(SUM(post_votes.vote), 0) AS votes,
    (SELECT vote FROM post_votes WHERE post_id = posts.id AND user_id = ?) AS user_vote
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    LEFT JOIN post_votes ON posts.id = post_votes.post_id
    WHERE posts.user_id = ?
    GROUP BY posts.id
    ORDER BY posts.created_at DESC";

$post_stmt = $conn->prepare($sql);
$post_stmt->bind_param("ii", $user_id, $user_data['id']);
$post_stmt->execute();
$posts = $post_stmt->get_result();


$is_following = false;
if (!$is_own_profile) {
    $check_follow = $conn->query("SELECT 1 FROM followers WHERE follower_id = {$user_id} AND following_id = {$user_data['id']}");
    $is_following = $check_follow->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@<?php echo htmlspecialchars($username); ?> | Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="profile-container">

        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user_data['profile_pic'] ?? 'assets/images/default.png'); ?>" class="profile-pic" alt="Profile Picture">

            <div class="profile-info">
                <h2>@<?php echo htmlspecialchars($username); ?></h2>
            
                <div class="follow-stats">
                    <div class="stat">
                        <strong><?php echo $follower_count; ?></strong><span>Followers</span>
                    </div>
                    <div class="stat">
                        <strong><?php echo $following_count; ?></strong><span>Following</span>
                    </div>
                </div>
                <br>
                <p><?php echo nl2br(htmlspecialchars($user_data['bio'] ?? '')); ?></p>
            
                <?php if (!$is_own_profile): ?>
                    <form action="follow.php" method="post">
                        <input type="hidden" name="follow_id" value="<?php echo $user_data['id']; ?>">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <button class="btn follow-btn <?php echo $is_following ? 'unfollow' : 'follow'; ?>" type="submit" name="action" value="<?php echo $is_following ? 'unfollow' : 'follow'; ?>">
                            <?php echo $is_following ? 'Following' : 'Follow'; ?>
                        </button>
                    </form>
                <?php endif; ?>
                
                <?php if ($is_own_profile): ?>
                    <form action="edit_profile.php" method="get">
                        <button type="submit" class="edit-profile-btn">Edit Profile</button>
                    </form>
                <?php endif; ?>
            </div>


        </div>
        <?php
            $all_posts = [];
            while ($post = $posts->fetch_assoc()) {
                $all_posts[] = $post;
            }
        ?>


        <div class="tabs-buttons">
            <button class="tab-btn active" data-tab="posts">Posts</button>
            <button class="tab-btn" data-tab="reels">Redacts</button>
            <button class="tab-btn" data-tab="tagged">Tagged</button>
        </div>

        <div class="profile-tab-content">
            <!-- Posts tab: show everything -->
            <div class="tab-content active" id="posts">
                <div class="profile-grid">
                    <?php foreach ($all_posts as $index => $post): ?>
                      <div class="grid-item" data-index="<?php echo $index; ?>">
                        <?php
                            $ext = strtolower(pathinfo($post['image_path'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                            <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                                <video autoplay loop muted>
                                    <source src="<?php echo htmlspecialchars($post['image_path']); ?>" type="video/<?php echo $ext; ?>">
                                </video>
                            <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                </div>
            </div>
                            
            <!-- Redacts tab: show videos only -->
            <div class="tab-content" id="reels">
                <div class="profile-grid">
                    <?php
                    $has_reels = false;
                    foreach ($all_posts as $post):
                        $ext = strtolower(pathinfo($post['image_path'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['mp4', 'webm', 'ogg'])):
                            $has_reels = true; ?>
                            <video autoplay loop muted>
                                <source src="<?php echo htmlspecialchars($post['image_path']); ?>" type="video/<?php echo $ext; ?>">
                            </video>
                        <?php endif;
                    endforeach;
                    if (!$has_reels): ?>
                        <p>No redacts yet.</p>
                    <?php endif; ?>
                </div>
            </div>
                    
            <!-- Tagged tab -->
            <div class="tab-content" id="tagged">
                <p>No tagged posts yet.</p>
            </div>
        </div>

        <!-- Followers Modal -->
        <div id="followers-modal" class="follow-modal hidden">
          <div class="follow-modal-content">
            <h3>Followers</h3>
            <button class="close-modal">&times;</button>
            <ul id="followers-list" class="follow-user-list"></ul>
          </div>
        </div>

        <!-- Following Modal -->
        <div id="following-modal" class="follow-modal hidden">
          <div class="follow-modal-content">
            <h3>Following</h3>
            <button class="close-modal">&times;</button>
            <ul id="following-list" class="follow-user-list"></ul>
          </div>
        </div>

        <!-- Post Display Modal -->
        <div id="post-modal" class="post-modal hidden">
          <span id="close-post" class="close-post">&times;</span>
          <div class="post-content-wrapper">
            <button id="prev-post">&larr;</button>
            <div id="post-content">
              <!-- JS will populate this -->
            </div>
            <button id="next-post">&rarr;</button>
          </div>
        </div>



    </div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    // Handle active tab button
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Handle tab content visibility
    const tabId = btn.getAttribute('data-tab');
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
      if (tab.id === tabId) {
        tab.classList.add('active');
      }
    });
  });
});


document.querySelectorAll('.follow-stats span').forEach(span => {
  span.addEventListener('click', () => {
    const type = span.textContent.includes('Followers') ? 'followers' : 'following';
    const modal = document.getElementById(`${type}-modal`);
    modal.classList.remove('hidden');

    fetch(`fetch_${type}.php?user=<?php echo urlencode($username); ?>`)
      .then(res => res.json())
      .then(data => {
        const list = document.getElementById(`${type}-list`);
        list.innerHTML = '';

        if (data.length === 0) {
          list.innerHTML = '<li>No users yet.</li>';
        } else {
          data.forEach(user => {
            const li = document.createElement('li');
            li.classList.add('follow-user-item');

            const profilePic = document.createElement('img');
            profilePic.src = user.profile_pic;
            profilePic.classList.add('follow-user-pic');

            const link = document.createElement('a');
            link.href = `profile.php?user=${encodeURIComponent(user.username)}`;
            link.textContent = '@' + user.username;
            link.classList.add('follow-user-link');

            const userInfo = document.createElement('div');
            userInfo.classList.add('follow-user-info');
            userInfo.appendChild(link);

            li.appendChild(profilePic);
            li.appendChild(userInfo);

            // Show follow/unfollow if not viewing own profile
            if (user.username !== "<?php echo $_SESSION['username']; ?>") {
              const followForm = document.createElement('form');
              followForm.method = 'post';
              followForm.action = 'follow.php';

              followForm.innerHTML = `
                <input type="hidden" name="follow_id" value="${user.id}">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <button type="submit" name="action" value="${user.is_following ? 'unfollow' : 'follow'}"
                  class="btn follow-btn ${user.is_following ? 'unfollow' : 'follow'}">
                  ${user.is_following ? 'Following' : 'Follow'}
                </button>
              `;
              li.appendChild(followForm);
            }

            list.appendChild(li);
          });
        }
      });
  });
});


document.querySelectorAll('.close-modal').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.closest('.follow-modal').classList.add('hidden');
  });
});

// Handle Post Display Modal
const allPosts = <?php echo json_encode($all_posts); ?>;
let currentIndex = 0;

const modal = document.getElementById('post-modal');
const postContent = document.getElementById('post-content');
const closeBtn = document.getElementById('close-post');
const nextBtn = document.getElementById('next-post');
const prevBtn = document.getElementById('prev-post');

function renderPost(index, direction = 'fade') {
  const post = allPosts[index];
  if (!post) return;

  const ext = post.image_path.split('.').pop().toLowerCase();
  const isImage = ext.match(/(jpg|jpeg|png|gif|webp)/);

  // Clear and reset animation
  postContent.innerHTML = '';
  postContent.style.animation = 'none';
  void postContent.offsetWidth;

  // Animation direction
  if (direction === 'left') {
    postContent.style.animation = 'slideLeft 0.3s ease';
  } else if (direction === 'right') {
    postContent.style.animation = 'slideRight 0.3s ease';
  } else {
    postContent.style.animation = 'fadeIn 0.3s ease';
  }

  const reelFrame = document.createElement('div');
  reelFrame.classList.add('reel-frame');
  reelFrame.dataset.postId = post.id;
  reelFrame.id = `post-${post.id}`;

  // Create media element
  const media = document.createElement(isImage ? 'img' : 'video');
  media.src = post.image_path;
  if (!isImage) {
    media.controls = true;
    media.autoplay = true;
    media.loop = true;
    media.muted = true;
  }
  media.classList.add('reel-video');

  // Overlay (username + caption)
  const overlay = document.createElement('div');
  overlay.classList.add('overlay');
  overlay.innerHTML = `
    <a href="profile.php?user=${encodeURIComponent(post.username)}">
      <h3>@${post.username}</h3>
    </a>
    <p>${post.content ? post.content.replace(/\n/g, '<br>') : ''}</p>
    <small>${post.created_at}</small>
  `;

  // Voting icons
  const upIcon = post.user_vote == 1 ? 'assets/images/upVote-arrow.png' : 'assets/images/up-arrow.png';
  const downIcon = post.user_vote == -1 ? 'assets/images/downVote-arrow.png' : 'assets/images/down-arrow.png';

  const voteContainer = document.createElement('div');
  voteContainer.classList.add('vote-container-loop');
  voteContainer.innerHTML = `
    <button class="vote" onclick="vote(${post.id}, 1)">
      <img src="${upIcon}" alt="Upvote" width="24" height="24">
    </button>

    <span class="vote-count" id="votes-${post.id}" width="24" height="24">${post.votes}</span>

    <button class="vote" onclick="vote(${post.id}, -1)">
      <img src="${downIcon}" alt="Downvote" width="24" height="24">
    </button>

    <button class="vote" onclick="toggleCommentPanel(${post.id})">
      <img src="assets/images/comment.png" alt="Comment" width="24" height="24">
    </button>
  `;

  // Comment panel wrapper (can be loaded later)
  const commentPanel = document.createElement('div');
  commentPanel.className = 'comment-panel';
  commentPanel.id = `comment-panel-${post.id}`;
  commentPanel.innerHTML = `
    <h4>Comments:</h4>

    <button class="close-btn" onclick="closeCommentPanel(${post.id})">X</button>

    <div id="comment-list-${post.id}" class="comment-list"></div>
    
    <form class="comment-input" onsubmit="submitComment(event, ${post.id})">
      <input type="text" name="content" placeholder="Write a comment..." required>
      <button type="submit">Post Comment</button>
    </form>
  `;

  // Append everything
  reelFrame.appendChild(media);
  reelFrame.appendChild(overlay);
  reelFrame.appendChild(voteContainer);
  reelFrame.appendChild(commentPanel);

  postContent.appendChild(reelFrame);

  loadComments(post.id); // Load comments when modal opens
}




// Handle opening post modal
document.querySelectorAll('.grid-item').forEach(item => {
  item.addEventListener('click', () => {
    currentIndex = parseInt(item.getAttribute('data-index'));
    renderPost(currentIndex);
    modal.classList.remove('hidden');
  });
});

closeBtn.addEventListener('click', () => {
  modal.classList.add('hidden');
  postContent.innerHTML = '';
});

nextBtn.addEventListener('click', () => {
  if (currentIndex < allPosts.length - 1) {
    currentIndex++;
    renderPost(currentIndex, 'left');
  }
});

prevBtn.addEventListener('click', () => {
  if (currentIndex > 0) {
    currentIndex--;
    renderPost(currentIndex, 'right');
  }
});

document.addEventListener('keydown', (e) => {
  if (!modal.classList.contains('hidden')) {
    if (e.key === 'ArrowRight') nextBtn.click();
    if (e.key === 'ArrowLeft') prevBtn.click();
    if (e.key === 'Escape') closeBtn.click();
  }
});


function preloadAdjacent(index) {
  [index - 1, index + 1].forEach(i => {
    if (allPosts[i]) {
      const ext = allPosts[i].image_path.split('.').pop().toLowerCase();
      const el = document.createElement(ext.match(/(jpg|jpeg|png|gif|webp)/) ? 'img' : 'video');
      el.src = allPosts[i].image_path;
    }
  });
}


</script>

<?php include 'settings.php'; ?>

<script>

    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reel-video').forEach(video => {
        video.pause();
        video.currentTime = 0; // Optional: reset to beginning if needed
    });
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
      // Update vote count
      const voteCountElem = document.getElementById(`votes-${postId}`);
      if (voteCountElem) voteCountElem.textContent = data.vote_count;

      const container = document.querySelector(`[data-post-id="${postId}"]`) || document.getElementById(`post-${postId}`);
      if (!container) return;

      const upImg = container.querySelector('button.vote:nth-of-type(1) img');
      const downImg = container.querySelector('button.vote:nth-of-type(2) img');

      if (data.user_vote == 1) {
        if (upImg) upImg.src = "assets/images/upVote-arrow.png";
        if (downImg) downImg.src = "assets/images/down-arrow.png";
      } else if (data.user_vote == -1) {
        if (upImg) upImg.src = "assets/images/up-arrow.png";
        if (downImg) downImg.src = "assets/images/downVote-arrow.png";
      } else {
        if (upImg) upImg.src = "assets/images/up-arrow.png";
        if (downImg) downImg.src = "assets/images/down-arrow.png";
      }

      // Optional: update cached post in allPosts
      const postIndex = allPosts.findIndex(p => p.id == postId);
      if (postIndex !== -1) {
        allPosts[postIndex].votes = data.vote_count;
        allPosts[postIndex].user_vote = data.user_vote;
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


// Toggle mute/unmute on video click
document.querySelectorAll('video.reel-video').forEach(video => {
    video.addEventListener('click', () => {
        video.muted = !video.muted;
    });
});


</script>
</body>
</html>
