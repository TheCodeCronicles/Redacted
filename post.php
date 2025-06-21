<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $media_path = null;
    $topics = isset($_POST['topics']) ? explode(',', strtolower(trim($_POST['topics']))) : [];

    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_tmp = $_FILES["media"]["tmp_name"];
        $file_type = mime_content_type($file_tmp);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];

        if (in_array($file_type, $allowed_types)) {
            $file_name = uniqid() . "_" . basename($_FILES["media"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $media_path = $target_file;
            }
        } else {
            echo "Unsupported file type.";
            exit();
        }
    }

if ($media_path !== null) {
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $content, $media_path);

    if ($stmt->execute()) {
        $post_id = $stmt->insert_id;

        // Handle topics
        if (!empty($_POST['topics'])) {
            // Parse tags: strip #, trim, lowercase, remove illegal chars
            $rawTags = explode(',', $_POST['topics']);
            $topics = [];

            foreach ($rawTags as $tag) {
                $tag = trim(strtolower($tag));
                $tag = ltrim($tag, '#'); // Remove # prefix if included
                $tag = preg_replace('/[^a-z0-9_]/', '', $tag); // Remove spaces/symbols

                if (!$tag) continue;
                $topics[] = $tag;
            }

            foreach ($topics as $tag) {
                // Check if topic exists
                $check = $conn->prepare("SELECT id FROM topics WHERE name = ?");
                $check->bind_param("s", $tag);
                $check->execute();
                $result = $check->get_result();

                if ($row = $result->fetch_assoc()) {
                    $topic_id = $row['id'];
                } else {
                    // Create new topic
                    $insert = $conn->prepare("INSERT INTO topics (name) VALUES (?)");
                    $insert->bind_param("s", $tag);
                    $insert->execute();
                    $topic_id = $insert->insert_id;
                }

                // Link to post
                $link = $conn->prepare("INSERT INTO post_topics (post_id, topic_id) VALUES (?, ?)");
                $link->bind_param("ii", $post_id, $topic_id);
                $link->execute();
            }
        }

        header("Location: feed.php");
        exit();
    } else {
        echo "Database error: " . $stmt->error;
    }
} else {
    echo "<div class='tip'>You must upload an image or video.</div>";
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        #media-warning {
          transition: opacity 0.3s ease;
        }

    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="userforms">
        <form method="POST" action="" enctype="multipart/form-data">
            <h1>Create a Post</h1>
            <textarea name="content" rows="5" placeholder="What's happening?" required></textarea><br><br>
            <input type="file" name="media" accept="image/*,video/*">
            <p id="media-warning" style="display:none; color: #ff6b6b; font-size: 14px; margin-top: 5px;">
              Please upload an image or video to continue.
            </p>

            <div class="input-container">
              <label for="topic-input" class="form-label">Topics (press Enter to add)</label>
              <input type="text" id="topic-input" class="form-input" placeholder="e.g. meme, quote" autocomplete="off">
              <div id="tag-container" class="tag-container"></div>
              <input type="hidden" name="topics" id="topics-hidden">
            </div>

            <button class="btn" type="submit">Post</button>
        </form>
    </div>
    
    <?php include 'settings.php'; ?>
    
<script>
document.querySelector('form').addEventListener('submit', function(e) {
  const fileInput = document.querySelector('input[name="media"]');
  const warning = document.getElementById('media-warning');

  if (!fileInput.files.length) {
    e.preventDefault();
    warning.style.display = 'block';
  } else {
    warning.style.display = 'none'; // Hide warning if fixed
  }
});

  const topicInput = document.getElementById('topic-input');
  const tagContainer = document.getElementById('tag-container');
  const hiddenInput = document.getElementById('topics-hidden');

  let topics = [];

  topicInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
      e.preventDefault();
      let raw = topicInput.value.trim();

      // Clean and normalize
      if (raw) {
        let cleaned = raw
          .toLowerCase()
          .replace(/^#+/, '')         // Remove starting #
          .replace(/\s+/g, '_')       // Replace spaces with _
          .replace(/[^a-z0-9_]/g, ''); // Remove invalid characters

        if (cleaned && !topics.includes(cleaned)) {
          topics.push(cleaned);
          renderTags();
        }
      }
      topicInput.value = '';
    }
  });

  function renderTags() {
    tagContainer.innerHTML = '';
    topics.forEach(topic => {
      const span = document.createElement('span');
      span.className = 'tag-pill';
      span.innerHTML = `#${topic} <span class="remove-tag" data-tag="${topic}">×</span>`;
      tagContainer.appendChild(span);
    });

    hiddenInput.value = topics.join(',');
  }

  tagContainer.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-tag')) {
      const tag = e.target.dataset.tag;
      topics = topics.filter(t => t !== tag);
      renderTags();
    }
  });

</script>

</body>
</html>