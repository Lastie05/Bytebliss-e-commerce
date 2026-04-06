<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "Already subscribed! ⚡";
        } else {
            $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                echo "Welcome to the Bliss! 🚀";
            } else {
                echo "Error. Try again later.";
            }
        }
    } else {
        echo "Invalid email format.";
    }
}
?>