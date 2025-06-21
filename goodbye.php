<?php
// No need to start session — it's already destroyed on delete
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Goodbye - [REDACTED]</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #111;
            color: #f5f5f5;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        .goodbye-container {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
        }
        .goodbye-container h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .goodbye-container p {
            font-size: 1.2em;
            color: #ccc;
        }
        .return-home {
            margin-top: 30px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #444;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .return-home:hover {
            background-color: #666;
        }
    </style>
</head>
<body>
    <div class="goodbye-container">
        <h1>Goodbye!</h1>
        <p>Your account has been successfully deleted.</p>
        <p>Thank you for being part of the community.</p>
        <a href="login.php" class="return-home">Return to Home</a>
    </div>
</body>
</html>
