<?php
try {
    // Esimerkin ostoskorin käsittely
    session_start();

    header('Content-Type: application/json');

    // Lue JSON-data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['name'], $data['price'], $data['stock'])) {
        echo json_encode(['success' => false, 'message' => 'Virheelliset tiedot.']);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = [
        'name' => $data['name'],
        'price' => $data['price'],
        'quantity' => 1,
        'stock' => $data['stock'],
    ];

    echo json_encode(['success' => true, 'message' => 'Tuote lisätty ostoskoriin.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}

?>
