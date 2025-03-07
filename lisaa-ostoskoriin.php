<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

try {
    // Tarkistetaan onko kÃ¤yttÃ¤jÃ¤ kirjautuneena
    if (!isset($_SESSION['SESS_MEMBER_ID'])) {
        echo json_encode(['success' => false, 'message' => 'Ennen ostoskoriin lisÃ¤Ã¤mistÃ¤, kirjaudu sisÃ¤Ã¤n.']);
        exit;
    }

    // Varmistetaan ettÃ¤ ostoskori on toiminnassa
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Haetaan ja decodataan JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['id'], $data['name'], $data['price'], $data['stock'], $data['image'])) {
        echo json_encode(['success' => false, 'message' => 'Virheelliset tiedot.']);
        exit;
    }

    // Valitoidaan ja puhditetaan tiedot
    $productID = intval($data['id']);
    $name = htmlspecialchars($data['name']);
    $price = floatval($data['price']);
    $stock = intval($data['stock']);
    $imageURL = htmlspecialchars($data['image']);
    $quantity = isset($data['quantity']) && is_numeric($data['quantity']) && $data['quantity'] > 0
        ? min(intval($data['quantity']), $stock) // KÃ¤ytetÃ¤Ã¤n pienempÃ¤Ã¤ mÃ¤Ã¤rÃ¤Ã¤ quantityn ja stockin vÃ¤lillÃ¤ 
        : 1;


    // Tarkistetaan hinta ja varasto
    if ($price <= 0 || $stock < 0) {
        echo json_encode(['success' => false, 'message' => 'Tuotteen hinnan tai varaston on oltava positiivinen.']);
        exit;
    }

    // tarkistetaan onko tuote jo olemassa ostoskorissa
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $productID) {
            // Jos tuote on jo ostoskorissa, pÃ¤ivitÃ¤ mÃ¤Ã¤rÃ¤
            $item['quantity'] = min($item['quantity'] + $quantity, $stock); // kÃ¤yetÃ¤Ã¤n tuotteen tÃ¤mÃ¤nhetkistÃ¤ mÃ¤Ã¤rÃ¤Ã¤
            $found = true;
            break;
        }
    }

    // jos tuote ei ole jo ostoskorissa, lisÃ¤tÃ¤Ã¤n se
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

    // lasketaan yhteensÃ¤ hinta uuelleen
    $_SESSION['cart_total'] = array_reduce($_SESSION['cart'], function ($carry, $item) {
        return $carry + ($item['price'] * $item['quantity']);
    }, 0);

    // onnistunut ilmoitus
    echo json_encode(['success' => true, 'message' => 'Tuote lisÃ¤tty ostoskoriin.']);
} catch (Exception $e) {
    // Virheilmoitus
    echo json_encode(['success' => false, 'message' => 'Palvelinvirhe: ' . $e->getMessage()]);
}
