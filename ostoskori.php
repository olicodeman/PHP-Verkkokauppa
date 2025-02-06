<?php
session_start();
$cart = $_SESSION['cart'] ?? []; // Noudetaan ostoskori sessiosta

//tuotteen poisto ostoskorista
if (isset($_POST['remove'])) {
    $indexToRemove = intval($_POST['index']); //haetaan tuotteen indexi
    unset($cart[$indexToRemove]); //tuotteen poisto
    $_SESSION['cart'] = array_values($cart); //päivitetään sessio ja korjataan indexit
}

// Päivitetään varastomäärä
if (isset($_POST['update_quantity']) && isset($_POST['index'])) {
    $indexToUpdate = intval($_POST['index']);
    $newQuantity = intval($_POST['quantity']);

    // päivitetään määrä ostoskorissa
    $cart[$indexToUpdate]['quantity'] = $newQuantity;
    $_SESSION['cart'] = $cart;
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
        /* Yleinen tyyli ostoskorille */
        body {
            background-image: url('https://live.staticflickr.com/8230/8410882716_2604e5af6b_b.jpg');
            background-size: cover;
            font-family: 'Arial', sans-serif;
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


        /* Modal tyylit */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: rgba(45, 45, 102, 0.8);
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* tyyli napeille */
        .modal-btn {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 7px;
            cursor: pointer;
        }

        .modal-btn:hover {
            background-color: #218838;
        }


        .quantity-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* tyyli määrän näyttämiseen */
        #quantity-display {
            font-size: 1.2em;
            font-weight: bold;
            padding: 0 10px;
        }


        /* ostoskorin tyyli */
        .cart-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.2em;
            margin-top: 20px;
            padding: 10px;
            background-color: rgba(45, 45, 102, 0.9);
            border-radius: 8px;
        }

        /* maksu nappi*/
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

        /* Action Form: napit vierekkäin*/
        .action-form {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 5px 0;
            background-color: rgba(45, 45, 102, 0.8);
            padding: 10px 20px;
            border-radius: 5px;
        }

        /* Tyyli poistamiselle ja muutos napit */
        .action-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s, transform 0.2s;
            flex-grow: 1;
            text-align: center;
        }

        /* Poistonapin oma tyyli */
        .remove-btn {
            background-color: #ff4d4d;
        }

        .remove-btn:hover {
            background-color: #ff3333;
            transform: scale(1.05);
        }

        /* Muutos napin oma tyyli*/
        .update-btn {
            background-color: #28a745;
        }

        .update-btn:hover {
            background-color: #e0a800;
            transform: scale(1.05);
        }

        /* Kummankin napin hover efekti*/
        .action-btn:hover {
            transform: scale(1.1);
        }
        @media (max-width: 500px) {
            .action-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 10px;
            border-radius: 8px;
            font-size: 10px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s, transform 0.2s;
            text-align: center;
        }
        .action-form {
            gap: 5px;
            background-color: rgba(45, 45, 102, 0.8);
            padding: 5px 10px;
            border-radius: 5px;
        }
        .remove-btn {
            background-color: #ff4d4d;
        }
        .cart-item img {
            max-width: 80px;
        }
        .cart-item {
            padding: 10px;
        }
        .cart {
            max-width: 500px;
        }
        }
        @media (max-width: 400px) {
            .action-btn {
            padding: 5px 10px;
            font-size: 10px;
        }
        .cart-item img {
            max-width: 60px;
        }
        }
    </style>
