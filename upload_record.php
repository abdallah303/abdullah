<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["record"]["name"]);
    move_uploaded_file($_FILES["record"]["tmp_name"], $target_file);

    // Inserting the record file path into the database
    $stmt = $conn->prepare("INSERT INTO health_records (user_id, record_file) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $target_file);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Record uploaded successfully!";
    } else {
        $_SESSION['error'] = "Failed to upload record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Medical Record</title>
</head>
<body>
    <h2>Upload Medical Record</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="record" required>
        <button type="submit">Upload</button>
    </form>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
    <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
</body>
</html>