<?php
// profile.php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role']; // Get the role from the session

// Fetch user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
</head>
<body>
    <h2>Your Profile</h2>
    <h3>Username: <?php echo htmlspecialchars($username); ?></h3>
    <h3>Email: <?php echo htmlspecialchars($email); ?></h3>
    <h3>Role: <?php echo htmlspecialchars($role); ?></h3>

    <h4>Welcome, <?php echo htmlspecialchars($role); ?></h4>
    
    <?php if ($role === 'nurse'): ?>
        <h4> Nurse Dashboard </h4>
        <p>This is the area where you can manage your patients.</p>
        <!-- Add more nurse-specific functionality here -->
    <?php elseif ($role === 'patient'): ?>
        <h4> Patient Dashboard </h4>
        <p>This is where you can see your medical records.</p>
        <!-- Add more patient-specific functionality here -->
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>