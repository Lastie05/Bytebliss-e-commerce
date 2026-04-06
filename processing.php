<?php
include 'db.php';
$order_id = $_GET['order_id']; // Passed from the fetch request
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Processing Payment | ByteBliss</title>
    <link rel="stylesheet" href="style.css"> <script>
        const orderId = <?php echo $order_id; ?>;
        
        function checkStatus() {
            fetch(`check_status.php?order_id=${orderId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'Completed') {
                        // Update UI to Success
                        document.getElementById('status-box').innerHTML = `
                            <div class="success-animation">
                                <h2 class="cyan-text">Payment Successful!</h2>
                                <p>Receipt: ${data.receipt}</p>
                                <button onclick="window.location.href='home.php'" class="btn-checkout-final">Back to Store</button>
                            </div>`;
                        localStorage.removeItem('bytebliss_cart'); // Clear the bag
                    } else if (data.status === 'Failed') {
                        document.getElementById('status-box').innerHTML = `
                            <h2 style="color: #ff4444;">Payment Failed</h2>
                            <p>The transaction was cancelled or timed out.</p>
                            <button onclick="window.location.href='home.php'" class="btn-checkout-final">Try Again</button>`;
                    }
                });
        }

        // Check every 3 seconds
        setInterval(checkStatus, 3000);
    </script>
</head>
<body class="auth-overlay">
    <div class="payment-container glass" id="status-box" style="text-align: center; padding: 50px;">
        <div class="spinner" style="display: block; margin: 0 auto 20px;"></div>
        <h2 class="cyan-text">Processing Your Payment...</h2>
        <p>Please check your phone for the M-Pesa PIN prompt.</p>
        <p class="small-text">Do not refresh this page.</p>
    </div>
</body>
</html>