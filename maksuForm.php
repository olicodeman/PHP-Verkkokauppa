<?php
$cart = $_SESSION['cart'] ?? [];
$totalPrice = $_SESSION['cart_total'] ?? 0;

	require_once('auth.php');
	require_once('config.php');

	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysqli_select_db($link, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

	$qry = "SELECT address, phonenumber FROM Members WHERE member_id = " . intval($_SESSION['SESS_MEMBER_ID']);
	$result = mysqli_query($link, $qry);
	
	if ($result) {
		$userDetails = mysqli_fetch_assoc($result); 
		$address = $userDetails['address'] ?? 'Ei tiedossa'; 
		$phoneNumber = $userDetails['phonenumber'] ?? 'Ei tiedossa'; 
	}
?>


<body>
<form>
<div style="text-align: center;">
	<h1>Tilaus vahvistus</h1>

<?php if (empty($cart)): ?>
        <p>Ostoskorisi on tyhjä.</p>
    <?php else: ?>
        <?php foreach ($cart as $item): ?>
            <p>
                <?= htmlspecialchars($item['name']) ?> - 
                €<?= number_format($item['price'], 2) ?> 
                x <?= $item['quantity'] ?>
            </p>
        <?php endforeach; ?>
        <p>Kokonaishinta: €<?= number_format($totalPrice, 2) ?></p>
    <?php endif; ?>


	<div class="profile-content-box">
		<form>
			<label for="fname">Etunimi</label>
			<input size="30" name="fname" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?>" readonly></input>

			<label for="lname">Sukunimi</label>
			<input size="30" name="lname" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_LAST_NAME']);?>" readonly></input>

			<label for="osoite">Osoite</label>
			<input size="30" name="osoite" type="text" value="<?php echo htmlspecialchars($address);?>" readonly></input>

			<label for="sähköposti">Sähköpostiosoite</label>
			<input size="30" name="sähköposti" type="text" value="<?php echo htmlspecialchars($_SESSION['SESS_EMAIL']);?>" readonly></input>

			<label for="puhelin">Puhelinnumero</label>
			<input size="30" name="puhelin" type="text" value="<?php echo htmlspecialchars($phoneNumber);?>" readonly></input>
		</form>
        <br>
		<p>Eikö tiedot ole oikein? Vaihda tietoja <a class="edit-btn" href="index.php?page=user-edit">Täältä</a>.</p>
		<br>
		<a class="edit-btn" href="tilausKasittely.php">Vahvista tilaus</a>
	</div>
</div>
</form>
</body>

    

