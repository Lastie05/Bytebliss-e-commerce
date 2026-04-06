<?php
include 'db.php';
header('Content-Type: application/json');

$order_id = $_GET['order_id'];

// Check the transaction table for this specific order
$stmt = $conn->prepare("SELECT status, receipt_number FROM mpesa_transactions WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if ($transaction) {
    echo json_encode([
        'status' => $transaction['status'],
        'receipt' => $transaction['receipt_number']
    ]);
} else {
    echo json_encode(['status' => 'Pending']);
}
?>