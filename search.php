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
        // Updated SQL for AJAX response
        $stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE username LIKE ? LIMIT 10");
        $likeQuery = "%" . $query . "%";
        $stmt->bind_param("s", $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = [
            'username' => $row['username'],
            'profile_pic' => $row['profile_pic'] ?: 'assets/images/default-avatar.png'
        ];
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
            border-radius: 12px;
            width: 500px;
            max-height: 400px;
            margin-left: 30px;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Hide overflow for clean scrolling */
            position: relative;
            animation: fadeIn 0.3s ease;
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
            background-color: #ffffff;
            color: black;
            padding: 0 2px;
            border-radius: 2px;
        }

        .search-results ul {
            padding: 0;
            margin: 0;
        }

        .search-results li {
            padding: 6px;
        }

        .search-results li:last-child {
            border-bottom: none;
        }

        .search-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* Custom scrollbar for follow modal list */
        .search-list::-webkit-scrollbar {
            width: 6px;
        }

        .search-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .search-list::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .search-list::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* Optional: smooth scrolling experience */
        .search-list {
            scroll-behavior: smooth;
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
                    resultsContainer.innerHTML = '<ul class="search-list">' + data.map(user => {
                        const regex = new RegExp(`(${query})`, 'ig');
                        const highlighted = user.username.replace(regex, '<mark>$1</mark>');
                        return `
                          <li style="display: flex; align-items: center; margin-bottom: 10px;">
                            <a href="profile.php?user=${encodeURIComponent(user.username)}" style="display: flex; align-items: center; gap: 12px; color: #ffffff; text-decoration: none;">
                              <img src="${user.profile_pic}" alt="${user.username}"
                                   style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; margin-top: 0;">
                              <span style="line-height: 1; display: flex; align-items: center;">${highlighted}</span>
                            </a>
                          </li>
                        `;

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
