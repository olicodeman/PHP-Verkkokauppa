<?php  
    require_once('config.php');

    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    // Haetaan tuotteet tietokannasta
    try {
        $stmt = $pdo->prepare("SELECT id, nimi, kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
        $stmt->execute();
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
        $products = [];
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ostoskori</title>
    <style>
        /* Your existing CSS, including the cart container styling, will be used here */

        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .cart-header {
            text-align: center;
            font-size: 2em;
            color: darkslateblue;
            margin-bottom: 20px;
        }

        .cart-items {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            padding: 15px;
            transition: transform 0.2s ease-in-out;
        }

        .cart-item:hover {
            transform: scale(1.05);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-details {
            flex-grow: 1;
            margin-left: 20px;
        }

        .cart-item-details h3 {
            font-size: 1.2em;
            color: darkslateblue;
        }

        .cart-item-details p {
            margin: 5px 0;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 120px;
        }

        .quantity-btn {
            background-color: darkslateblue;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .quantity-btn:hover {
            background-color: slateblue;
        }

        .cart-item-total {
            font-size: 1.2em;
            color: darkslateblue;
        }

        .cart-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2em;
        }

        .checkout-btn {
            background-color: darkslateblue;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: slateblue;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Ostoskori</h1>
        </div>

        <!-- Cart items grid -->
        <div class="cart-items">
            <!-- Example cart item -->
            <div class="cart-item">
                <img src="product-image.jpg" alt="Product Image">
                <div class="cart-item-details">
                    <h3>Tuote Nimi</h3>
                    <p>Product description here</p>
                    <p>€ Price</p>
                </div>
                <div class="cart-item-quantity">
                    <button class="quantity-btn">-</button>
                    <span>1</span>
                    <button class="quantity-btn">+</button>
                </div>
                <div class="cart-item-total">
                    € Total Price
                </div>
            </div>
            <!-- Repeat cart items dynamically here -->
        </div>

        <!-- Cart footer with total price and checkout button -->
        <div class="cart-footer">
            <div class="cart-total">
                <b>Total: € 100.00</b>
            </div>
            <a href="checkout.php" class="checkout-btn">Checkout</a>
        </div>
    </div>
</body>
</html>
