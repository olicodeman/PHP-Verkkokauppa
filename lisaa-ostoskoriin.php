<?php
session_start();
header('Content-Type: application/json');

// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Käyttäjä ei ole kirjautunut sisään
    echo json_encode(['success' => false, 'message' => 'Ennen ostoskoriin lisäämistä, kirjaudu sisään.']);
    exit;  // Lopetetaan käsittely, koska käyttäjä ei ole kirjautunut
}

try {
    // Varmistetaan, että ostoskori on alustettu
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Haetaan lähetetty JSON-data (tuotteen tiedot frontendistä)
    $data = json_decode(file_get_contents('php://input'), true);

    // Tarkistetaan, että tarvittavat tiedot ovat mukana
    if (!isset($data['name'], $data['price'], $data['stock'], $data['image'])) {
        echo json_encode(['success' => false, 'message' => 'Virheelliset tiedot.']);
        exit;
    }

    // Sanitoidaan ja otetaan tuotteen tiedot käyttöön
    $name = htmlspecialchars($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $imageURL = htmlspecialchars($data['image']);  // Tuotteen kuvan URL
    $quantity = isset($data['quantity']) && is_numeric($data['quantity']) && $data['quantity'] > 0 ? intval($data['quantity']) : 1;  // Määrä (oletus 1, jos ei annettu)

    // Lisätään tuote ostoskoriin
    $_SESSION['cart'][] = [
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'image' => $imageURL,
        'quantity' => $quantity,
    ];

    // Palautetaan onnistumisviesti
    echo json_encode(['success' => true, 'message' => 'Tuote lisätty ostoskoriin.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}
?>
