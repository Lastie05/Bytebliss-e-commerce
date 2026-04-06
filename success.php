<?php
include 'db.php';
session_start();

// Get the order ID from the URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Fetch order details with items
$stmt = $conn->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(oi.quantity, 'x ', oi.product_name) SEPARATOR ', ') as items_list
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ?
    GROUP BY o.id
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Fetch individual items for detailed display
$items_stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC);

if (!$order) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success | ByteBliss</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #1a1a2e, #0f0c29, #000000);
            color: white;
            padding: 100px 20px;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(0, 229, 255, 0.3);
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.2);
            max-width: 600px;
            width: 100%;
        }
        .check-icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-number {
            color: #00e5ff;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .order-details {
            margin: 30px 0;
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
        }
        .order-items {
            margin: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .home-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: #00e5ff;
            color: black;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
        .home-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.5);
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        .cyan-text {
            color: #00e5ff;
        }
        hr {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="success-wrapper">
        <div class="success-card">
            <div class="check-icon">✓</div>
            <h1 style="text-align: center;">Order Confirmed!</h1>
            <p style="text-align: center; color: #aaa;">Thank you for shopping with ByteBliss</p>
            
            <div class="order-details">
                <p><strong>Order #:</strong> <span class="order-number"><?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span></p>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                <p><strong>Payment Status:</strong> <span class="status-badge"><?php echo $order['payment_status']; ?></span></p>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
            </div>
            
            <h3>Order Summary</h3>
            <div class="order-items">
                <?php foreach ($items as $item): ?>
                <div class="order-item">
                    <span><?php echo $item['quantity']; ?>x <?php echo htmlspecialchars($item['product_name']); ?></span>
                    <span class="cyan-text">KSh <?php echo number_format($item['price'] * $item['quantity']); ?></span>
                </div>
                <?php endforeach; ?>
                
                <hr>
                
                <div class="order-item" style="font-weight: bold; font-size: 1.1rem;">
                    <span>Total Amount</span>
                    <span class="cyan-text">KSh <?php echo number_format($order['total_amount']); ?></span>
                </div>
            </div>
            
            <p style="text-align: center; font-size: 0.9rem; color: #888; margin-top: 20px;">
                <?php if ($order['payment_status'] == 'Paid'): ?>
                Your payment has been received. We'll ship your order within 72 hours.
                <?php elseif ($order['payment_status'] == 'Awaiting Payment'): ?>
                Please complete the M-Pesa payment on your phone. The order will be processed once payment is confirmed.
                <?php else: ?>
                You'll pay when your order arrives. Our team will contact you to confirm delivery.
                <?php endif; ?>
            </p>
            
            <p><a href="track_order.php?id=<?php echo $order['id']; ?>" style="color: #00d4ff; text-decoration: none;">
                📦 Track Your Order →
            </a></p>   

            <a href="index.php" class="home-btn">Continue Shopping →</a>
        </div>
    </div>
</body>
</html>