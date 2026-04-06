<?php

session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use prepared statements to prevent SQL injection
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the password using password_verify() for security
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];

            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password. Please try again.'); window.location.href='index.php';</script>";
        }
    } else {
        // If email doesn't exist, prompt them to sign up
        echo "<script>alert('Account not found. Please sign up first.'); window.location.href='index.php';</script>";
    }
    
    $stmt->close();
}
?>