<?php
session_start();
require_once 'db/db.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['u'])) exit;

$username = trim($_GET['u']);
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
echo json_encode(['available' => $res->num_rows === 0 || $res->fetch_assoc()['id'] == $_SESSION['user_id']]);
