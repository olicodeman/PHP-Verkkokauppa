<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('auth.php');
require_once('config.php');
require_once('lang.php');

$lang = $_SESSION['lang'] ?? 'fi'; // Suomi oletuskielenä

$cart = $_SESSION['cart'] ?? [];
$totalPrice = $_SESSION['cart_total'] ?? 0;
$memberId = $_SESSION['SESS_MEMBER_ID'] ?? null;

if (!isset($_GET['token']) || !isset($_SESSION['order_token']) || $_GET['token'] !== $_SESSION['order_token']) {
    header('Location: index.php?page=error');
    exit();
}

unset($_SESSION['order_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw values for payment and delivery methods from the radio buttons
    if (isset($_POST['choice']) && isset($_POST['choice2'])) {
        $paymentMethod = htmlspecialchars($_POST['choice']); // 'Bill', 'card', etc.
        $deliveryMethod = htmlspecialchars($_POST['choice2']); // 'pickup', 'posted', etc.
        
        // Define English equivalents for payment methods
        $paymentMethodEn = '';
        switch ($paymentMethod) {
            case 'Kortti':
                $paymentMethodEn = 'Card';
                break;
            case 'Lasku':
                $paymentMethodEn = 'Bill';
                break;
            // Handle more cases or set a default
            default:
                $paymentMethodEn = 'Unknown';  // Ensure a valid fallback value
                break;
        }

        // Define English equivalents for delivery methods
        $deliveryMethodEn = '';
        switch ($deliveryMethod) {
            case 'Nouto':
                $deliveryMethodEn = 'Pickup';
                break;
            case 'Postitus':
                $deliveryMethodEn = 'Posted';
                break;
            // Handle more cases or set a default
            default:
                $deliveryMethodEn = 'Unknown';  // Ensure a valid fallback value
                break;
        }
    } else {
        // Handle missing payment or delivery method
        echo "Payment or delivery method is missing.";
        exit(); // Or redirect to an error page
    }
}

// Yhteys tietokantaan
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to database: ' . mysqli_connect_error());
}

// Ensure UTF-8 encoding for database connection
mysqli_set_charset($link, 'utf8');

// Select database
$db = mysqli_select_db($link, DB_DATABASE);
if (!$db) {
    die("Unable to select database");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable detailed error reporting

// Aloita transaktio
mysqli_begin_transaction($link);

try {
    // Insert into tilaukset table
    $query = "INSERT INTO tilaukset (member_id, total_price, order_date, Maksutapa, Toimitustapa, Maksutapa_en, Toimitustapa_en) VALUES (?, ?, NOW(), ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'idssss', $memberId, $totalPrice, $paymentMethod, $deliveryMethod, $paymentMethodEn, $deliveryMethodEn);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error inserting into tilaukset: ' . mysqli_stmt_error($stmt));
    }

    // Hae juuri lisätyn tilauksen ID
    $orderId = mysqli_insert_id($link);
    echo "Order ID: " . $orderId;

    // Add products to tilaus_tuotteet
    $query = "INSERT INTO tilaus_tuotteet (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    foreach ($cart as $item) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        $productId = $item['id'];

        mysqli_stmt_bind_param($stmt, 'iiid', $orderId, $productId, $quantity, $price);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Error inserting into tilaus_tuotteet: ' . mysqli_stmt_error($stmt));
        }

        // Update stock
        $updateStockQuery = "UPDATE tuotteet SET varastomäärä = varastomäärä - ? WHERE id = ?";
        $updateStmt = mysqli_prepare($link, $updateStockQuery);
        mysqli_stmt_bind_param($updateStmt, 'ii', $quantity, $productId);
        if (!mysqli_stmt_execute($updateStmt)) {
            throw new Exception('Error updating stock: ' . mysqli_stmt_error($updateStmt));
        }
        mysqli_stmt_close($updateStmt);
    }

    // Commit transaction
    mysqli_commit($link);

    // Clear cart
    unset($_SESSION['cart']);
    unset($_SESSION['cart_total']);

    echo "<h1>" . $current_lang['OrderConfirmed'] . "</h1>";
    echo "<h3>" . $current_lang['ThanksForOrdering'] . "</h3>";
    echo "<a href='index.php'>" . $current_lang['BackToHomepage'] . "</a>";

} catch (Exception $e) {
    // Rollback transaction in case of error
    mysqli_rollback($link);
    echo "Tilausta ei voitu tallentaa: " . $e->getMessage();
}

// Close connection
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
