<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Use prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { 
        // For now, we redirect with a success status.
        header("Location: index.php?reset=sent");
    } else {
        header("Location: index.php?error=email_not_found");
    }
    exit();
}
?>