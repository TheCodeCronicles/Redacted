<!-- settings_button.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Button -->
<button class="settings-btn" onclick="toggleSettings()">
    <span>&#9881;</span>
</button>

<!-- Modal -->
<div class="settings-modal" id="settingsModal">
    <h3>Settings</h3>
    <form action="update_password.php" method="POST">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Change Password</button>
    </form>
    <form action="delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
        <button type="submit" class="danger">Delete Account</button>
    </form>
</div>

<script>
function toggleSettings() {
    const modal = document.getElementById("settingsModal");
    modal.style.display = modal.style.display === "block" ? "none" : "block";
}
</script>
