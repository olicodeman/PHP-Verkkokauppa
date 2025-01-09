<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $index = $data['index'];
    $quantity = $data['quantity'];

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;

        // Päivitetään kokonaishinta
        $updatedPrice = $_SESSION['cart'][$index]['price'] * $quantity;
        $totalPrice = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $_SESSION['cart']));

        echo json_encode([
            'success' => true,
            'updatedPrice' => $updatedPrice,
            'cartTotal' => $totalPrice,
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
