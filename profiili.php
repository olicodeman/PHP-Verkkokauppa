<?php
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
<div style="text-align: center;">
	<h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?></h1>
	<a href="index.php?page=logout">Kirajudu ulos</a>
	<h2 style="color: white;">Sinun tiedot:</h2>
	<div class="profile-content-box">
		<p>Nimi: <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?> <?php echo htmlspecialchars($_SESSION['SESS_LAST_NAME']);?></p>
		<p>Sähköposti: <?php echo htmlspecialchars($_SESSION['SESS_EMAIL']);?></p>
		<p>Osoite: <?php echo htmlspecialchars($address); ?></p>
		<p>Puhelinnumero: <?php echo htmlspecialchars($phoneNumber); ?></p>
		<p>Käyttäjänimi: <?php echo htmlspecialchars($_SESSION['SESS_LOGIN']);?></p>
		<a class="edit-btn" href="index.php?page=user-edit">Muuta tietojasi</a>
	</div>
</div>
</body>

