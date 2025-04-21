<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch usage statistics
$stmt = $conn->prepare("SELECT COUNT(*) AS total_usage FROM app_usage");
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$totalUsage = $data['total_usage'];

// Fetch nurse performance
$stmt = $conn->prepare("SELECT nurse_id, COUNT(*) AS appointments FROM appointments GROUP BY nurse_id");
$stmt->execute();
$result = $stmt->get_result();

$nursePerformance = [];
while ($row = $result->fetch_assoc()) {
    $nursePerformance[] = $row;
}

// Fetch patient satisfaction feedback (optional)
function logAppUsage($userId, $action, $conn) {
    $stmt = $conn->prepare("INSERT INTO app_usage (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $action);
    $stmt->execute();
}

logAppUsage($_SESSION['user_id'], 'User viewed appointments', $conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics and Reporting</title>
</head>
<body>
    <h2>Analytics and Reporting</h2>
    <h3>Total App Usage: <?php echo $totalUsage; ?></h3>
    
    <h3>Nurse Performance</h3>
    <table>
        <tr>
            <th>Nurse ID</th>
            <th>Appointments</th>
        </tr>
        <?php foreach ($nursePerformance as $performance): ?>
            <tr>
                <td><?php echo htmlspecialchars($performance['nurse_id']); ?></td>
                <td><?php echo htmlspecialchars($performance['appointments']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>