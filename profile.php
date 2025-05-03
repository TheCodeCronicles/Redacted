<?php
session_start();
require_once 'db/db.php';

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
$post_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$post_stmt->bind_param("i", $user_data['id']);
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
                    <?php foreach ($all_posts as $post): ?>
                        <?php
                            $ext = strtolower(pathinfo($post['image_path'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                            <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                                <video autoplay loop muted>
                                    <source src="<?php echo htmlspecialchars($post['image_path']); ?>" type="video/<?php echo $ext; ?>">
                                </video>
                            <?php endif; ?>
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

</script>

<?php include 'settings.php'; ?>

</body>
</html>
