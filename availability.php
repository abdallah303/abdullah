<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $available_days = $_POST['available_days']; // e.g. ["monday", "tuesday"]

    // Clear previous availability
    $conn->query("DELETE FROM nurse_availability WHERE nurse_id = $userId");

    foreach ($available_days as $day) {
        $stmt = $conn->prepare("INSERT INTO nurse_availability (nurse_id, day) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $day);
        $stmt->execute();
    }


    $_SESSION['message'] = "Availability updated!";
    
    function getNearbyNurses($patientLatitude, $patientLongitude, $range, $conn) {
        $stmt = $conn->prepare("SELECT id, user_id, latitude, longitude, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance FROM nurses HAVING distance < ? ORDER BY distance");
        $stmt->bind_param("ddds", $patientLatitude, $patientLongitude, $patientLatitude, $range);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Availability</title>
</head>
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}    
</script>
<body>

    <h2>Set Your Availability</h2>
    <form method="post">
        <label><input type="checkbox" name="available_days[]" value="monday"> Monday</label><br>
        <label><input type="checkbox" name="available_days[]" value="tuesday"> Tuesday</label><br>
        <label><input type="checkbox" name="available_days[]" value="wednesday"> Wednesday</label><br>
        <label><input type="checkbox" name="available_days[]" value="thursday"> Thursday</label><br>
        <label><input type="checkbox" name="available_days[]" value="friday"> Friday</label><br>
        <label><input type="checkbox" name="available_days[]" value="saturday"> Saturday</label><br>
        <label><input type="checkbox" name="available_days[]" value="sunday"> Sunday</label><br>
        <button type="submit">Update Availability</button>
        
        <!-- Inside availability.php -->
        <input type="text" id="latitude" name="latitude" placeholder="Latitude" required>
        <input type="text" id="longitude" name="longitude" placeholder="Longitude" required>
        <button type="button" onclick="getLocation()">Get My Location</button>
    </form>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>

</body>
</html>