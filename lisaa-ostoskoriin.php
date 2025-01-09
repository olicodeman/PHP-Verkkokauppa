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

    // Check if the product already exists in the cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $productID) {
            // If the product is already in the cart, update the quantity
            $item['quantity'] = min($item['quantity'] + $quantity, $item['stock']);
            $found = true;
            break;
        }
    }

    // If the product is not in the cart, add it
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $productID, 
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'image' => $imageURL,
            'quantity' => $quantity,
        ];
    }

    // Recalculate the total price
    $_SESSION['cart_total'] = array_reduce($_SESSION['cart'], function($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);

    // Success response
    echo json_encode(['success' => true, 'message' => 'Tuote lisätty ostoskoriin.']);
} catch (Exception $e) {
    // Catch any errors and return a JSON error message
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}
