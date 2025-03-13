<?php
require_once('auth.php');
require_once('config.php');

// Check if the cart is empty
if (empty($_SESSION['cart']) || empty($_SESSION['cart_total'])) {
    header('Location: index.php?page=error');
    exit();
}

$token = bin2hex(random_bytes(16)); // Generate a secure token
$_SESSION['order_token'] = $token; // Store the token in the session

$cart = $_SESSION['cart'] ?? [];
$totalPrice = $_SESSION['cart_total'] ?? 0;


	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysqli_select_db($link, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

	$qry = "SELECT address, phonenumber FROM members WHERE member_id = " . intval($_SESSION['SESS_MEMBER_ID']);
	$result = mysqli_query($link, $qry);
	
	if ($result) {
		$userDetails = mysqli_fetch_assoc($result); 
		$address = $userDetails['address'] ?? 'Ei tiedossa'; 
		$phoneNumber = $userDetails['phonenumber'] ?? 'Ei tiedossa'; 
	}
?>




<form id="paymentForm" action="tilausKasittely.php?token=<?= htmlspecialchars($token)?>" method="POST">
    <div style="text-align: center;">
        <h1><?= $current_lang['OrderConfirm']; ?></h1>
<input type="hidden" id="Maksutapa_en" name="Maksutapa_en">
        <?php if (empty($cart)): ?>
            <p><?= $current_lang['CartEmpty']; ?></p>
        <?php else: ?>
            <?php foreach ($cart as $item): ?>
                <p>
                    <?= htmlspecialchars($item['name']) ?> - 
                    €<?= number_format($item['price'], 2) ?> 
                    x <?= $item['quantity'] ?>
                </p>
            <?php endforeach; ?>
            <p><?= $current_lang['Total']; ?>: €<?= number_format($totalPrice, 2) ?></p>
        <?php endif; ?>

        <div class="profile-content-box">
            <label for="fname"><?= $current_lang['FirstName']; ?></label>
            <input class="resize" size="30" id="fname" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?>" readonly>

            <label for="lname"><?= $current_lang['LastName']; ?></label>
            <input class="resize" size="30" id="lname" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_LAST_NAME']);?>" readonly>

            <label for="osoite"><?= $current_lang['Address']; ?></label>
            <input class="resize" size="30" id="osoite" type="text" value="<?php echo htmlspecialchars($address);?>" readonly>

            <label for="sähköposti"><?= $current_lang['Email']; ?></label>
            <input class="resize" size="30" id="sähköposti" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_EMAIL']);?>" readonly>

            <label for="puhelin"><?= $current_lang['PhoneNmb']; ?></label>
            <input class="resize" size="30" id="puhelin" type="text" value="<?php echo htmlspecialchars($phoneNumber);?>" readonly>

            <p> <?= $current_lang['CorrectInfo']; ?><a href="index.php?page=user-edit"><?= $current_lang['Here']; ?></a>.</p>

            <h3><?= $current_lang['ChoosePayment']; ?></h3>
            <input type="radio" name="choice" value="Kortti" onchange="toggleVisibility()" required> <?= $current_lang['Card']; ?>
            <input type="radio" name="choice" value="Lasku" onchange="toggleVisibility()" required><?= $current_lang['Check']; ?>
            <br><br>

            <!-- Payment fields for Kortti -->
            <div id="contentOption1" class="hidden">
                <label for="crdnmb"><?= $current_lang['CardNumber']; ?>: </label>
                <input id="crdnmb" name="crdnmb" type="text" minlength="16" maxlength="16" style="width: 200px; margin-bottom: 10px" oninput="validateDigits(event)">

                <label for="cvv">CVV:</label>
                <input id="cvv" name="cvv" type="text" minlength="3" maxlength="3" style="width: 25px; margin-bottom: 10px" oninput="validateDigits(event)">

                <label for="expdate"><?= $current_lang['ExpirationDate']; ?>:</label>
                <input id="expdate" name="expdate" type="month">
            </div>

            <!-- Payment fields for Lasku -->
            <div id="contentOption2" class="hidden">
                <label for="lo"><?= $current_lang['Address']; ?>:</label>
                <input id="lo" name="lo" type="text" style="width: 200px;">
            </div>

            <h4><?= $current_lang['Delivery']; ?></h4>
            <input type="radio" name="choice2" value="Postitus" required> <?= $current_lang['Deliver']; ?>
            <input type="radio" name="choice2" value="Nouto" required> <?= $current_lang['Fetch']; ?>
            <br><br>


            <!-- Submit Button -->
            <button class="edit-btn" type="submit"><?= $current_lang['ConfirmOrder']; ?></button>
        </div>
    </div>
</form>



<script>

   function toggleVisibility() {
    var option1 = document.querySelector('input[name="choice"][value="Kortti"]:checked');
    var option2 = document.querySelector('input[name="choice"][value="Lasku"]:checked');
    
    // Show or hide content based on the selected option
    if (option1) {
        document.getElementById('contentOption1').classList.remove('hidden');
        document.getElementById('contentOption2').classList.add('hidden');
        
        // Set Maksutapa_en value for Kortti
        document.getElementById('Maksutapa_en').value = 'Card';
        
        // Remove 'required' from 'Lasku' fields
        document.getElementById('lo').removeAttribute('required');
    } else if (option2) {
        document.getElementById('contentOption1').classList.add('hidden');
        document.getElementById('contentOption2').classList.remove('hidden');
        
        // Set Maksutapa_en value for Lasku
        document.getElementById('Maksutapa_en').value = 'Invoice';
        
        // Add 'required' to 'Lasku' fields
        document.getElementById('lo').setAttribute('required', 'required');
    } else {
        document.getElementById('contentOption1').classList.add('hidden');
        document.getElementById('contentOption2').classList.add('hidden');
        
        // Clear Maksutapa_en value if no payment method is selected
        document.getElementById('Maksutapa_en').value = '';
    }
}
	window.onload = toggleVisibility;

	document.getElementById('paymentForm').addEventListener('submit', function(event) {
    var paymentMethodSelected = document.querySelector('input[name="choice"]:checked');
    if (!paymentMethodSelected) {
        alert('Valitse maksutapa.');
        event.preventDefault();
        return false;
    }

    // Check required fields based on payment method
    if (paymentMethodSelected.value === 'Kortti') {
        var cardNumber = document.getElementById('crdnmb').value;
        var cvv = document.getElementById('cvv').value;
        var expDate = document.getElementById('expdate').value;
        if (!cardNumber || !cvv || !expDate) {
            alert('Täytä kaikki kortin tiedot.');
            event.preventDefault();
            return false;
        }
    } else if (paymentMethodSelected.value === 'Lasku') {
        var billingAddress = document.getElementById('lo').value;
        if (document.getElementById('contentOption2').style.display !== 'none' && !billingAddress) {
            alert('Syötä laskuosoite.');
            event.preventDefault();
            return false;
        }
    }

    // Check if a delivery method is selected
    var deliveryOptionSelected = document.querySelector('input[name="choice2"]:checked');
    if (!deliveryOptionSelected) {
        alert('Valitse toimitustapa.');
        event.preventDefault();
        return false;
    }

    return true;
    });

	function validateDigits(event) {
    const input = event.target;
    // Allow only digits, no other characters, and restrict the input to exactly 16 digits
    input.value = input.value.replace(/[^0-9]/g, '').slice(0, 16);
  }
  </script>


