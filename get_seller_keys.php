<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

$data = json_decode(file_get_contents("php://input"));
$usdtReceiverAddress = $data->usdt_receiver_address;

// Prepare SQL statement
$stmt = $conn->prepare("SELECT public_key, private_key FROM sell_orders WHERE usdt_receiver_address = ?");
$stmt->bind_param("s", $usdtReceiverAddress);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row); // Return public and private key as JSON
} else {
    echo json_encode(['error' => 'No keys found']);
}

$stmt->close();
$conn->close();
?>