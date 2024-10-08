# Tahcoin Decentralized Exchange (DEX)

## Description

Tahcoin DEX is a decentralized exchange platform allowing users to trade Tahcoin securely and efficiently.

## Features

- Create sell orders for Tahcoin.
- View available sell orders.
- Day/Night mode toggle.

## Usage

1. Enter your public and private keys.
2. Create sell orders by specifying the amount and USDT receiver address.
3. View available sell orders and filter them as needed.

## Setup Instructions

To set up the Tahcoin DEX on your local computer or server, follow these steps:

### 1. Clone the Repository

Clone this repository to your local machine using Git:

```bash
git clone https://github.com/TAHCOINBLOCKCHAIN/Tahcoin-Decentralized-Exchange.git
```

### 2. Configure Database Connection

Navigate to the `php` directory and open `db_connection.php`. Update the following variables with your database credentials:

```php
$servername = "localhost"; // Your database server
$username = "your_db_username"; // Your database username
$password = "your_db_password"; // Your database password
$dbname = "your_db_name"; // Your database name
```

### 3. Import Database Schema

You need to create a database for the DEX and import the SQL schema. Follow these steps:

1. Access your database management tool (e.g., phpMyAdmin).
2. Create a new database with the name you specified in `db_connection.php`.
3. Import `database.sql` from the project directory into your newly created database.

### 4. Configure API Endpoints

In the `php` directory, ensure that `api.php` and `load_orders.php` are correctly set up to interact with your database. No additional configuration is usually needed if you have set up your database connection properly.

## Transactions Verification

To add the Polygonscan API to your `app.9.9.9.5.js` file at line number 399, you will need to follow these steps:

### Step-by-Step Instructions

1. **Register for an API Key**:
   - Go to [Polygonscan Registration](https://polygonscan.com/register).
   - Create an account if you don’t have one, or log in if you already do.
   - After logging in, navigate to the API section of your account to generate an API key.

2. **Update Your JavaScript File**:
   - Open your `app.9.9.9.5.js` file.
   - Navigate to line 399 and add the following code snippet to initialize the Polygonscan API.

### Example Code Snippet

Here’s an example of how you might implement the Polygonscan API in your JavaScript file:

```javascript
// Line 399: Initialize Polygonscan API
const POLYGONSCAN_API_KEY = 'YOUR_API_KEY_HERE'; // Replace with your actual API key

async function fetchPolygonData(address) {
    const apiUrl = `https://api.polygonscan.com/api?module=account&action=balance&address=${address}&tag=latest&apikey=${POLYGONSCAN_API_KEY}`;

    try {
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
            throw new Error(`Error fetching data: ${response.statusText}`);
        }

        const data = await response.json();
        
        if (data.status === "1") {
            console.log(`Balance for address ${address}: ${data.result}`);
            return data.result; // Return the balance
        } else {
            console.error("Error fetching balance:", data.message);
            return null;
        }
    } catch (error) {
        console.error("Fetch error:", error);
    }
}

// Example usage
fetchPolygonData('0xYourPolygonAddressHere'); // Replace with the desired Polygon address
```

### Explanation of the Code

- **API Key**: Store your Polygonscan API key in a constant variable (`POLYGONSCAN_API_KEY`).
- **Function `fetchPolygonData`**: This function takes a Polygon address as input and constructs a URL to fetch the balance using the Polygonscan API.
- **Error Handling**: The function includes error handling to manage potential issues with the API request.
- **Example Usage**: Replace `'0xYourPolygonAddressHere'` with the actual address you want to query.

### Final Steps

1. **Replace `'YOUR_API_KEY_HERE'`** with the actual API key you obtained from Polygonscan.
2. **Test Your Implementation**: After adding this code, test it by calling `fetchPolygonData` with a valid Polygon address to ensure it retrieves the balance correctly.

By following these steps, you can successfully integrate the Polygonscan API into your application at line 399 of `app.9.9.9.5.js`.

### 5. Accessing the DEX

Once everything is set up, navigate to your web server's root directory and access the DEX through your web browser:

```
http://localhost/tahcoin-dex/index.php
```

Replace `localhost` with your server's IP address if you're hosting it remotely.

## Important Security Instructions

### Creating a New Wallet

1. **Create a New Wallet**: It is highly recommended to create a new wallet specifically for use with the Tahcoin DEX. This helps to isolate your trading activities from your main wallet, enhancing security.

2. **Use a Trusted Wallet Provider**: Choose a reputable wallet provider that supports Tahcoin. Follow their instructions to create a new wallet, ensuring you securely store your recovery phrase or private keys.

### Managing Your Tahcoin

- **Deposit Only What You Need**: When using the DEX, only send the amount of Tahcoin you plan to trade into your DEX wallet. This minimizes exposure and reduces the risk of loss.

- **Avoid Long-Term Storage**: Do not keep large amounts of Tahcoin in your DEX wallet for prolonged periods. After completing your trades, transfer any remaining balance back to your main wallet for safekeeping.

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) file for details.

### Key Additions

1. **Setup Instructions**: Detailed steps on how to clone the repository and configure it on a local machine or server.
2. **Database Configuration**: Instructions on how to set up `db_connection.php`, including where to put database credentials.
3. **Database Import**: Guidance on creating a database and importing `database.sql`.
4. **Accessing the DEX**: Clear instructions on how to access the DEX after setup.