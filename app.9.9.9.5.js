document.getElementById('createOrder').addEventListener('click', function() {
    const publicKey = document.getElementById('public_key').value;
    const privateKey = document.getElementById('private_key').value;
    const amount = parseFloat(document.getElementById('amount').value); // Ensure amount is a number
    const usdtReceiverAddress = document.getElementById('usdt_receiver_address').value;

    // Validate input fields
    if (!publicKey || !privateKey || isNaN(amount) || amount <= 0 || !usdtReceiverAddress) {
        alert("Please fill in all fields with valid values.");
        return;
    }

    // Calculate total price for the amount of Tahcoin
    const pricePerTahcoin = 1; // Price for 1 Tahcoin in USDT
    const totalPriceInUSDT = (amount * pricePerTahcoin).toFixed(3); // Total price for the amount

    // Generate a random decimal between 0.000000001 and 0.999999999
    // Function to generate a random decimal excluding the digit '6'
function generateRandomDecimal(length) {
    let result = '';
    
    for (let i = 0; i < length; i++) {
        let digit;
        do {
            digit = Math.floor(Math.random() * 10); // Generate a digit from 0 to 9
        } while (digit === 6); // Exclude the digit '6'
        
        result += digit; // Append the valid digit to the result
    }
    
    return result;
}

// Generate a random decimal of 10 digits excluding '6'
const randomDecimal = generateRandomDecimal(5);

// Output the generated random decimal
//console.log(randomDecimal); // Example output: "1234567890" (but without '6')

    // Combine total price with random decimal, ensuring no additional decimal point
    const priceInUSDT = `${totalPriceInUSDT}${randomDecimal}`; // Append random decimal without additional formatting

    // Create sell order logic
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'create_sell_order',
            public_key: publicKey,
            private_key: privateKey,
            amount: amount.toFixed(19), // Ensure amount is formatted correctly
            usdt_receiver_address: usdtReceiverAddress,
            price_in_usdt: priceInUSDT // Include price in USDT
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('message').innerText = data.message;
        loadSellOrders(); // Reload sell orders after creating one
    })
    .catch(error => {
        console.error("Error during transaction:", error);
        document.getElementById('message').innerText = "Error occurred while processing the transaction.";
    });
});

// Function to generate a random 9-digit number excluding '6'
function generateRandomDecimal(length) {
    let result = '';
    
    for (let i = 0; i < length; i++) {
        let digit;
        do {
            digit = Math.floor(Math.random() * 5); // Generate a digit from 0 to 5
        } while (digit === 6); // Exclude the digit '6'
        
        result += digit; // Append the valid digit to the result
    }
    
    return result;
}

let currentPage = 1;
const ordersPerPage = 9; // Number of orders to display per page
let allActiveOrders = []; // Store all active orders as objects
let allSuccessfulOrdersHtml = ''; // Store all successful orders HTML

// Load sell orders from MySQL database with polling
function loadSellOrders() {
    fetch('load_orders.php') // Updated to use load_orders.php
        .then(response => response.json())
        .then(data => {
            if (data.status === '1') {
                allActiveOrders = parseOrders(data.active_orders_html); // Parse active orders into objects
                allSuccessfulOrdersHtml = data.successful_orders_html; // Store successful orders HTML
                displayOrders(currentPage);
                updatePaginationControls();
            } else {
                console.error("Error loading sell orders:", data.message);
            }
        })
        .catch(error => console.error("Error loading sell orders:", error));
}

// Parse HTML into an array of order objects
function parseOrders(html) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const orders = [];

    const orderItems = tempDiv.querySelectorAll('.sell-order-item');
    orderItems.forEach(item => {
        const amountText = item.querySelector('strong').nextSibling.textContent.trim(); // Amount
        const priceText = item.querySelectorAll('strong')[1].nextSibling.textContent.trim(); // Price
        const dateText = item.querySelectorAll('strong')[2] ? 
            item.querySelectorAll('strong')[2].nextSibling.textContent.trim() : 
            new Date().toISOString(); // Default to now if no date is present

        // Assuming USDT Receiver Address is the next sibling of the last strong tag
        const usdtReceiverAddress = item.querySelector('strong:last-of-type').nextSibling.textContent.trim();

        orders.push({
            amount: parseFloat(amountText),
            price_in_usdt: parseFloat(priceText),
            date: new Date(dateText),
            usdt_receiver_address: usdtReceiverAddress
        });
    });

    return orders;
}

