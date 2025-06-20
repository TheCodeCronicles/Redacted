<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, bio, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $bio = trim($_POST['bio']);

    // Check username uniqueness
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
    $check->bind_param("si", $newUsername, $userId);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errors[] = "That username is already taken.";
    }

    // Handle profile image upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = 'uploads/avatars/';
        $fileTmp = $_FILES['profile_pic']['tmp_name'];
        $fileExt = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Invalid file type.";
        } else {
            $newName = $uploadDir . uniqid() . '.' . $fileExt;
            if (!move_uploaded_file($fileTmp, $newName)) {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET username=?, bio=?";
        $params = [$newUsername, $bio];
        $types = "ss";
        if (isset($newName)) {
            $sql .= ", profile_pic=?";
            $params[] = $newName;
            $types .= "s";
        }
        $sql .= " WHERE id=?";
        $params[] = $userId;
        $types .= "i";

        $upd = $conn->prepare($sql);
        $upd->bind_param($types, ...$params);
        if ($upd->execute()) {
            $_SESSION['username'] = $newUsername;
            $success = true;
            header("Location: profile.php?user=" . urlencode($newUsername));
            exit();
        } else {
            $errors[] = "Database error.";
        }
    }
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .valid { color: green; }
    .invalid { color: red; }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="userforms-edit">
    <h2>Edit Profile</h2>
    <?php if ($errors): ?>
      <div class="errors"><?= implode("<br>", $errors) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="form-wrapper">

        <!-- Profile Picture Preview -->        
        <div class="input-container" style="text-align: center;">
          <img src="<?=htmlspecialchars($user['profile_pic'])?>" alt="Current Profile Picture" class="profile-preview" style="max-width: 120px; border-radius: 50%; margin-bottom: 10px;">
          <p style="color: #ccc; font-size: 12px;">Current Profile Picture</p>
        </div>
        
        <!-- Profile Picture Upload -->
        <div class="input-container">
          <input type="file" name="profile_pic" id="profile_pic" class="form-input" accept="image/*">
          <label for="profile_pic" class="form-label">Change Profile Picture</label>
        </div>

        <!-- Username -->
        <div class="input-container">
          <input type="text" name="username" id="username" class="form-input" value="<?=htmlspecialchars($user['username'])?>" required>
          <label for="username" class="form-label">Username</label><br>
          <span id="uname-msg" class="input-feedback"></span>
        </div>

        <!-- Bio -->
        <div class="input-container">
          <input name="bio" id="bio" class="form-input" rows="4" value="<?=htmlspecialchars($user['bio'])?>"><br>
          <label for="bio" class="form-label">Bio</label>
        </div><br>

        <!-- Submit -->
        <div class="input-container">
          <button type="submit" class="form-button">Save Changes</button>
        </div>

    </form>

  </div>

  <!-- Username live-check JS -->
<script>
let typingTimer;
const unameInput = document.getElementById('username');
const feedback = document.getElementById('uname-msg');
const saveBtn = document.querySelector('.form-button');

let isFormatValid = false;
let isUsernameAvailable = true; // Assume true initially (current username)

function validateForm() {
  saveBtn.disabled = !(isFormatValid && isUsernameAvailable);
}

unameInput.addEventListener('input', function () {
  clearTimeout(typingTimer);
  const val = this.value.trim();

  feedback.textContent = '';
  feedback.classList.remove('valid', 'invalid');

  // Format validation
  if (!val.match(/^[a-zA-Z0-9_]{3,30}$/)) {
    feedback.textContent = "3-30 chars, letters/numbers/_ only.";
    feedback.classList.add('invalid');
    isFormatValid = false;
    isUsernameAvailable = false;
    validateForm();
    return;
  }

  isFormatValid = true;

  typingTimer = setTimeout(() => {
    fetch(`check_username.php?u=${encodeURIComponent(val)}`)
      .then(res => res.json())
      .then(data => {
        if (data.available) {
          feedback.textContent = "✔ Available";
          feedback.classList.add('valid');
          feedback.classList.remove('invalid');
          isUsernameAvailable = true;
        } else {
          feedback.textContent = "✖ Taken";
          feedback.classList.add('invalid');
          feedback.classList.remove('valid');
          isUsernameAvailable = false;
        }
        validateForm();
      })
      .catch(() => {
        feedback.textContent = "Error checking.";
        feedback.classList.add('invalid');
        isUsernameAvailable = false;
        validateForm();
      });
  }, 500);
});
</script>


  <?php include 'settings.php'; ?>
</body>
</html>

