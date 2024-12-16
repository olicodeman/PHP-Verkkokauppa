<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// Tietokantayhteys
try {
    $pdo = new PDO('mysql:host=localhost;dbname=verkkokauppa', 'käyttäjänimi', 'salasana');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Tietokantavirhe: ' . $e->getMessage());
}

// Tarkistetaan lomakedata
$firstname = isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : null;
$lastname = isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : null;
$phonenumber = isset($_POST['phonenumber']) ? htmlspecialchars($_POST['phonenumber']) : null;

// Varmistetaan, että kaikki pakolliset kentät on täytetty
if (!$firstname || !$lastname || !$phonenumber) {
    die('Virhe: Kaikki kentät ovat pakollisia.');
}

try {
    // Aloitetaan tietokantatransaktio
    $pdo->beginTransaction();

    // Lisätään tilaustiedot tietokantaan
    $stmt = $pdo->prepare("INSERT INTO orders (firstname, lastname, phonenumber) VALUES (:firstname, :lastname, :phonenumber)");
    $stmt->execute([
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':phonenumber' => $phonenumber
    ]);

    // Hae tilauksen ID
    $orderId = $pdo->lastInsertId();

    // Lisätään ostoskori tuotteet tietokantaan
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (:order_id, :product_name, :price, :quantity)");

        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_name' => $item['name'],
                ':price' => $item['price'],
                ':quantity' => $item['quantity']
            ]);
        }
    }

    // Päätetään transaktio
    $pdo->commit();

    // Tyhjennetään ostoskori
    unset($_SESSION['cart']);

    echo 'Tilaus käsiteltiin onnistuneesti!';
} catch (Exception $e) {
    // Palautetaan mahdolliset muutokset, jos jotain meni pieleen
    $pdo->rollBack();
    die('Virhe tilausta käsiteltäessä: ' . $e->getMessage());
}
?>