// Display orders based on current page
function displayOrders(page) {
    const sellOrdersDiv = document.getElementById('sellOrders');
    sellOrdersDiv.innerHTML = ''; // Clear previous orders

    const startIndex = (page - 1) * ordersPerPage;
    const endIndex = startIndex + ordersPerPage;

    const paginatedActiveOrders = allActiveOrders.slice(startIndex, endIndex);

    paginatedActiveOrders.forEach(order => {
        const orderDiv = document.createElement('div');
        orderDiv.classList.add('sell-order-item');
        orderDiv.innerHTML = `
            <p><strong>Amount:</strong> ${order.amount} Tahcoin</p>
            <p><strong>Price:</strong> ${order.price_in_usdt} USDT</p>
            <button onclick="copyToClipboard('${order.price_in_usdt}')">📋</button>
            <p><strong>USDT Receiver Address:</strong> ${order.usdt_receiver_address}</p>
            <button onclick="copyToClipboard('${order.usdt_receiver_address}')">📋</button>
            <button onclick="displayPurchaseInstructions('${order.amount}', '${order.usdt_receiver_address}')">Buy</button>
        `;
        sellOrdersDiv.appendChild(orderDiv);
    });
}

// Update pagination controls
function updatePaginationControls() {
    const totalPages = Math.ceil(allActiveOrders.length / ordersPerPage);
    
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage >= totalPages;

    document.getElementById('pageInfo').innerText = `Page ${currentPage} of ${totalPages}`;
}

// Event listeners for pagination buttons
document.getElementById('prevPage').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        displayOrders(currentPage);
        updatePaginationControls();
    }
});

document.getElementById('nextPage').addEventListener('click', () => {
    const totalPages = Math.ceil(allActiveOrders.length / ordersPerPage);
    
    if (currentPage < totalPages) {
        currentPage++;
        displayOrders(currentPage);
        updatePaginationControls();
    }
});

// Event listener for filter button
document.getElementById('applyFilters').addEventListener('click', () => {
    const priceFilter = document.getElementById('filterPrice').value;
    const dateFilter = document.getElementById('filterDate').value;

    let filteredOrders = [...allActiveOrders]; // Copy all active orders for filtering

    // Apply price filter
    if (priceFilter === 'highest') {
        filteredOrders.sort((a, b) => b.price_in_usdt - a.price_in_usdt);
    } else if (priceFilter === 'lowest') {
        filteredOrders.sort((a, b) => a.price_in_usdt - b.price_in_usdt);
    }

    // Apply date filter
    if (dateFilter === 'newest') {
        filteredOrders.sort((a, b) => b.date - a.date);
    } else if (dateFilter === 'oldest') {
        filteredOrders.sort((a, b) => a.date - b.date);
    }

    currentPage = 1; // Reset to first page after filtering
    allActiveOrders = filteredOrders; // Update stored active orders with filtered results
    displayOrders(currentPage); // Display filtered results
});

// Polling for updates every 29 seconds
setInterval(loadSellOrders, 29000);

// Load sell orders on page load
window.onload = loadSellOrders;

// Function to handle buying Tahcoin
function displayPurchaseInstructions(amount, usdtReceiverAddress) {
    const buyerAddress = prompt("Please enter your Tahcoin wallet address:");

    if (!buyerAddress) {
        alert("You must enter a wallet address.");
        return;
    }

    // Show instructions in UI
    const instructionDiv = document.createElement('div');
    instructionDiv.innerHTML = `
        <p>Are you sure you want to buy ${amount} Tahcoin? Please send ${amount} USDT to ${usdtReceiverAddress}.</p>
        <button onclick='validateTransaction("${usdtReceiverAddress}", "${amount}", "${buyerAddress}")'>Validate Transaction</button>
    `;
    
    document.getElementById('message').innerHTML = ''; // Clear previous messages
    document.getElementById('message').appendChild(instructionDiv);
}

// Function to validate USDT transaction
async function validateTransaction(receiverAddress, amount, buyerAddress) {
    const isValid = await validateUSDTTransaction(receiverAddress, amount);
    
    if (isValid) {
        alert("USDT transaction validated! Proceeding with purchase...");
        
        // Proceed with buying process by creating a transaction
        createTahcoinTransaction(buyerAddress, receiverAddress, amount);
        
    } else {
        alert("USDT transaction validation failed.");
        showFetchedTransactions(receiverAddress, amount);
    }
}

// Function to create a Tahcoin transaction using API 313
async function fetchSellerKeys(usdtReceiverAddress) {
    const response = await fetch('get_seller_keys.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ usdt_receiver_address: usdtReceiverAddress })
    });

    if (response.ok) {
        const keys = await response.json(); // Return the seller keys as JSON
        return keys; // Return keys
    }
    
    console.error("Failed to fetch seller keys:", response.statusText);
    return null; // Return null if fetching keys fails
}

