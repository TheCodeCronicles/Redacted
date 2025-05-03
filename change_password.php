<?php
session_start();
require_once 'db/db.php';

$response = ["success" => false, "message" => ""];

if (!isset($_SESSION['user_id'])) {
    $response["message"] = "You must be logged in to change your password.";
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        $response["message"] = "New passwords do not match.";
        echo json_encode($response);
        exit();
    }

    // Fetch user info
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['password'])) {
            // Hash new password and update it
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedNewPassword, $userId);

            if ($updateStmt->execute()) {
                $response["success"] = true;
                $response["message"] = "Password updated successfully.";
            } else {
                $response["message"] = "Failed to update password. Please try again.";
            }
        } else {
            $response["message"] = "Current password is incorrect.";
        }
    } else {
        $response["message"] = "User not found.";
    }

    echo json_encode($response);
    exit();
}
?>
