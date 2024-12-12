<?php
session_start();
header('Content-Type: application/json');

try {
    // Ensure a cart session is initialized
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Get the posted JSON data (for product details from the frontend)
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required data
    if (!isset($data['name'], $data['price'], $data['stock'], $data['image'])) {
        echo json_encode(['success' => false, 'message' => 'Virheelliset tiedot.']);
        exit;
    }

    // Sanitize and assign product details
    $name = htmlspecialchars($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $imageURL = htmlspecialchars($data['image']);  // This is the image URL sent from the frontend

    // Add product to the cart (image URL is already included)
    $_SESSION['cart'][] = [
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'image' => $imageURL, // Add image URL to cart data
    ];

    // Respond with success
    echo json_encode(['success' => true, 'message' => 'Tuote lisätty ostoskoriin.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}
?>
