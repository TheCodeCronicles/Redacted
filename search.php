<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$searchResults = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['query'])) {
    $query = trim($_POST['query']);

    if (!empty($query)) {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE ?");
        $likeQuery = "%" . $query . "%";
        $stmt->bind_param("s", $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row['username'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="userforms">
        <form method="POST" action="">
            <h1>Search Users or Tags</h1>
            <input type="text" name="query" placeholder="Search for a username or hashtag..." required>
            <button class="btn" type="submit">Search</button>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <div class="search-results">
                <h2>Results:</h2>
                <?php if (empty($searchResults)): ?>
                    <p>No users found.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($searchResults as $username): ?>
                            <li><a href="profile.php?user=<?= urlencode($username) ?>"><?= htmlspecialchars($username) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'settings.php'; ?>

</body>
</html>