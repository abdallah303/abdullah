<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch available nurses
$stmt = $conn->prepare("SELECT n.id, u.username FROM nurses n INNER JOIN users u ON n.user_id = u.id");
$stmt->execute();
$result = $stmt->get_result();

$nurses = [];
while ($row = $result->fetch_assoc()) {
    $nurses[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nurseId = $_POST['nurse_id'];
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, nurse_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $userId, $nurseId, $appointmentDate, $appointmentTime);

    if ($stmt->execute()) {
        $appointmentId = $conn->insert_id; // Capture the last insert ID for payment

        // Redirect to payment page
        header("Location: payment.php?appointment_id=$appointmentId");
    } else {
        $_SESSION['error'] = "Failed to schedule appointment.";
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment</title>
</head>
<body>
    <h2>Schedule Appointment</h2>
    <form method="post">
        <label for="nurse">Select Nurse:</label>
        <select name="nurse_id" id="nurse" required>
            <?php foreach ($nurses as $nurse): ?>
                <option value="<?php echo $nurse['id']; ?>"><?php echo $nurse['username']; ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="appointment_date">Date:</label>
        <input type="date" name="appointment_date" id="appointment_date" required><br>

        <label for="appointment_time">Time:</label>
        <input type="time" name="appointment_time" id="appointment_time" required><br>

        <button type="submit">Schedule Appointment</button>
    </form>
    <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
</body>
</html>