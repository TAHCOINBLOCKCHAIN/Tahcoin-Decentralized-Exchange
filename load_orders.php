<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost"; // Your server name
$username = "your_database"; // Your database username
$password = "your_password"; // Your database password
$dbname = "your_database"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => '0', 'message' => 'Database connection failed']));
}

// Load active sell orders
$sqlActive = "SELECT * FROM sell_orders WHERE status='active' ORDER BY created_at DESC";
$resultActive = $conn->query($sqlActive);

if ($resultActive === FALSE) {
    die(json_encode(['status' => '0', 'message' => 'Error loading active orders: ' . $conn->error]));
}

// Load successful sell orders
$sqlSuccessful = "SELECT * FROM sell_orders WHERE status='successful' ORDER BY created_at DESC";
$resultSuccessful = $conn->query($sqlSuccessful);

if ($resultSuccessful === FALSE) {
    die(json_encode(['status' => '0', 'message' => 'Error loading successful orders: ' . $conn->error]));
}

// Output the results directly without storing them in an array
$activeOrdersHtml = '';
while ($row = $resultActive->fetch_assoc()) {
    $activeOrdersHtml .= '<div class="sell-order-item">
        <strong>Amount:</strong> ' . htmlspecialchars($row['amount']) . ' Tahcoin<br>
        <strong>Price:</strong> ' . htmlspecialchars($row['price_in_usdt']) . ' USDT<br>
        <strong>USDT Receiver Address:</strong> 
        ' . htmlspecialchars($row['usdt_receiver_address']) . ' 
        <button onclick="copyToClipboard(\'' . htmlspecialchars($row['usdt_receiver_address']) . '\')">📋</button><br>
        <button onclick="displayPurchaseInstructions(\'' . htmlspecialchars($row['amount']) . '\', \'' . htmlspecialchars($row['usdt_receiver_address']) . '\')">Buy</button>
    </div>';
}

$successfulOrdersHtml = '';
while ($row = $resultSuccessful->fetch_assoc()) {
    $successfulOrdersHtml .= '<div class="sell-order-item">
        <strong>Amount:</strong> ' . htmlspecialchars($row['amount']) . ' Tahcoin<br>
        <strong>Price:</strong> ' . htmlspecialchars($row['price_in_usdt']) . ' USDT<br>
        <strong>Status:</strong> Successful<br>
        <strong>USDT Receiver Address:</strong> 
        ' . htmlspecialchars($row['usdt_receiver_address']) . '
    </div>';
}

// Return both active and successful orders as HTML
echo json_encode([
    'status' => '1',
    'active_orders_html' => $activeOrdersHtml,
    'successful_orders_html' => $successfulOrdersHtml,
]);

$conn->close();
?>