<?php
session_start();
$cart = $_SESSION['cart'] ?? []; // Noudetaan ostoskori sessiosta


//tuotteen poisto ostoskorista
if(isset($_POST['remove'])) {
    $indexToRemove = intval ($_POST['index']); //haetaan tuotteen indexi
    unset($cart[$indexToRemove]);//tuotteen poisto
    $_SESSION['cart'] = array_values($cart);//päivitetään sessio ja korjataan indexit
}
// Lasketaan kokonaishinta
$totalPrice = 0;
foreach ($cart as $item) {
    // Varmistetaan, että 'price' ja 'quantity' ovat asetettuina
    if (isset($item['price']) && isset($item['quantity'])) { 
        $totalPrice += $item['price'] * $item['quantity']; 
    }
}

// Tallennetaan kokonaishinta sessioon
$_SESSION['cart_total'] = $totalPrice;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ostoskori</title>
    <style>
      /* General Body Style for Cart Page */
body {
    background-image: url('https://live.staticflickr.com/8230/8410882716_2604e5af6b_b.jpg');
    background-size: cover;
    font-family: 'Arial', sans-serif;
    color: white;
}

 /* Ei saa siirtää pois tältä sivulta */
 form {
            background-color:white;  /* Muutetaan lomakkeen taustaväri */
            padding: 10px;
            border-radius: 5px;
            color: white;
        }

/* Cart Container */
.cart {
    max-width: 800px;
    margin: 30px auto;
    padding: 20px;
    background-color: rgba(45, 45, 102, 0.8);
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Cart Item Style */
.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
    background-color: #f9f9f9;
    transition: transform 0.3s ease-in-out;
}

.cart-item:hover {
    transform: scale(1.02);
}

/* Cart Item Image */
.cart-item img {
    max-width: 120px;
    height: auto;
    border-radius: 8px;
    margin-right: 20px;
}

/* Cart Item Details */
.cart-item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.cart-item-details h3 {
    margin: 0;
    font-size: 1.2em;
    font-weight: bold;
    color: #333;
}

.cart-item-details p {
    margin: 5px 0;
    font-size: 1em;
    color: #555;
}

/* Remove Button Style */
.cart-item .remove-btn {
    background-color: #ff4d4d;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.cart-item .remove-btn:hover {
    background-color: #ff3333;
}

/* Cart Total Section */
.cart-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.2em;
    margin-top: 20px;
    padding: 10px;
    background-color: rgba(45, 45, 102, 0.9);
    border-radius: 8px;
}

/* Checkout Button */
.checkout-btn {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1em;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.checkout-btn:hover {
    background-color: #218838;
}

    </style>
</head>
<body>
<div class="cart">
    <h1>Ostoskori</h1>
    <?php if (empty($cart)): ?>
        <p>Ostoskorisi on tyhjä.</p>
    <?php else: ?>
        <?php foreach ($cart as $index => $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="cart-item-details">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p>Hinta: €<?= number_format($item['price'], 2) ?></p>
                    <p>Määrä: <?= htmlspecialchars($item['quantity']) ?></p>
                    <p>Yhteensä: €<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                </div>
                <form method="POST">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="remove" class="remove-btn">Poista</button>
                </form>
            </div>
        <?php endforeach; ?>
        <div class="cart-total">
            <p>Kokonaishinta: €<?= number_format($totalPrice, 2) ?></p>
        </div>
        <a href="index.php?page=maksuForm" class="checkout-btn">Maksamaan</a>
    <?php endif; ?>
</div>
</body>
</html>
