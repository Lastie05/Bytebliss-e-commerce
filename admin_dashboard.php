<?php
session_start();
include 'db.php';

// ============================================
// SIMPLE PASSWORD AUTHENTICATION
// ============================================
$is_admin = false;

// Check via URL parameter (from admin modal in footer.php)
if (isset($_GET['access']) && $_GET['access'] === 'YourSecureAdminPassword123!') {
    $is_admin = true;
    $_SESSION['admin_access'] = true;
}

// Check via session
if (isset($_SESSION['admin_access']) && $_SESSION['admin_access'] === true) {
    $is_admin = true;
}

// If not admin, deny access
if (!$is_admin) {
    header('HTTP/1.0 403 Forbidden');
    die("<!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied</title>
        <style>
            body {
                background: radial-gradient(circle at center, #1a1a2e, #0f0c29, #000000);
                color: white;
                text-align: center;
                padding: 100px;
                font-family: Arial, sans-serif;
            }
            h1 { color: #ff4757; }
            a { color: #00d4ff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <h1>🔒 Access Denied</h1>
        <p>You do not have permission to view this page.</p>
        <p><a href='index.php'>← Return to Homepage</a></p>
    </body>
    </html>");
}

// ============================================
// HANDLE ORDER UPDATES
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_order'])) {
        $order_id = (int)$_POST['order_id'];
        $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
        $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
        $tracking_number = mysqli_real_escape_string($conn, $_POST['tracking_number']);
        $delivery_date = !empty($_POST['delivery_date']) ? $_POST['delivery_date'] : null;
        $notes = mysqli_real_escape_string($conn, $_POST['notes']);
        
        $stmt = $conn->prepare("UPDATE orders SET order_status = ?, payment_status = ?, tracking_number = ?, delivery_date = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $order_status, $payment_status, $tracking_number, $delivery_date, $notes, $order_id);
        $stmt->execute();
        
        if ($payment_status === 'Paid') {
            $stmt2 = $conn->prepare("UPDATE orders SET payment_verified_by = 'Admin', payment_verified_at = NOW() WHERE id = ?");
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();
        }
        $success_message = "Order #$order_id updated successfully!";
    }
    if (isset($_POST['verify_mpesa'])) {
        $transaction_id = (int)$_POST['transaction_id'];
        $order_id = (int)$_POST['order_id'];
        
        $stmt = $conn->prepare("UPDATE mpesa_transactions SET status = 'Completed', verified_at = NOW(), verified_by = 'Admin' WHERE id = ?");
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        
        $stmt2 = $conn->prepare("UPDATE orders SET payment_status = 'Paid', payment_verified_by = 'Admin', payment_verified_at = NOW() WHERE id = ?");
        $stmt2->bind_param("i", $order_id);
        $stmt2->execute();
        
        $success_message = "M-Pesa payment verified for Order #$order_id!";
    }
}

// ============================================
// GET STATISTICS
// ============================================
$stats = [
    'total_orders' => $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'],
    'pending_orders' => $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'Processing'")->fetch_assoc()['total'],
    'shipped_orders' => $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'Shipped'")->fetch_assoc()['total'],
    'delivered_orders' => $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = 'Delivered'")->fetch_assoc()['total'],
    'paid_orders' => $conn->query("SELECT COUNT(*) as total FROM orders WHERE payment_status = 'Paid'")->fetch_assoc()['total'],
    'pending_payment' => $conn->query("SELECT COUNT(*) as total FROM orders WHERE payment_status IN ('Pending', 'Awaiting Payment')")->fetch_assoc()['total'],
    'total_revenue' => $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'Paid'")->fetch_assoc()['total'],
];

// Fetch orders
$orders_query = "SELECT o.*, u.full_name as user_name,
                 (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                 FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 ORDER BY o.order_date DESC";
$orders_result = $conn->query($orders_query);

// Fetch pending M-Pesa transactions
$mpesa_query = "SELECT mt.*, o.customer_name, o.total_amount, o.id as order_id
                FROM mpesa_transactions mt
                JOIN orders o ON mt.order_id = o.id
                WHERE mt.status = 'Requested' OR mt.status = 'Pending'
                ORDER BY mt.created_at DESC";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ByteBliss Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-dashboard { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .admin-header { background: linear-gradient(135deg, #00d4ff, #2ecc71); padding: 25px; border-radius: 15px; margin-bottom: 30px; color: #000; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header h1 { margin: 0; font-size: 1.5rem; }
        .admin-header p { margin: 5px 0 0; font-size: 0.8rem; opacity: 0.8; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 15px; border-radius: 15px; text-align: center; border: 1px solid rgba(0,212,255,0.2); }
        .stat-card h3 { font-size: 1.8rem; margin: 0; color: #00d4ff; }
        .stat-card p { margin: 5px 0 0; font-size: 0.8rem; color: #aaa; }
        .section-title { margin: 30px 0 15px; color: #00d4ff; border-left: 3px solid #00d4ff; padding-left: 15px; }
        .admin-table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 15px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .admin-table th { background: rgba(0,212,255,0.2); color: #00d4ff; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; }
        .status-processing { background: #ffc107; color: #000; }
        .status-shipped { background: #17a2b8; color: #fff; }
        .status-delivered { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }
        .status-pending { background: #ffc107; color: #000; }
        .status-paid { background: #28a745; color: #fff; }
        .status-awaiting-payment { background: #fd7e14; color: #fff; }
        .btn-update, .btn-verify { background: #00d4ff; color: #000; border: none; padding: 5px 12px; border-radius: 5px; cursor: pointer; font-size: 0.75rem; }
        .btn-verify { background: #2ecc71; }
        .order-details-row { display: none; background: rgba(255,255,255,0.03); }
        .order-details-row.active { display: table-row; }
        .order-details-cell { padding: 20px; }
        .form-group { display: inline-block; margin-right: 15px; margin-bottom: 10px; vertical-align: top; }
        .form-group label { display: block; font-size: 0.7rem; color: #888; margin-bottom: 3px; }
        .form-group input, .form-group select, .form-group textarea { background: rgba(255,255,255,0.1); border: 1px solid #00d4ff; color: #fff; padding: 6px 10px; border-radius: 5px; font-size: 0.8rem; }
        .form-group textarea { width: 200px; }
        .success-msg { background: #2ecc71; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; color: #000; font-weight: bold; }
        .toggle-details { background: transparent; border: 1px solid #00d4ff; color: #00d4ff; padding: 4px 10px; border-radius: 5px; cursor: pointer; font-size: 0.7rem; }
        .logout-btn { background: rgba(0,0,0,0.2); padding: 8px 15px; border-radius: 20px; color: #000; text-decoration: none; font-size: 0.8rem; }
        .admin-info { font-size: 0.8rem; text-align: right; }
        @media (max-width: 768px) {
            .admin-header { flex-direction: column; text-align: center; gap: 10px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .admin-table { font-size: 0.7rem; }
            .admin-table th, .admin-table td { padding: 8px; }
            .form-group { display: block; margin-bottom: 10px; }
            .form-group textarea { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <div class="admin-header">
            <div>
                <h1>📦 ByteBliss Admin Dashboard</h1>
                <p>Manage orders, verify payments, track deliveries</p>
            </div>
            <div class="admin-info">
                <div>👤 Admin</div>
                <a href="logout.php" class="logout-btn" onclick="return confirm('Logout?')">🚪 Logout</a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="success-msg">✅ <?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card"><h3><?php echo $stats['total_orders']; ?></h3><p>Total Orders</p></div>
            <div class="stat-card"><h3><?php echo $stats['pending_orders']; ?></h3><p>Processing</p></div>
            <div class="stat-card"><h3><?php echo $stats['shipped_orders']; ?></h3><p>Shipped</p></div>
            <div class="stat-card"><h3><?php echo $stats['delivered_orders']; ?></h3><p>Delivered</p></div>
            <div class="stat-card"><h3><?php echo $stats['paid_orders']; ?></h3><p>Paid Orders</p></div>
            <div class="stat-card"><h3><?php echo $stats['pending_payment']; ?></h3><p>Pending Payment</p></div>
            <div class="stat-card"><h3>KSh <?php echo number_format($stats['total_revenue'] ?? 0); ?></h3><p>Total Revenue</p></div>
        </div>

        <!-- Pending M-Pesa Payments -->
        <?php if ($mpesa_result && $mpesa_result->num_rows > 0): ?>
        <div class="section-title">💰 Pending M-Pesa Payments to Verify</div>
        <table class="admin-table">
            <thead>
                <tr><th>ID</th><th>Order #</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Date</th><th>Action</th>
            </thead>
            <tbody>
                <?php while ($trans = $mpesa_result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $trans['id']; ?></td>
                    <td>#<?php echo str_pad($trans['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($trans['customer_name']); ?></td>
                    <td><?php echo $trans['phone']; ?></td>
                    <td>KSh <?php echo number_format($trans['amount']); ?></td>
                    <td><?php echo date('M d, H:i', strtotime($trans['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="transaction_id" value="<?php echo $trans['id']; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $trans['order_id']; ?>">
                            <button type="submit" name="verify_mpesa" class="btn-verify" onclick="return confirm('Confirm payment received?')">✓ Verify</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- All Orders -->
        <div class="section-title">📋 All Orders</div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th><th>Customer</th><th>Phone</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Tracking</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders_result->fetch_assoc()): ?>
                <tr id="order-row-<?php echo $order['id']; ?>">
                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo $order['customer_phone'] ?? 'N/A'; ?></td>
                    <td><?php echo $order['item_count']; ?> items</td>
                    <td>KSh <?php echo number_format($order['total_amount']); ?></td>
                    <td><span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['payment_status'])); ?>"><?php echo $order['payment_status']; ?></span></td>
                    <td><span class="status-badge status-<?php echo strtolower($order['order_status']); ?>"><?php echo $order['order_status']; ?></span></td>
                    <td><?php echo $order['tracking_number'] ?? '—'; ?></td>
                    <td><button class="toggle-details" onclick="toggleDetails(<?php echo $order['id']; ?>)">📋 Manage</button></td>
                </tr>
                <tr id="details-<?php echo $order['id']; ?>" class="order-details-row">
                    <td colspan="9">
                        <div class="order-details-cell">
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="form-group">
                                    <label>Order Status</label>
                                    <select name="order_status">
                                        <option value="Processing" <?php echo $order['order_status']=='Processing'?'selected':''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order['order_status']=='Shipped'?'selected':''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order['order_status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order['order_status']=='Cancelled'?'selected':''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Payment Status</label>
                                    <select name="payment_status">
                                        <option value="Pending" <?php echo $order['payment_status']=='Pending'?'selected':''; ?>>Pending</option>
                                        <option value="Awaiting Payment" <?php echo $order['payment_status']=='Awaiting Payment'?'selected':''; ?>>Awaiting Payment</option>
                                        <option value="Paid" <?php echo $order['payment_status']=='Paid'?'selected':''; ?>>Paid</option>
                                        <option value="Failed" <?php echo $order['payment_status']=='Failed'?'selected':''; ?>>Failed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tracking Number</label>
                                    <input type="text" name="tracking_number" placeholder="e.g., BB-<?php echo date('Ymd') . '-' . $order['id']; ?>" value="<?php echo $order['tracking_number']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Delivery Date</label>
                                    <input type="date" name="delivery_date" value="<?php echo $order['delivery_date']; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" rows="2" placeholder="Delivery notes..."><?php echo $order['notes']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" name="update_order" class="btn-update">💾 Update</button>
                                </div>
                            </form>
                            <hr>
                            <h4>Order Items:</h4>
                            <?php 
                            $items_stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
                            $items_stmt->bind_param("i", $order['id']);
                            $items_stmt->execute();
                            $items = $items_stmt->get_result();
                            ?>
                            <table style="width:100%; font-size:0.8rem;">
                                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                                <tbody>
                                <?php while($item=$items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>KSh <?php echo number_format($item['price']); ?></td>
                                    <td>KSh <?php echo number_format($item['quantity']*$item['price']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                            <p style="margin-top:15px; font-size:0.75rem;"><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleDetails(orderId) {
            const detailsRow = document.getElementById(`details-${orderId}`);
            detailsRow.classList.toggle('active');
        }
    </script>
</body>
</html>