<?php
// nurse_profile.php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle form submission to add/update nurse details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qualifications = $_POST['qualifications'];
    $experience = $_POST['experience'];
    $specialties = $_POST['specialties'];

    // Check if the nurse's profile already exists
    $stmt = $conn->prepare("SELECT id FROM nurses WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing profile
        $stmt->close();
        $stmt = $conn->prepare("UPDATE nurses SET qualifications = ?, experience = ?, specialties = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $qualifications, $experience, $specialties, $userId);
    } else {
        // Insert new profile
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO nurses (user_id, qualifications, experience, specialties) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $qualifications, $experience, $specialties);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Nurse profile updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update nurse profile.";
    }
    $stmt->close();
}

// Retrieve existing nurse profile if available
$stmt = $conn->prepare("SELECT qualifications, experience, specialties FROM nurses WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($qualifications, $experience, $specialties);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Profile</title>
</head>
<body>
    <h2>Nurse Profile</h2>
    <form method="post" action="">
        Qualifications: <textarea name="qualifications" required><?php echo htmlspecialchars($qualifications); ?></textarea><br>
        Experience: <textarea name="experience" required><?php echo htmlspecialchars($experience); ?></textarea><br>
        Specialties: <textarea name="specialties" required><?php echo htmlspecialchars($specialties); ?></textarea><br>
        <button type="submit">Save Profile</button>
    </form>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
    <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
</body>
</html>