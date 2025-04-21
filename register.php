<?php
// register.php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role']; // Get role from the form

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful!";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Username already exists!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form method="post" action="">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Email: <input type="email" name="email" required><br>
        Role: 
        <select name="role" required>
            <option value="patient">Patient</option>
            <option value="nurse">Nurse</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
    <?php if (isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
</body>
</html>