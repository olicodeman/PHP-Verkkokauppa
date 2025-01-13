<?php

require_once('auth.php');
require_once('config.php');

$cart = $_SESSION['cart'] ?? [];
$totalPrice = $_SESSION['cart_total'] ?? 0;
$memberId = $_SESSION['SESS_MEMBER_ID'] ?? null;


// Checks if order token exists after confirming order in payment form page
if (!isset($_GET['token']) || !isset($_SESSION['order_token']) || $_GET['token'] !== $_SESSION['order_token']) {
    header('Location: index.php?page=error');
    exit();
}

unset($_SESSION['order_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $paymentMethod = htmlspecialchars($_POST['choice']);
    $deliveryMethod = htmlspecialchars($_POST['choice2']);
}

// Yhteys tietokantaan
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if(!$link) {
    die('Failed to connect to database: ' . mysqli_connect_error());
}

//Select database
$db = mysqli_select_db($link, DB_DATABASE);
if(!$db) {
    die("Unable to select database");
} 

// Aloita transaktio
mysqli_begin_transaction($link);

try {
    // Lisää tilaus tietokantaan
    $query = "INSERT INTO tilaukset (member_id, total_price, order_date, Maksutapa, Toimitustapa) VALUES (?, ?, NOW(), ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'idss', $memberId, $totalPrice, $paymentMethod, $deliveryMethod);
    mysqli_stmt_execute($stmt);

    // Hae juuri lisätyn tilauksen ID
    $orderId = mysqli_insert_id($link);

    // Lisää tuotteet tilaukseen
    $query = "INSERT INTO tilaus_tuotteet (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    
    foreach ($cart as $item) {
       $quantity = $item['quantity'];
       $price = $item['price'];
       $productId = $item['id'];

        mysqli_stmt_bind_param($stmt, 'iiid', $orderId, $productId, $quantity, $price);
        mysqli_stmt_execute($stmt);

        $updateStockQuery = "UPDATE tuotteet SET varastomäärä = varastomäärä - ? WHERE id = ?";
        $updateStmt = mysqli_prepare($link, $updateStockQuery);
        mysqli_stmt_bind_param($updateStmt, 'ii', $quantity, $productId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }

    // Vahvista transaktio
    mysqli_commit($link);

    // Tyhjennä ostoskori
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);

    echo "<h1>Tilauksesi on vahvistettu!</h1>";
    echo "<h3>Kiitos tilauksestasi.</h3>";
    echo "<a href='index.php'>Takaisin etusviulle</a>";

} catch (Exception $e) {
    // Peru transaktio virheen sattuessa
    mysqli_rollback($link);
    echo "Tilausta ei voitu tallentaa: " . $e->getMessage();
}

// Sulje yhteys
mysqli_close($link);
?>


<style>
    body {
    background-image: url('https://live.staticflickr.com/8230/8410882716_2604e5af6b_b.jpg');
    text-align: center;
    color: white;
    margin-top: 100px;
    }

</style>
