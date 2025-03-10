<?php
require_once('auth.php');
require_once('config.php');

// Establish database connection using mysqli
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Fetch user details
$qry = "SELECT address, phonenumber FROM members WHERE member_id = ?";
$stmt = mysqli_prepare($link, $qry);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_MEMBER_ID']);  // Bind the session member ID as an integer

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$address = 'Ei tiedossa';
$phoneNumber = 'Ei tiedossa';
if ($result) {
    $userDetails = mysqli_fetch_assoc($result);
    $address = $userDetails['address'] ?? 'Ei tiedossa';
    $phoneNumber = $userDetails['phonenumber'] ?? 'Ei tiedossa';
}

mysqli_stmt_close($stmt);

// Fetch orders for the user
$qry = "SELECT order_id, order_date, total_price FROM tilaukset WHERE member_id = ?";
$stmt = mysqli_prepare($link, $qry);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['SESS_MEMBER_ID']);  // Bind the session member ID as an integer

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orderDetails = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orderDetails[] = $row;
    }
}

mysqli_stmt_close($stmt);

// Fetch products for a specific order (if order_id is set)
$products = [];
if (isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);  // Ensure it's a valid integer

    if ($orderId > 0) {
        $deliveryColumn = (isset($_SESSION['lang']) && $_SESSION['lang'] === 'en') ? "t.Toimitustapa_en" : "t.Toimitustapa";
        $paymentColumn = (isset($_SESSION['lang']) && $_SESSION['lang'] === 'en') ? "t.Maksutapa_en" : "t.Maksutapa";

        $orderQry = "SELECT order_id, order_date, total_price, $paymentColumn AS Maksutapa, $deliveryColumn AS Toimitustapa 
                    FROM tilaukset t 
                    WHERE order_id = ?";

        $stmt = mysqli_prepare($link, $orderQry);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $orderResult = mysqli_stmt_get_result($stmt);
        $orderData = mysqli_fetch_assoc($orderResult);


        // Fetch the products for the selected order
        $columnName = (isset($_SESSION['lang']) && $_SESSION['lang'] === 'en') ? 'nimi_en' : 'nimi';

        $productQry = "SELECT op.quantity, op.price, p.$columnName AS nimi 
                    FROM tilaus_tuotteet op
                    JOIN tuotteet p ON op.product_id = p.id
                    WHERE op.order_id = ?";

        $stmt = mysqli_prepare($link, $productQry);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $productResult = mysqli_stmt_get_result($stmt);

        while ($product = mysqli_fetch_assoc($productResult)) {
            $products[] = $product;  // Store the result in the products array
        }


        mysqli_stmt_close($stmt);
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
        color: white;
        border: 1px solid black;
    }
    .profile-content-box2 {
        width: 48%;
        height: fit-content;
        color: white;
        border: 1px solid black;
        background-color: white;
        border-radius: 10px;
        padding: 15px; 
        max-width: 500px; 
        margin: 20px auto; 
        box-sizing: border-box;
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
        position: fixed;
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
    @media (max-width: 480px) {
        .orders-table {
        width: 25%;
        font-size: 8px;
        text-align: center;
    }
    /* Table headers */
    .orders-table th {
        padding: 2px 5px;
    }
    /* Table rows */
    .orders-table td {
        padding: 2px 5px;
    }
    #orderDetailsPopup {
        padding: 10px;
    }
    }
    @media (max-width: 768px) {
        .orders-table {
        width: 25%;
        font-size: 8px;
        text-align: center;
    }
    /* Table headers */
    .orders-table th {
        padding: 2px 5px;
    }
    /* Table rows */
    .orders-table td {
        padding: 2px 5px;
    }
    #orderDetailsPopup {
        padding: 10px;
    }
    }
    @media (max-width: 400px) {
        .orders-table {
        width: 25%;
        font-size: 8px;
        text-align: center;
    }
    /* Table headers */
    .orders-table th {
        padding: 1px 3px;
    }
    /* Table rows */
    .orders-table td {
        padding: 1px 3px;
    }
    #orderDetailsPopup {
        padding: 5px;
    }
    }
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body>
<div style="text-align: center;">
    <h1><?= $current_lang['Welcome']; ?> <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?></h1>
    <a href="index.php?page=logout"><?= $current_lang['logout']; ?></a>

    <?php if ($_SESSION['SESS_LOGIN'] == 'admin'): ?>
        <a style="margin-left: 10px;" href="admin-panel.php">Admin Panel</a>
    <?php endif; ?>

    <div class="profile-content-container">
        <div class="profile-content-box2">
            <h2 style="color: black;"><?= $current_lang['YourInfo']; ?></h2>
            <p><?= $current_lang['FullName']; ?>: <?php echo htmlspecialchars($_SESSION['SESS_FIRST_NAME']);?> <?php echo htmlspecialchars($_SESSION['SESS_LAST_NAME']);?></p>
            <p><?= $current_lang['Email']; ?>: <?php echo htmlspecialchars($_SESSION['SESS_EMAIL']);?></p>
            <p><?= $current_lang['Address']; ?>: <?php echo htmlspecialchars($address); ?></p>
            <p><?= $current_lang['PhoneNmb']; ?>: <?php echo htmlspecialchars($phoneNumber); ?></p>
            <p><?= $current_lang['User']; ?>: <?php echo htmlspecialchars($_SESSION['SESS_LOGIN']);?></p>
            <a class="edit-btn" href="index.php?page=user-edit"><?= $current_lang['ChangeInfo']; ?></a>
        </div>

        <div class="profile-content-box">
            <h2 style="color: black;"><?= $current_lang['Orders']; ?>:</h2>
            <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black;">
                <thead>
                    <tr>
                        <th><?= $current_lang['OrderID']; ?></th>
                        <th><?= $current_lang['Total']; ?></th>
                        <th><?= $current_lang['Date']; ?></th>
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
                            <td colspan="4" style="text-align: center;"><?= $current_lang['NoOrders']; ?></td>
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
            <h3><?= $current_lang['OrderDetails']; ?></h3>
            <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black;">
                <thead>
                    <tr>
                        <th><?= $current_lang['TilauksenID']; ?></th>
                        <th><?= $current_lang['TotalPrice']; ?></th>
                        <th><?= $current_lang['Date']; ?></th>
                        <th><?= $current_lang['PMethod']; ?></th>
                        <th><?= $current_lang['DMethod']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($orderData['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($orderData['total_price']); ?> €</td>
                        <td><?php echo htmlspecialchars($orderData['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($orderData['Maksutapa']); ?></td>
                        <td><?php echo htmlspecialchars($orderData['Toimitustapa']); ?></td>
                    </tr>
                </tbody>
            </table>

            <h4><?= $current_lang['OrderProducts']; ?></h4>
            <!-- Products Table -->
            <table class="orders-table" style="width: 100%; border-collapse: collapse; color: black;">
                <thead>
                    <tr>
                        <th><?= $current_lang['Product']; ?></th>
                        <th><?= $current_lang['Amount']; ?></th>
                        <th><?= $current_lang['PricePerProduct']; ?></th>
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
            <button class="oc-btn" onclick="closePopup()"><?= $current_lang['Close']; ?></button>
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
</html>
