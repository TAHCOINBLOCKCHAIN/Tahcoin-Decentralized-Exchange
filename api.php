<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Database connection settings
$dsn = 'mysql:host=127.0.0.1;dbname=your_database;charset=utf8';
$username = 'your_database';
$password = 'your_password';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['message' => "Database connection failed: " . htmlspecialchars($e->getMessage())]);
    exit;
}

// Decode the incoming JSON request
$request = json_decode(file_get_contents("php://input"), true);
$action = $request['action'] ?? null;

if ($action == 'create_sell_order') {
    createSellOrder($request);
} elseif ($action == 'buy_tahcoin') {
    buyTahcoin($request);
} else {
    echo json_encode(['message' => "Invalid action specified."]);
}

if ($action == 'load_sell_orders') {
   loadSellOrders();
}

function loadSellOrders() {
   global $pdo;

   try {
       $stmt = $pdo->prepare("SELECT * FROM sell_orders");
       $stmt->execute();
       $sellOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

       echo json_encode($sellOrders);
   } catch (Exception $e) {
       echo json_encode(['message' => "Failed to load sell orders: " . htmlspecialchars($e->getMessage())]);
   }
}

function encrypt($input, $shiftAmount) {
    $output = '';
    foreach (str_split($input) as $char) {
        // Shift character by the defined amount
        $shiftedChar = chr(ord($char) + $shiftAmount);
        $output .= $shiftedChar;
    }
    // Reverse the string
    return strrev($output);
}

function decrypt($input, $shiftAmount) {
    // Reverse the string first
    $reversed = strrev($input);
    $output = '';
    
    foreach (str_split($reversed) as $char) {
        // Shift character back by the defined amount
        $originalChar = chr(ord($char) - $shiftAmount);
        $output .= $originalChar;
    }
    
    return $output;
}

function createSellOrder($request) {
    global $pdo; // Use global variable for PDO connection

    $publicKey = $request['public_key'] ?? null;
    $privateKey = $request['private_key'] ?? null;
    $amount = $request['amount'] ?? null;
    $usdtReceiverAddress = $request['usdt_receiver_address'] ?? null;
    $priceInUSDT = $request['price_in_usdt'] ?? null;

    // Validate input parameters
    if (!$publicKey || !$privateKey || !$amount || !$usdtReceiverAddress || !$priceInUSDT) {
        echo json_encode(['message' => "Missing required parameters for creating sell order."]);
        return;
    }

    // Encrypt the private key before storing or processing
    $shiftAmount = 3; // Define a shift amount for encryption
    $encryptedPrivateKey = encrypt($privateKey, $shiftAmount);

    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO sell_orders (public_key, amount, usdt_receiver_address, price_in_usdt, private_key) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$publicKey, $amount, $usdtReceiverAddress, $priceInUSDT, $encryptedPrivateKey])) {
        echo json_encode(['message' => "Sell order created for {$amount} Tahcoin at {$priceInUSDT} USDT each."]);
    } else {
        echo json_encode(['message' => "Failed to save sell order."]);
    }
}

