<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// If JSON parsing failed, try regular POST
if (!$data) {
    $data = $_POST;
}

// If still no data, return error
if (!$data || empty($data)) {
    $response['message'] = 'No data received';
    echo json_encode($response);
    exit;
}

// Extract data
$name = isset($data['name']) ? trim($data['name']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$subject = isset($data['subject']) ? trim($data['subject']) : 'General Inquiry';
$message = isset($data['message']) ? trim($data['message']) : '';
$rating = isset($data['rating']) ? (int)$data['rating'] : null;

// Validation
if (empty($name)) {
    $response['message'] = 'Please enter your name';
    echo json_encode($response);
    exit;
}

if (empty($email)) {
    $response['message'] = 'Please enter your email';
    echo json_encode($response);
    exit;
}

if (empty($message)) {
    $response['message'] = 'Please enter your message';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address';
    echo json_encode($response);
    exit;
}

// Insert into database (without user_id since it might not exist)
$sql = "INSERT INTO feedback (full_name, email, subject, message, rating, status) 
        VALUES (?, ?, ?, ?, ?, 'Approved')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $email, $subject, $message, $rating);

if ($stmt->execute()) {
    $feedback_id = $stmt->insert_id;
    
    $response['success'] = true;
    $response['message'] = 'Thank you for your feedback!';
    $response['feedback'] = [
        'id' => $feedback_id,
        'full_name' => htmlspecialchars($name),
        'email' => htmlspecialchars($email),
        'subject' => htmlspecialchars($subject),
        'message' => htmlspecialchars($message),
        'rating' => $rating,
        'created_at' => date('Y-m-d H:i:s')
    ];
} else {
    $response['message'] = 'Database error: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
