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

	$qry = "
    SELECT 
        t.order_id,
        p.nimi AS nimi,
        op.quantity,
        (p.hinta * op.quantity) AS total_price
    FROM 
        tilaukset t
    INNER JOIN 
        tilaus_tuotteet op ON t.order_id = op.order_id
    INNER JOIN 
        tuotteet p ON op.product_id = p.id
    WHERE 
        t.member_id = " . intval($_SESSION['SESS_MEMBER_ID']);

		$result = mysqli_query($link, $qry);

		$orderDetails = [];
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$orderDetails[] = $row;
			}
		}
?>

<style>
	.profile-content-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px; 
}

.profile-content-box {
    width: 48%; 
    box-sizing: border-box; 
    padding: 20px;
    color: white;
    border-radius: 8px;
    border: 1px solid black;
}
.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 16px;
    text-align: left;
}

/* Table headers */
.orders-table th {
    background-color: darkslateblue;
    color: white;
    padding: 12px 15px;
    border: 1px solid #ddd;
}

/* Table rows */
.orders-table td {
    padding: 10px 15px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

</style>
<body>
<div style="text-align: center;">
    <h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?></h1>
    <a href="index.php?page=logout">Kirajudu ulos</a>
    
    <div class="profile-content-container">
        <div class="profile-content-box">
            <h2 style="color: black;">Sinun tiedot:</h2>
            <p>Nimi: <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?> <?php echo htmlspecialchars($_SESSION['SESS_LAST_NAME']);?></p>
            <p>Sähköposti: <?php echo htmlspecialchars($_SESSION['SESS_EMAIL']);?></p>
            <p>Osoite: <?php echo htmlspecialchars($address); ?></p>
            <p>Puhelinnumero: <?php echo htmlspecialchars($phoneNumber); ?></p>
            <p>Käyttäjänimi: <?php echo htmlspecialchars($_SESSION['SESS_LOGIN']);?></p>
            <a class="edit-btn" href="index.php?page=user-edit">Muuta tietojasi</a>
        </div>

        <div class="profile-content-box">
    <h2 style="color: black;">Tehdyt tilauksesi:</h2>
    <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black; ">
        <thead>
            <tr>
                <th>Tilauksen ID</th>
                <th>Tuotteen nimi</th>
                <th>Määrä</th>
                <th>Hinta</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orderDetails)): ?>
                <?php foreach ($orderDetails as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['nimi']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Ei tilauksia</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</div>
</body>

