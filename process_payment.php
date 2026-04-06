<?php
include 'db.php';
include 'mpesa_stk.php'; 
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

// Validate required fields
if (empty($data['name']) || empty($data['phone']) || empty($data['county']) || empty($data['address']) || empty($data['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required information. Please provide name, phone, county, address, and cart items.']);
    exit;
}

$cart = json_decode($data['cart'], true);
if (empty($cart)) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
    exit;
}

// Validate phone
$cleanPhone = preg_replace('/[^0-9]/', '', $data['phone']);

// Normalize to Kenyan format (starting with 07 or 01)
if (substr($cleanPhone, 0, 3) === '254') {
    $cleanPhone = '0' . substr($cleanPhone, 3);
}

// Validate Kenyan phone number format (07XXXXXXXX or 01XXXXXXXX)
if (!preg_match('/^(07|01)\d{8}$/', $cleanPhone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid Kenyan phone number (e.g., 0712345678)']);
    exit;
}

// Update the data array with cleaned phone for later use
$data['phone'] = $cleanPhone;

mysqli_begin_transaction($conn);

try {
    $calculated_total = 0;
    foreach ($cart as $item) {
        $calculated_total += $item['price'] * $item['qty'];
    }
    
    $payment_status = ($data['payment_method'] === 'Cash') ? 'Pending' : 'Awaiting Payment';
    
    // Build full address
    $fullAddress = $data['address'] . ', ' . $data['county'] . ' County';
    if (!empty($data['delivery_instructions'])) {
        $fullAddress .= "\nInstructions: " . $data['delivery_instructions'];
    }
    
    // Format phone
    $formattedPhone = $data['phone'];
    if (substr($formattedPhone, 0, 1) === '0') {
        $formattedPhone = '254' . substr($formattedPhone, 1);
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // IMPORTANT: Make sure column names match your database EXACTLY
    // Your database has: id, user_id, customer_name, customer_phone, address, total_amount, payment_status, order_status, order_date
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, address, total_amount, payment_status, order_status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Processing')");
    $stmt->bind_param("isssds", $user_id, $data['name'], $formattedPhone, $fullAddress, $calculated_total, $payment_status);
    
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    $orderId = $stmt->insert_id;
    
    // Insert order items
    $order_items_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                                        VALUES (?, ?, ?, ?, ?)");
    
    foreach ($cart as $item) {
        $product_id = isset($item['id']) ? $item['id'] : null;
        $order_items_stmt->bind_param("iisid", $orderId, $product_id, $item['name'], $item['qty'], $item['price']);
        $order_items_stmt->execute();
    }
    
    // Process payment
    if ($data['payment_method'] === 'M-Pesa') {
        if (empty($data['mpesa_phone'])) {
            throw new Exception('M-Pesa number is required');
        }
        
        $mpesaPhone = $data['mpesa_phone'];
        if (substr($mpesaPhone, 0, 1) === '0') {
            $mpesaPhone = '254' . substr($mpesaPhone, 1);
        }
        if (substr($mpesaPhone, 0, 3) !== '254') {
            $mpesaPhone = '254' . $mpesaPhone;
        }
        
        $mpesa = new MpesaSTK();
        $response = $mpesa->sendSTKPush($mpesaPhone, $calculated_total, $orderId);
        
        if (isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
            $checkoutRequestID = $response['CheckoutRequestID'];
            
            $mpesa_stmt = $conn->prepare("INSERT INTO mpesa_transactions (order_id, checkout_request_id, phone, amount, status) 
                                          VALUES (?, ?, ?, ?, 'Requested')");
            $mpesa_stmt->bind_param("issd", $orderId, $checkoutRequestID, $mpesaPhone, $calculated_total);
            $mpesa_stmt->execute();
            
            mysqli_commit($conn);
            
            echo json_encode([
                'status' => 'success', 
                'order_id' => $orderId, 
                'method' => 'mpesa'
            ]);
        } else {
            $error_message = isset($response['errorMessage']) ? $response['errorMessage'] : 'M-Pesa request failed';
            throw new Exception($error_message);
        }
        
    } else if ($data['payment_method'] === 'Cash') {
        mysqli_commit($conn);
        
        echo json_encode([
            'status' => 'success', 
            'order_id' => $orderId, 
            'method' => 'cash'
        ]);
        
    } else {
        throw new Exception('Invalid payment method');
    }
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) $stmt->close();
if (isset($order_items_stmt)) $order_items_stmt->close();
if (isset($mpesa_stmt)) $mpesa_stmt->close();

$conn->close();
?>