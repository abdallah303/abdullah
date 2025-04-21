<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch users
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Fetch appointments
$stmt = $conn->prepare("SELECT a.id, u.username, a.appointment_date, a.appointment_time, n.id AS nurse_id FROM appointments a JOIN users u ON a.patient_id = u.id JOIN nurses n ON a.nurse_id = n.id");
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <h3>Users</h3>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?php echo htmlspecialchars($user['username']) . ' (' . $user['role'] . ')'; ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Appointments</h3>
    <table>
        <tr>
            <th>Patient</th>
            <th>Nurse</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
        <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?php echo htmlspecialchars($appointment['username']); ?></td>
                <td><?php echo htmlspecialchars($appointment['nurse_id']); ?></td>
                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>