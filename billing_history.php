<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch billing history
$stmt = $conn->prepare("SELECT bh.amount, bh.payment_status, bh.payment_date, a.appointment_date, a.appointment_time FROM billing_history bh INNER JOIN appointments a ON bh.appointment_id = a.id WHERE bh.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing History</title>
</head>
<body>
    <h2>Billing History</h2>
    <table>
        <tr>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Date</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($transaction['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                <td><?php echo htmlspecialchars($transaction['payment_status']); ?></td>
                <td><?php echo htmlspecialchars($transaction['payment_date']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>