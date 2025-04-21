<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $subject, $message);
    if ($stmt->execute()) {
        echo "Support ticket created.";
    }
    exit();
}

// Load open tickets
$stmt = $conn->prepare("SELECT * FROM support_tickets WHERE user_id = ? AND status = 'open'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Chat</title>
</head>
<body>
    <h2>Support Chat</h2>
    
    <form method="post">
        <input type="text" name="subject" required placeholder="Subject"><br>
        <textarea name="message" required></textarea><br>
        <button type="submit">Send</button>
    </form>

    <h3>Your Open Tickets</h3>
    <ul>
        <?php foreach ($tickets as $ticket): ?>
            <li><?php echo htmlspecialchars($ticket['subject']); ?> - <?php echo htmlspecialchars($ticket['message']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>