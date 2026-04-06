<?php
include 'db.php';

// Simple password protection (Replace 'AdminBliss2026' with your own password)
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Check URL parameter as fallback (remove in production)
    if (!isset($_GET['access']) || $_GET['access'] !== "AdminBliss2026") {
        die("Access Denied");
    }
}

// Fetch subscribers
$query = "SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ByteBliss | Subscriber Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <header class="admin-header">
            <h1>Newsletter <span class="text-gradient">Leads</span> 📈</h1>
            <p>Total Subscribers: <?php echo $result->num_rows; ?></p>
        </header>

        <div class="admin-card">
            <table class="subscriber-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email Address</th>
                        <th>Date Subscribed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td class="email-cell"><?php echo $row['email']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['subscribed_at'])); ?></td>
                        <td>
                            <button class="copy-btn" onclick="copyEmail('<?php echo $row['email']; ?>')">Copy</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function copyEmail(email) {
        navigator.clipboard.writeText(email);
        alert("Email copied to clipboard!");
    }
    </script>
    
</body>
</html>