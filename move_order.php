<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

$data = json_decode(file_get_contents("php://input"));
$usdtReceiverAddress = $data->usdt_receiver_address;
$amount = $data->amount;

// Move order logic here (you may want to adjust this based on your schema)
$stmt = $conn->prepare("DELETE FROM sell_orders WHERE usdt_receiver_address = ? AND amount = ?");
$stmt->bind_param("sd", $usdtReceiverAddress, $amount);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move order']);
}

$stmt->close();
$conn->close();
?>