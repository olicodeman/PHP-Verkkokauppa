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

	$qry = " SELECT order_id, order_date, total_price FROM tilaukset WHERE member_id = " . intval($_SESSION['SESS_MEMBER_ID']);

		$result = mysqli_query($link, $qry);


		$orderDetails = [];
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$orderDetails[] = $row;
			}
		}

        $products = [];
        if (isset($_GET['order_id'])) {
        $orderId = intval($_GET['order_id']);  // Ensure it's a valid integer

        $orderQry = "
        SELECT order_id, order_date, total_price
        FROM tilaukset
        WHERE order_id = $orderId
    ";
    
    $orderResult = mysqli_query($link, $orderQry);
    $orderData = mysqli_fetch_assoc($orderResult);
    
    // Only proceed if we have a valid order_id
    if ($orderId > 0) {
        // Fetch the products for the selected order
        $productQry = "
            SELECT op.quantity, op.price, p.nimi
            FROM tilaus_tuotteet op
            JOIN tuotteet p ON op.product_id = p.id
            WHERE op.order_id = $orderId
        ";

        $productResult = mysqli_query($link, $productQry);
        while ($product = mysqli_fetch_assoc($productResult)) {
            $products[] = $product;
        }
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
#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: none; /* Hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 999;
}
.oc-btn {
    background-color: darkslateblue;
    border-radius: 20px;
    transition: background ease-in 0.3s;
    padding: 10px;
}
.oc-btn:hover {
    background-color: slateblue;
}

/* Centered Popup */
#orderDetailsPopup {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* This centers the popup */
    background-color: #fff;
    width: 80%;
    max-width: 600px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    z-index: 1000; /* Ensure the popup is above the overlay */
    text-align: center;
}
p {
    color: black;
}
th, td {
    text-align: center;
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
                <th>Kokonaishinta</th>
                <th>Päivämäärä</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orderDetails)): ?>
                <?php foreach ($orderDetails as $order): ?>
                    <tr>
                        <td>
                        <a href="index.php?page=profiili&order_id=<?php echo $order['order_id']; ?>">
                            <?php echo htmlspecialchars($order['order_id']); ?>
                        </a>

                        </td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
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

<?php if (isset($orderData)): ?>
    <!-- Popup for Order Details -->
    <div id="overlay" style="display: block;"></div>
    <div id="orderDetailsPopup" style="display: block;">
        <div class="popup-content">
            <h3>Tilaustiedot</h3>
            <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kokonaishinta</th>
                    <th>Päivämäärä</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($orderData['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($orderData['total_price']); ?> €</td>
                    <td><?php echo htmlspecialchars($orderData['order_date']); ?></td>
                </tr>
            </tbody>
        </table>

        <h4>Tilatut tuotteet</h4>
        <!-- Products Table -->
        <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black;">
            <thead>
                <tr>
                    <th>Tuote</th>
                    <th>Kpl</th>
                    <th>Hinta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['nimi']); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
            <button class="oc-btn" onclick="closePopup()">Sulje</button>
        </div>
    </div>


    <script>
    function closePopup() {
        document.getElementById('orderDetailsPopup').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';

        var url = new URL(window.location.href);
        url.searchParams.delete('order_id');  // Remove the "order_id" query parameter
        window.history.replaceState({}, document.title, url.toString());  // Update the URL without reloading the page
    }
</script>
<?php endif; ?>
</body>

