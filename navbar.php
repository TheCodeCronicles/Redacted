<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
/* === Navbar Styling === */
.navbar {
    position: fixed;
    top: 50%;
    left: 20px;
    transform: translateY(-50%);
    background: #1e1e1e;
    padding: 20px 10px;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    border: 1px solid #333;
    align-items: center; /* Align contents to the center */
    width: 150px; /* Navbar width when expanded */
    transition: all 0.5s ease; /* Smooth transition for collapsing and expanding */
}

/* Navbar when collapsed (only logo visible) */
.navbar.collapsed {
    width: 60px; /* Shrink width to logo size */
    padding: 0; /* Remove padding when collapsed */
    gap: 0; /* Remove gap between elements */
    border-radius: 50%; /* Make the navbar rounded when collapsed */
}

/* Navbar Logo Circle with Image */
.navbar-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: white; /* White circle */
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); /* Slight shadow */
    transition: transform 0.3s ease, margin-top 0.5s ease; /* Smooth transform for hover and top movement */
    margin-bottom: 0; /* No margin when collapsed */
    overflow: hidden; /* Ensures the image fits inside the circle */
    cursor: pointer; /* Indicate it's clickable */
}

/* Navbar Logo Image */
.navbar-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image covers the circle */
}

/* Hover effect for logo */
.navbar-logo:hover {
    transform: scale(1.1); /* Slightly enlarge logo on hover */
}

/* Styling for Navbar Links */
.navbar a {
    color: #aaa;
    text-decoration: none;
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 10px;
    transition: background 0.2s, color 0.2s;
    text-align: center;
    width: 100%; /* Ensures the links span the full width */
    display: flex;
    align-items: center; /* Vertically center the text and icon */
    justify-content: flex-start; /* Align the text to the left */
}

/* Add space between icon and text */
.navbar a img {
    width: 20px; /* Icon size */
    height: 20px;
    margin-right: 10px; /* Space between icon and text */
}

/* Active state for links */
.navbar a.active {
    background: #1138cc;
    color: white;
}

/* Hover effect for links */
.navbar a:hover {
    background: #333;
    color: white;
}

/* Hide links when navbar is collapsed */
.navbar.collapsed a {
    display: none; /* Hide links in collapsed state */
}
</style>

<div class="navbar">
    <div class="navbar-logo" onclick="toggleNavbar()">
        <!-- Image for Logo -->
        <img src="assets/images/Redacted_Logo.png" alt="Logo">
    </div>
    <a href="feed.php" class="<?= $currentPage == 'feed.php' ? 'active' : '' ?>">
        <!-- House icon for Feed -->
        <img src="assets/images/home_icon.png" alt="House Icon">
        Feed
    </a>
    <a href="loop.php" class="<?= $currentPage == 'loop.php' ? 'active' : '' ?>">
        <!-- Some icon for Redacted (replace with appropriate icon) -->
        <img src="assets/images/redacts_icon.png" alt="Loop Icon">
        [REDACTS]
    </a>
</div>

<script>
    // Function to toggle the navbar collapse
    function toggleNavbar() {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('collapsed'); // Toggle the 'collapsed' class on click
    }
</script>