function buyTahcoin($request) {
    global $pdo; // Use global variable for PDO connection

    $buyerAddress = $request['buyer_address'] ?? null;
    $sellerUsdtAddress = $request['seller_usdt_address'] ?? null;
    $expectedAmount = floatval($request['expected_amount'] ?? null);
    $actualAmount = floatval($request['actual_amount'] ?? null); // New parameter

    // Validate input parameters
    if (!$buyerAddress || !$sellerUsdtAddress || !$expectedAmount || !$actualAmount) {
        echo json_encode(['message' => "Error: Missing required parameters for buying Tahcoin."]);
        return;
    }

    echo json_encode(['message' => "Processing purchase..."]);
    ob_flush();
    flush();
    
    sleep(1); // Simulate processing time

    try {
        global $publicKey, $privateKey; // Use global variables to access keys
        
        // Decrypt the private key before using it
        $shiftAmount = 3; // Same shift amount used for encryption
        if (!isset($privateKey)) {
            throw new Exception("Private key not set.");
        }
        
        $decryptedPrivateKey = decrypt($privateKey, $shiftAmount); // Decrypt private key

        // Send Tahcoin using the decrypted private key
        sendTahcoin($publicKey, $decryptedPrivateKey, htmlspecialchars($buyerAddress), htmlspecialchars($actualAmount)); 

        // Load existing sell orders and remove the sold order
        $stmt = $pdo->prepare("SELECT * FROM sell_orders WHERE usdt_receiver_address = ? AND amount = ?");
        if (!$stmt->execute([$sellerUsdtAddress, floatval($expectedAmount)])) {
            throw new Exception("Failed to fetch sell orders.");
        }

        if ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Remove sold order from database
            if ($deleteStmt = $pdo->prepare("DELETE FROM sell_orders WHERE id = ?")) {
                if ($deleteStmt->execute([$order['id']])) {
                    saveSuccessfulOrder($order); // Save successful order
                    echo json_encode(['message' => "Success: Tahcoin sent to {$buyerAddress}."]);
                } else {
                    echo json_encode(['message' => "Failed to remove sold order."]);
                }
            }
        } else {
            echo json_encode(['message' => "No matching sell order found."]);
        }

    } catch (Exception $e) { 
        echo json_encode(['message' => "Error: " . htmlspecialchars($e->getMessage())]);
        return; 
    }
}

function saveSuccessfulOrder($order) {
    global $pdo; // Use global variable for PDO connection

    // Save relevant details only without sensitive information like private keys
    unset($order['private_key']); // Remove private key before saving

   try {
       if (!$stmt = $pdo->prepare("INSERT INTO successful_orders (amount, usdt_receiver_address) VALUES (?, ?)")) {
           throw new Exception("Failed to prepare statement.");
       }
       
       if (!$stmt->execute([$order['amount'], htmlspecialchars($order['usdt_receiver_address'])])) {
           throw new Exception("Failed to save successful order.");
       }
       
   } catch (Exception $e) {
       throw new Exception("Error saving successful order: " . htmlspecialchars($e->getMessage()));
   }
}

function sendTahcoin($publicKey, $decryptedPrivateKey, $receiverAddress, $amountToSend) {
   try {
       if (!$url = 'https://tahriver.online/api_313.php') { 
           throw new Exception("Invalid API URL.");
       }

       if (!$data = http_build_query([
           'public_key' => htmlspecialchars($publicKey),
           'private_key' => htmlspecialchars($decryptedPrivateKey), 
           'receiver_address' => htmlspecialchars($receiverAddress),
           'amount' => htmlspecialchars($amountToSend),
       ])) { 
           throw new Exception("Failed to build request data.");
       }

       // Set up HTTP options for the request
       if (!$options = [
           'http' => [
               'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
               'method'  => 'POST',
               'content' => $data,
           ],
       ]) { 
           throw new Exception("Failed to set up HTTP options.");
       }

       // Execute HTTP request
       if (!$context  = stream_context_create($options)) { 
           throw new Exception("Failed to create stream context.");
       }

       if (!$response = file_get_contents($url, false, $context)) {
           throw new Exception("Failed to connect to Tahcoin API.");
       }

       if (!$responseData = json_decode($response, true)) {
           throw new Exception("Invalid response from Tahcoin API.");
       }

       if (isset($responseData['error'])) {
           throw new Exception("Tahcoin API error: " . htmlspecialchars($responseData['error']));
       }

       if ($responseData['status'] !== 'success') {
           throw new Exception("Failed to create transaction: " . htmlspecialchars($responseData['message']));
       }

       return true;

   } catch (Exception $e) {
       throw new Exception("Transaction failed: " . htmlspecialchars($e->getMessage()));
   }
}
?>