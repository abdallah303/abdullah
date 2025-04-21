<?php
// patient_history.php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle form submission to add/update medical history/preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicalHistory = $_POST['medical_history'];
    $preferences = $_POST['preferences'];

    // Check if the patient's medical history already exists
    $stmt = $conn->prepare("SELECT id FROM patient_medical_history WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing medical history
        $stmt->close();
        $stmt = $conn->prepare("UPDATE patient_medical_history SET medical_history = ?, preferences = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $medicalHistory, $preferences, $userId);
    } else {
        // Insert new medical history
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO patient_medical_history (user_id, medical_history, preferences) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $medicalHistory, $preferences);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Medical history updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update medical history.";
    }
    $stmt->close();
}

// Retrieve existing medical history if available
$stmt = $conn->prepare("SELECT medical_history, preferences FROM patient_medical_history WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($medicalHistory, $preferences);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Medical History</title>
</head>
<body>
    <h2>Patient Medical History</h2>
    <form method="post" action="">
        Medical History: <textarea name="medical_history" required><?php echo htmlspecialchars($medicalHistory); ?></textarea><br>
        Preferences: <textarea name="preferences" required><?php echo htmlspecialchars($preferences); ?></textarea><br>
        <button type="submit">Save Medical History</button>
    </form>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
    <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
</body>
</html>