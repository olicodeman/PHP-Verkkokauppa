<?php

require_once('auth.php');
require_once('config.php');

$cart = $_SESSION['cart'] ?? [];
$totalPrice = $_SESSION['cart_total'] ?? 0;
$memberId = $_SESSION['SESS_MEMBER_ID'] ?? null;

// Tarkista, onko ostoskori tyhjä tai käyttäjä ei ole kirjautunut
if (empty($cart) || !$memberId) {
    die('Ostoskorisi on tyhjä tai et ole kirjautunut sisään.');
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
    $query = "INSERT INTO tilaukset (member_id, total_price, order_date) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'id', $memberId, $totalPrice);
    mysqli_stmt_execute($stmt);

    // Hae juuri lisätyn tilauksen ID
    $orderId = mysqli_insert_id($link);

    // Lisää tuotteet tilaukseen
    $query = "INSERT INTO tilaus_tuotteet (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    
    foreach ($cart as $item) {
       $quantity = $item['quantity'];
        $price = $item['price'];

        mysqli_stmt_bind_param($stmt, 'iiid', $orderId, $productId, $quantity, $price);
        mysqli_stmt_execute($stmt);
    }

    // Vahvista transaktio
    mysqli_commit($link);

    // Tyhjennä ostoskori
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);

    echo "Tilaus tallennettu onnistuneesti!";
    echo "<a href='index.php'>Takaisin etusviulle</a>";

} catch (Exception $e) {
    // Peru transaktio virheen sattuessa
    mysqli_rollback($link);
    echo "Tilausta ei voitu tallentaa: " . $e->getMessage();
}

// Sulje yhteys
mysqli_close($link);
?>
