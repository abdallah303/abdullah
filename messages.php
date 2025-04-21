<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Load nurses (if patient) or load patients (if nurse)
$role = $_SESSION['role'];
$targetRole = $role === 'patient' ? 'nurse' : 'patient';

$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = ?");
$stmt->bind_param("s", $targetRole);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['receiver_id'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $receiverId, $message);
    if ($stmt->execute()) {
        echo "Message sent.";
    }
    exit();
}

// Fetch messages
$messages = [];
foreach ($users as $user) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at");
    $stmt->bind_param("iiii", $userId, $user['id'], $user['id'], $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
</head>
<body>
    <h2>Messages</h2>
    
    <form id="messageForm">
        <label for="receiver">Select User:</label>
        <select name="receiver_id" id="receiver" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <textarea name="message" required></textarea><br>
        <button type="submit">Send Message</button>
    </form>

    <div id="chat">
        <?php foreach ($messages as $message): ?>
            <p><strong><?php echo $message['sender_id'] === $userId ? 'You' : $message['receiver_id']; ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
        <?php endforeach; ?>
    </div>

    <script>
        document.getElementById('messageForm').onsubmit = function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            fetch('messages.php', {
                method: 'POST',
                body: formData,
            }).then(response => {
                return response.text();
            }).then(data => {
                console.log(data);
                window.location.reload(); // Reloads to get updated messages
            });
        };
    </script>
</body>
</html>