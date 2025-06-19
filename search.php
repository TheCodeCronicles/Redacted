<?php
session_start();
require_once 'db/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle AJAX request for live search
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $results = [];

    if (!empty($query)) {
        $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE ? LIMIT 10");
        $likeQuery = "%" . $query . "%";
        $stmt->bind_param("s", $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results[] = $row['username'];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .search-results {
            background: #111;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .search-results ul {
            list-style: none;
            padding-left: 0;
        }

        .search-results li a {
            color: lightblue;
            text-decoration: none;
        }

        .search-results li a:hover {
            text-decoration: underline;
        }

        mark {
            background-color: yellow;
            color: black;
            padding: 0 2px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="userforms">
        <form id="liveSearchForm" method="POST" action="">
            <h1>Search Users</h1>
            <input type="text" name="query" id="liveSearchInput" placeholder="Search for a username..." autocomplete="off" required>
        </form>

        <div class="search-results" id="liveResults" style="display:none;"></div>
    </div>

    <?php include 'settings.php'; ?>

    <script>
    document.getElementById('liveSearchInput').addEventListener('input', function () {
        const query = this.value.trim();
        const resultsContainer = document.getElementById('liveResults');

        if (query.length === 0) {
            resultsContainer.innerHTML = '';
            resultsContainer.style.display = 'none';
            return;
        }

        fetch(`search.php?ajax=true&query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    const regex = new RegExp(`(${query})`, 'ig');
                    resultsContainer.innerHTML = '<ul>' + data.map(username => {
                        const highlighted = username.replace(regex, '<mark>$1</mark>');
                        return `<li><a href="profile.php?user=${encodeURIComponent(username)}">${highlighted}</a></li>`;
                    }).join('') + '</ul>';
                } else {
                    resultsContainer.innerHTML = '<p>No users found.</p>';
                }
                resultsContainer.style.display = 'block';
            })
            .catch(err => {
                resultsContainer.innerHTML = '<p>Error fetching results.</p>';
                resultsContainer.style.display = 'block';
            });
    });
    </script>
</body>
</html>
