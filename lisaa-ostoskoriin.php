<?php
session_start();
header('Content-Type: application/json');

// Suppress output of errors to the browser, log them instead
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

try {
    // Check if the user is logged in
    if (!isset($_SESSION['SESS_MEMBER_ID'])) {
        echo json_encode(['success' => false, 'message' => 'Ennen ostoskoriin lisäämistä, kirjaudu sisään.']);
        exit;
    }

    // Ensure the cart is initialized
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Get and decode JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['id'], $data['name'], $data['price'], $data['stock'], $data['image'])) {
        echo json_encode(['success' => false, 'message' => 'Virheelliset tiedot.']);
        exit;
    }

    // Sanitize and validate product data
    $productID = intval($data['id']);
    $name = htmlspecialchars($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $imageURL = htmlspecialchars($data['image']);
    $quantity = isset($data['quantity']) && is_numeric($data['quantity']) && $data['quantity'] > 0 ? intval($data['quantity']) : 1;

    // Validate price and stock
    if ($price <= 0 || $stock < 0) {
        echo json_encode(['success' => false, 'message' => 'Tuotteen hinnan tai varaston on oltava positiivinen.']);
        exit;
    }

    // Add the product to the cart
    $_SESSION['cart'][] = [
        'id' => $productID, 
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'image' => $imageURL,
        'quantity' => $quantity,
    ];

    $_SESSION['cart_total'] = array_reduce($_SESSION['cart'], function($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);

    // Success response
    echo json_encode(['success' => true, 'message' => 'Tuote lisätty ostoskoriin.']);
} catch (Exception $e) {
    // Catch any errors and return a JSON error message
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}