async function createTahcoinTransaction(buyerAddress, sellerUsdtAddress, expectedAmount) {
    // Fetch seller's public and private keys from the database
    const sellerKeys = await fetchSellerKeys(sellerUsdtAddress);
    
    if (!sellerKeys) {
        alert("Failed to retrieve seller keys.");
        return;
    }

    const publicKey = sellerKeys.public_key; // Adjust this line if necessary
    const privateKey = sellerKeys.private_key; // Adjust this line if necessary

    // Prepare data for API 313 exactly as in the cURL command
    const apiData = new URLSearchParams();
    apiData.append('public_key', publicKey); // Use public key as is
    apiData.append('private_key', privateKey); // Use private key as is (with spaces)
    apiData.append('receiver_address', buyerAddress); // Ensure this is the correct receiver address
    apiData.append('amount', expectedAmount); // Ensure this is the correct amount
    
    try {
        const response = await fetch('https://tahriver.online/api_313.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: apiData.toString() // Send data exactly as formatted
        });

        // Check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get raw response text
        const rawResponseText = await response.text();

        // Extract JSON part from the raw response using regex
        const jsonResponseMatch = rawResponseText.match(/({.*})/);
        
        let jsonResponse;
        
        if (jsonResponseMatch) {
            jsonResponse = jsonResponseMatch[1]; // Get the matched JSON part
            
            // Attempt to parse the cleaned JSON response
            let data;
            try {
                data = JSON.parse(jsonResponse);
                
                // Check for success message
                if (data.message && data.message.includes("Transaction response")) {
                    document.getElementById('message').innerText = "Transaction successful: " + data.message;
                    moveSellOrderToSuccessful(sellerUsdtAddress, expectedAmount);
                    loadSellOrders();
                } else {
                    document.getElementById('message').innerText = "Transaction failed: " + (data.error || "Unknown error");
                }
                
            } catch (e) {
                console.error("Error parsing JSON response:", e);
                document.getElementById('message').innerText = "Received an invalid JSON response from the server.";
            }
            
        } else {
            console.error("No valid JSON found in response:", rawResponseText);
            document.getElementById('message').innerText = "Received an invalid response from the server.";
        }
        
    } catch (error) {
        console.error("Error during transaction:", error);
        document.getElementById('message').innerText = "Error occurred while processing the transaction.";
    }
}

// Function to fetch seller's public and private keys from the database
async function fetchSellerKeys2(usdtReceiverAddress) {
    const response = await fetch('get_seller_keys.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ usdt_receiver_address: usdtReceiverAddress })
    });

    if (response.ok) {
        return response.json();
    }
    
    return null; // Return null if fetching keys fails
}

// Function to move sell order to successful orders in DB
async function moveSellOrderToSuccessful(usdtReceiverAddress, amount) {
    const response = await fetch('move_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ usdt_receiver_address: usdtReceiverAddress, amount })
    });

    if (!response.ok) {
        console.error("Failed to move order:", response.statusText);
    }
}

// Example function to calculate the actual amount (you can customize this)
function calculateActualAmount(expectedAmount) {
    // Logic to determine how much Tahcoin to send
    return expectedAmount; // For example, returning expected amount directly
}

// Function to copy address to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert("USDT receiver address copied to clipboard!");
    }, () => {
        alert("Failed to copy address.");
    });
}

// Function to validate USDT transaction and show results in UI
async function validateUSDTTransaction(receiverAddress, amount) {
    const apiKey = 'your_polygonscan_api_key'; // Replace with your actual API key
    const url = `https://api.polygonscan.com/api?module=account&action=txlist&address=${receiverAddress}&startblock=0&endblock=99999999&page=1&offset=99&sort=asc&apikey=${apiKey}`;
    
    const response = await fetch(url);
    const data = await response.json();

    if (data.status === '1' && data.result.length > 0) {
        let matchFound = false;
        
        data.result.forEach(transaction => {
            const transactionValueInWei = parseFloat(transaction.value);
            const expectedValueInWei = parseFloat(amount * 1e18); // Convert amount to Wei
            
            const transactionDiv = document.createElement('div');
            
            if (
                transaction.to.toLowerCase() === receiverAddress.toLowerCase() &&
                transactionValueInWei === expectedValueInWei
            ) {
                matchFound = true; // Transaction is valid
                transactionDiv.style.color = "green"; // Match found
                transactionDiv.innerText = `Matched Transaction Hash: ${transaction.hash} - Value: ${transactionValueInWei} Wei`;
            } else {
                transactionDiv.style.color = "red"; // No match found
                transactionDiv.innerText = `Non-Matching Transaction Hash: ${transaction.hash} - Value: ${transactionValueInWei} Wei`;
            }

            document.getElementById('message').appendChild(transactionDiv);
        });

        return matchFound; // Return whether any match was found
    }
    
    return false; // No valid transaction found
}

// Load sell orders on page load
window.onload = loadSellOrders;