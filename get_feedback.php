<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Include database connection
include 'db.php';

// Check if connection exists
if (!$conn) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit;
}

// Get parameters
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Simple query to fetch feedback
$sql = "SELECT id, full_name, email, subject, message, rating, created_at 
        FROM feedback 
        WHERE status = 'Approved' OR status IS NULL
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        'success' => false, 
        'error' => 'Query failed: ' . mysqli_error($conn)
    ]);
    exit;
}

$feedback = [];
while ($row = mysqli_fetch_assoc($result)) {
    $feedback[] = [
        'id' => $row['id'],
        'full_name' => htmlspecialchars($row['full_name']),
        'email' => htmlspecialchars($row['email']),
        'subject' => htmlspecialchars($row['subject']),
        'message' => htmlspecialchars($row['message']),
        'rating' => $row['rating'],
        'created_at' => date('M d, Y', strtotime($row['created_at']))
    ];
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM feedback";
$count_result = mysqli_query($conn, $count_sql);
$total_row = mysqli_fetch_assoc($count_result);
$total = $total_row['total'];

echo json_encode([
    'success' => true,
    'feedback' => $feedback,
    'total' => $total,
    'limit' => $limit,
    'offset' => $offset
]);

mysqli_close($conn);
?>
