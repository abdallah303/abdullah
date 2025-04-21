<?php
session_start();
require 'config.php';
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('YOUR_STRIPE_SECRET_KEY');

if (!isset($_GET['appointment_id'])) {
    header("Location: profile.php");
    exit();
}

$appointmentId = $_GET['appointment_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = 50.00; // Example amount
    $token = $_POST['stripeToken'];

    try {
        $charge = \Stripe\Charge::create([
            'amount' => $amount * 100, // Amount in cents
            'currency' => 'usd',
            'description' => 'Appointment Payment',
            'source' => $token,
        ]);

        // Insert into billing_history
        $stmt = $conn->prepare("INSERT INTO billing_history (user_id, appointment_id, amount, payment_status) VALUES (?, ?, ?, 'paid')");
        $stmt->bind_param("iid", $_SESSION['user_id'], $appointmentId, $amount);
        $stmt->execute();

        $_SESSION['message'] = "Payment successful!";
        header("Location: profile.php");
    } catch (\Stripe\Exception\CardException $e) {
        $_SESSION['error'] = "Payment failed: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h2>Payment for Appointment</h2>
    <form action="" method="post" id="payment-form">
        <label for="card-element">Credit or debit card</label>
        <div id="card-element"><!-- A Stripe Element will be inserted here. --></div>
        <div id="card-errors" role="alert"></div>
        <button type="submit">Pay</button>
    </form>

    <script>
        // Create a Stripe client.
        var stripe = Stripe('YOUR_STRIPE_PUBLIC_KEY');
        // Create an instance of Elements.
        var elements = stripe.elements();
        // Create a card element.
        var card = elements.create('card');
        // Add an instance of the card Element into the `card-element` div.
        card.mount('#card-element');
        // Handle real-time validation errors from the card Element.
        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    </script>
</body>
</html>