<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
.navbar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #1e1e1e;
    padding: 10px 20px;
    border-radius: 15px;
    display: flex;
    gap: 20px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    border: 1px solid #333;
}

.navbar a {
    color: #aaa;
    text-decoration: none;
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 10px;
    transition: background 0.2s, color 0.2s;
}

.navbar a.active {
    background: #1138cc;
    color: white;
}
</style>

<div class="navbar">
    <a href="feed.php" class="<?= $currentPage == 'feed.php' ? 'active' : '' ?>">Feed</a>
    <a href="loop.php" class="<?= $currentPage == 'loop.php' ? 'active' : '' ?>">[REDACTS]</a>
</div>
