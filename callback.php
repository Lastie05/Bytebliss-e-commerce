<?php
include 'db.php';
$callbackJSONData = file_get_contents('php://input');
$data = json_decode($callbackJSONData, true);

$resultCode = $data['Body']['stkCallback']['ResultCode'];
$checkoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'];

if ($resultCode == 0) {
    // Payment Successful!
    $items = $data['Body']['stkCallback']['CallbackMetadata']['Item'];
    $receipt = "";
    foreach($items as $item) {
        if($item['Name'] == "MpesaReceiptNumber") $receipt = $item['Value'];
    }

    // Update database
    $conn->query("UPDATE mpesa_transactions SET status = 'Completed', receipt_number = '$receipt' 
                  WHERE checkout_request_id = '$checkoutRequestID'");
    
    // Update main order status
    $conn->query("UPDATE orders SET status = 'completed' 
                  WHERE id = (SELECT order_id FROM mpesa_transactions WHERE checkout_request_id = '$checkoutRequestID')");
} else {
    // Payment Failed or Cancelled
    $conn->query("UPDATE mpesa_transactions SET status = 'Failed' WHERE checkout_request_id = '$checkoutRequestID'");
}
?>