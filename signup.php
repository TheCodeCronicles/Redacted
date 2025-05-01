<?php
session_start();
require_once 'db/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "❌ Passwords do not match. Please try again.";
    } else {
        // Check username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "❌ Username already taken. Try something else.";
        } else {
            // Check email
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "❌ Email already registered. Try logging in.";
            } else {
                if (!preg_match('/^[a-zA-Z0-9._]+$/', $username)) {
                   $error = "❌ Username can only contain letters, numbers, underscores _ , and periods . with no spaces.";
                } elseif (strlen($username) > 30) {   
                    $error = "❌ Username cannot exceed 30 characters.";
                } else {
                    // Hash password and insert
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $email, $hashed_password);

                    if ($stmt->execute()) {
                        $_SESSION['user_id'] = $stmt->insert_id;
                        $_SESSION['username'] = $username;
                        header("Location: feed.php");
                        exit();
                    } else {
                        $error = "❌ Something went wrong. Please try again.";
                    }
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="userforms">
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <h1>Sign Up to [REDACTED]</h1>

            <div class="input-container">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <span id="username-feedback" class="input-feedback"></span>
            </div>

            <div class="input-container">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-container">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-container">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button class="btn" type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
    <script>
        const usernameInput = document.getElementById('username');
        const feedback = document.getElementById('username-feedback');

        usernameInput.addEventListener('input', () => {
            const value = usernameInput.value;
            const regex = /^[a-zA-Z0-9._]+$/;

            if (value.length === 0) {
                feedback.textContent = '';
                feedback.style.color = '';
            } else if (!regex.test(value)) {
                feedback.textContent = '❌ Only letters, numbers, underscores (_) and periods (.) allowed.';
                feedback.style.color = 'red';
            } else if (value.length > 30) {
                feedback.textContent = `❌ Too long (${value.length}/30)`;
                feedback.style.color = 'red';
            } else {
                feedback.textContent = `✅ Looks good (${value.length}/30)`;
                feedback.style.color = 'green';
            }
        });
    </script>
</body>

</html>
