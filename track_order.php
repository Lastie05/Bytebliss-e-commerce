<?php
include 'db.php';
session_start();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$tracking_number = isset($_GET['tracking']) ? $_GET['tracking'] : null;
$order = null;

if ($order_id || $tracking_number) {
    if ($order_id) {
        $stmt = $conn->prepare("SELECT o.*, u.full_name as user_name 
                                FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.id 
                                WHERE o.id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
    } elseif ($tracking_number) {
        $stmt = $conn->prepare("SELECT o.*, u.full_name as user_name 
                                FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.id 
                                WHERE o.tracking_number = ?");
        $stmt->bind_param("s", $tracking_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - ByteBliss</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .track-container {
            max-width: 900px;
            margin: 100px auto;
            padding: 20px;
        }
        .track-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(0,212,255,0.3);
        }
        .track-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .track-header h1 {
            color: #00d4ff;
            margin-bottom: 10px;
        }
        .track-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .track-form input {
            flex: 1;
            padding: 12px 15px;
            background: rgba(255,255,255,0.1);
            border: 1px solid #00d4ff;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
        }
        .track-form button {
            padding: 12px 25px;
            background: #00d4ff;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .track-form button:hover {
            background: #2ecc71;
            transform: translateY(-2px);
        }
        .order-details {
            margin-top: 30px;
            padding: 20px;
            background: rgba(255,255,255,0.03);
            border-radius: 15px;
        }
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .order-info-item {
            text-align: center;
        }
        .order-info-item .label {
            font-size: 0.75rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .order-info-item .value {
            font-size: 1.1rem;
            font-weight: bold;
            color: #00d4ff;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .status-processing { background: #ffc107; color: #000; }
        .status-shipped { background: #17a2b8; color: #fff; }
        .status-delivered { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }
        .status-pending-payment { background: #ffc107; color: #000; }
        .status-paid { background: #28a745; color: #fff; }
        
        .timeline {
            margin: 40px 0;
            position: relative;
        }
        .timeline-item {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }
        .timeline-icon {
            width: 50px;
            height: 50px;
            background: rgba(0,212,255,0.1);
            border: 2px solid #00d4ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            z-index: 2;
            background-color: #0f0c29;
        }
        .timeline-icon.completed {
            background: #2ecc71;
            border-color: #2ecc71;
        }
        .timeline-icon.current {
            background: #00d4ff;
            border-color: #00d4ff;
            animation: pulse 1.5s infinite;
        }
        .timeline-content {
            flex: 1;
            padding-bottom: 10px;
        }
        .timeline-content h4 {
            margin: 0 0 5px 0;
            color: #fff;
        }
        .timeline-content p {
            margin: 0;
            color: #888;
            font-size: 0.85rem;
        }
        .timeline-line {
            position: absolute;
            left: 24px;
            top: 50px;
            bottom: 0;
            width: 2px;
            background: #00d4ff;
            z-index: 1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .items-table th {
            color: #00d4ff;
        }
        .tracking-number {
            background: rgba(0,212,255,0.1);
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            text-align: center;
            margin: 15px 0;
        }
        .delivery-note {
            background: rgba(46,204,113,0.1);
            border-left: 3px solid #2ecc71;
            padding: 12px;
            margin-top: 20px;
            border-radius: 8px;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0,212,255,0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0,212,255,0); }
            100% { box-shadow: 0 0 0 0 rgba(0,212,255,0); }
        }
        .btn-home {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background: transparent;
            border: 1px solid #00d4ff;
            color: #00d4ff;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-home:hover {
            background: #00d4ff;
            color: #000;
        }
        @media (max-width: 768px) {
            .track-container {
                margin: 60px auto;
                padding: 15px;
            }
            .order-info {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .timeline-icon {
                width: 40px;
                height: 40px;
                font-size: 0.8rem;
            }
            .timeline-line {
                left: 19px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="track-container">
        <div class="track-card">
            <div class="track-header">
                <h1>📦 Track Your Order</h1>
                <p>Enter your Order ID or Tracking Number to check status</p>
            </div>

            <form class="track-form" method="GET" action="track_order.php">
                <input type="text" name="id" placeholder="Order ID (e.g., 1001)" value="<?php echo $order_id; ?>">
                <span style="color: #888; align-self: center;">OR</span>
                <input type="text" name="tracking" placeholder="Tracking Number" value="<?php echo $tracking_number; ?>">
                <button type="submit">Track →</button>
            </form>

            <?php if ($order): ?>
                <div class="order-details">
                    <div class="order-info">
                        <div class="order-info-item">
                            <div class="label">Order Number</div>
                            <div class="value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div class="order-info-item">
                            <div class="label">Order Date</div>
                            <div class="value"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                        </div>
                        <div class="order-info-item">
                            <div class="label">Total Amount</div>
                            <div class="value">KSh <?php echo number_format($order['total_amount']); ?></div>
                        </div>
                        <div class="order-info-item">
                            <div class="label">Payment Status</div>
                            <div class="value">
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['payment_status'])); ?>">
                                    <?php echo $order['payment_status']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="order-info-item">
                            <div class="label">Order Status</div>
                            <div class="value">
                                <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                                    <?php echo $order['order_status']; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if ($order['tracking_number']): ?>
                    <div class="tracking-number">
                        📬 Tracking Number: <strong><?php echo $order['tracking_number']; ?></strong>
                    </div>
                    <?php endif; ?>

                    <!-- Timeline -->
                    <div class="timeline">
                        <div class="timeline-line"></div>
                        
                        <?php
                        $statuses = [
                            'Processing' => ['icon' => '🛒', 'label' => 'Order Confirmed', 'desc' => 'Your order has been received and is being processed'],
                            'Shipped' => ['icon' => '🚚', 'label' => 'Order Shipped', 'desc' => 'Your order has been shipped and is on its way'],
                            'Delivered' => ['icon' => '✅', 'label' => 'Delivered', 'desc' => 'Your order has been delivered successfully'],
                        ];
                        
                        $current_status = $order['order_status'];
                        $status_order = ['Processing', 'Shipped', 'Delivered'];
                        $current_index = array_search($current_status, $status_order);
                        
                        foreach ($statuses as $key => $status):
                            $status_index = array_search($key, $status_order);
                            $is_completed = $status_index <= $current_index;
                            $is_current = $status_index == $current_index;
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-icon <?php echo $is_completed ? 'completed' : ''; ?> <?php echo $is_current && !$is_completed ? 'current' : ''; ?>">
                                <?php echo $status['icon']; ?>
                            </div>
                            <div class="timeline-content">
                                <h4><?php echo $status['label']; ?></h4>
                                <p><?php echo $status['desc']; ?></p>
                                <?php if ($key == 'Delivered' && $order['delivery_date'] && $is_completed): ?>
                                    <p style="color: #2ecc71; font-size: 0.8rem;">✓ Delivered on <?php echo date('F j, Y', strtotime($order['delivery_date'])); ?></p>
                                <?php endif; ?>
                                <?php if ($key == 'Shipped' && $order['tracking_number'] && $is_completed): ?>
                                    <p style="color: #00d4ff; font-size: 0.8rem;">Tracking: <?php echo $order['tracking_number']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Items -->
                    <h4>🛍️ Items Ordered</h4>
                    <table class="items-table">
                        <thead>
                            <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $items_stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
                            $items_stmt->bind_param("i", $order['id']);
                            $items_stmt->execute();
                            $items = $items_stmt->get_result();
                            while ($item = $items->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>KSh <?php echo number_format($item['price']); ?></td>
                                <td>KSh <?php echo number_format($item['quantity'] * $item['price']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Delivery Address -->
                    <h4>📍 Delivery Address</h4>
                    <p><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>

                    <?php if ($order['notes']): ?>
                    <div class="delivery-note">
                        <strong>📝 Delivery Note:</strong><br>
                        <?php echo nl2br(htmlspecialchars($order['notes'])); ?>
                    </div>
                    <?php endif; ?>

                    <div style="text-align: center;">
                        <a href="index.php" class="btn-home">← Continue Shopping</a>
                    </div>
                </div>

            <?php elseif ($order_id || $tracking_number): ?>
                <div style="text-align: center; padding: 40px;">
                    <p style="color: #ff4757;">❌ Order not found. Please check your Order ID or Tracking Number.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>