</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body>
    <div class="cart">
        <h1><?= $current_lang['Cart']; ?></h1>
        <?php if (empty($cart)): ?>
            <p><?= $current_lang['CartEmpty']; ?></p>
        <?php else: ?>
            <?php foreach ($cart as $index => $item): ?>
                <div class="cart-item">
                    <img src="<?= isset($item['image']) ? htmlspecialchars($item['image']) : 'default.jpg' ?>"
                        alt="<?= isset($item['name']) ? htmlspecialchars($item['name']) : 'Unknown Product' ?>">
                    <div class="cart-item-details">
                        <h3><?= isset($item['name']) ? htmlspecialchars($item['name']) : 'Unknown Product' ?></h3>
                        <p><?= $current_lang['price']; ?>: €<?= isset($item['price']) ? number_format($item['price'], 2) : '0.00' ?></p>
                        <p><?= $current_lang['quantity']; ?>: <?= isset($item['quantity']) ? htmlspecialchars($item['quantity']) : '0' ?></p>
                        <p><?= $current_lang['Total']; ?>:
                            €<?= isset($item['price']) && isset($item['quantity']) ? number_format($item['price'] * $item['quantity'], 2) : '0.00' ?>
                        </p>
                    </div>

                    <form method="POST" class="action-form">
                        <input type="hidden" name="index" value="<?= $index ?>">
                        <button type="submit" name="remove" class="action-btn remove-btn"><?= $current_lang['Remove']; ?></button>
                        <button type="button" class="action-btn update-btn" onclick="openModal(<?= $index ?>)"><?= $current_lang['ChangeQuantity']; ?></button>
                    </form>
                </div>
            <?php endforeach; ?>


            <div class="cart-total">
                <p><?= $current_lang['Total']; ?>: €<?= number_format($totalPrice, 2) ?></p>
            </div>
            <a href="index.php?page=maksuForm" class="checkout-btn"><?= $current_lang['Pay']; ?></a>
        <?php endif; ?>
    </div>

    <!-- Modal määrän muuttamiseen  -->
    <div class="modal" id="quantity-modal">
        <div class="modal-content">
            <h3><?= $current_lang['ChangeQuantity']; ?></h3>
            <div class="quantity-buttons">
                <button id="decrease-btn" onclick="updateQuantity(-1)" class="modal-btn">-</button>
                <span id="quantity-display">1</span>
                <button id="increase-btn" onclick="updateQuantity(1)" class="modal-btn">+</button>
            </div>
            <button onclick="saveQuantity()" class="modal-btn"><?= $current_lang['Save']; ?></button>
            <button onclick="closeModal()" class="modal-btn"><?= $current_lang['Close']; ?></button>
        </div>
    </div>

    <script>
        //Määritellään muuttuja nykyiselle indeksille
        let currentIndex = null;

        //näyttää modalin ja asettaa tuotteen valitun määrän
        function openModal(index) {
            currentIndex = index;
            const quantity = <?= json_encode(array_column($cart, 'quantity')) ?>[currentIndex];
            document.getElementById('quantity-display').textContent = quantity;
            document.getElementById('quantity-modal').style.display = 'flex';
        }
        //piilotetaan modal
        function closeModal() {
            document.getElementById('quantity-modal').style.display = 'none';
        }
        //päivitetään tuotteen määrä tarkistamalla varasto ja nykyisen määrän
        function updateQuantity(amount) {
            const quantityDisplay = document.getElementById('quantity-display');
            let currentQuantity = parseInt(quantityDisplay.textContent);

            // Tarkistetaan varaston riittävyys 
            const stock = <?= json_encode(array_column($cart, 'stock')) ?>[currentIndex];
            if (currentQuantity + amount <= 0) {
                alert('<?= $current_lang['QuantityCanNotBe']; ?>');
                return;
            }
            if (currentQuantity + amount > stock) {
                alert('<?= $current_lang['NotEnoughProducts']; ?>');
                return;
            }
            quantityDisplay.textContent = currentQuantity + amount;
        }
        //tallentaa päivitetyn määrän palvelimelle lähettämällä lomakkeen
        function saveQuantity() {
            const newQuantity = parseInt(document.getElementById('quantity-display').textContent);
            const form = document.createElement('form');
            form.method = 'POST';
            const inputIndex = document.createElement('input');
            inputIndex.name = 'index';
            inputIndex.value = currentIndex;
            form.appendChild(inputIndex);

            //lisätään tarvittavat tiedot lomakkeeseen
            const inputQuantity = document.createElement('input');
            inputQuantity.name = 'quantity';
            inputQuantity.value = newQuantity;
            form.appendChild(inputQuantity);

            const inputUpdateQuantity = document.createElement('input');
            inputUpdateQuantity.name = 'update_quantity';
            inputUpdateQuantity.value = 'true';
            form.appendChild(inputUpdateQuantity);

            //lähettää lomakkeen
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>