<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahcoin DEX</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Tahcoin Decentralized Exchange</h1>
<!-- Day/Night Mode Toggle -->
        <div class="mode-toggle" id="modeToggle" title="Toggle Day/Night Mode">
            ☀️
        </div>
        <!-- Tahcoin Price and Market Cap Section -->
        <div class="tahcoin-info">
            <h2>Tahcoin Information</h2>
            <p><strong>Price:</strong> $1.00 @ 0.000015 BTC (0.00%)</p>
        </div>
        <h2>Set Your Dex Wallet</h2>
        <div class="wallet-info">
            <label for="public_key">Public Key:</label>
            <input type="text" id="public_key" placeholder="Enter your public key">

            <label for="private_key">Private Key:</label>
            <input type="password" id="private_key" placeholder="Enter your private key">
        </div>

        <div class="sell-order">
            <h2>Create Sell Order</h2>
            <label for="amount">Amount of Tahcoin:</label>
            <input type="number" id="amount" placeholder="Enter amount">

            <label for="usdt_receiver_address">USDT Receiver Address:</label>
            <input type="text" id="usdt_receiver_address" placeholder="Enter USDT receiver address">

            <button id="createOrder">Create Sell Order</button>
        </div>

        <h2>Available Sell Orders</h2>

        <div class="filter-options">
            <label for="filterPrice">Filter by Price:</label>
            <select id="filterPrice">
                <option value="all">All</option>
                <option value="highest">Highest to Lowest</option>
                <option value="lowest">Lowest to Highest</option>
            </select>

            <label for="filterDate">Filter by Date:</label>
            <select id="filterDate">
                <option value="all">All</option>
                <!--option value="newest">Newest First</option-->
                <!--option value="oldest">Oldest First</option-->
            </select>

            <button id="applyFilters">Apply Filters</button>
        </div>

        <div id="error-container" style="color: red;"></div>
        <!-- Message Display -->
        <div id="message"></div>
        <!-- Sell Orders List -->
        <div id="sellOrders"></div>

        <div id="paginationControls">
            <button id="prevPage" disabled>Previous</button>
            <span id="pageInfo"></span>
            <button id="nextPage">Next</button>
        </div>
    </div>
    <!-- Include your script -->
    <script src="app.9.9.9.5.js"></script>
    <script>
    document.getElementById('modeToggle').addEventListener('click', function() {
   document.body.classList.toggle('light-mode');
});
</script> 
</body>
</html>