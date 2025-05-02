<?php
session_start();
require_once 'db/db.php';

if (!isset($_GET['user'])) {
    header("Location: feed.php");
    exit();
}

$username = $_GET['user'];

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

// Fetch user posts
$post_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$post_stmt->bind_param("i", $user_data['id']);
$post_stmt->execute();
$posts = $post_stmt->get_result();
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
                <p><?php echo nl2br(htmlspecialchars($user_data['bio'] ?? '')); ?></p>
                <?php if ($is_own_profile): ?>
                    <a href="edit_profile.php" class="btn">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-tabs">
            <button class="tab-btn active" data-tab="posts">Posts</button>
            <button class="tab-btn" data-tab="reels">Reels</button>
            <button class="tab-btn" data-tab="tagged">Tagged</button>
        </div>

        <div class="profile-tab-content">
            <div class="tab-content active" id="posts">
                <div class="profile-grid">
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <?php
                            $ext = strtolower(pathinfo($post['image_path'], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="">
                            <?php elseif (in_array($ext, ['mp4', 'webm', 'ogg'])): ?>
                                <video autoplay loop muted>
                                    <source src="<?php echo htmlspecialchars($post['image_path']); ?>" type="video/<?php echo $ext; ?>">
                                </video>
                            <?php endif; ?>
                    <?php endwhile; ?>
                </div>
            </div>
                            
            <div class="tab-content" id="reels">
                <p>No reels yet.</p>
            </div>
                            
            <div class="tab-content" id="tagged">
                <p>No tagged posts yet.</p>
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
</script>


</body>
</html>
