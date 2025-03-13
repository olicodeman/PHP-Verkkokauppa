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
    <title>Ostoskori</title>
    

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
