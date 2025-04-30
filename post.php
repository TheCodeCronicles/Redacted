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
    $image_path = null;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Make uploads folder if missing
        }

        $file_name = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (!empty($content) || $image_path !== null) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $content, $image_path);

        if ($stmt->execute()) {
            header("Location: feed.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Post cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="userforms">
        <form method="POST" action="" enctype="multipart/form-data">
            <h1>Create a Post</h1>
            <textarea name="content" rows="5" placeholder="What's happening?" required></textarea><br><br>
            <input type="file" name="image" accept="image/*"><br><br>
            <button class="btn" type="submit">Post</button>
        </form>
        <p><a href="feed.php">Back to Feed</a></p>
    </div>
    
</body>
</html>
