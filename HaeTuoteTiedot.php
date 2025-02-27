<?php
require_once('config.php');

//  Aseetetaan yhteys tyyppi JSON
header('Content-Type: application/json');

// Varmistetaan että oikeas tuote id on annettu 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Virheellinen tuotteen ID']);
    exit;
}

// Varmistetaan että kielen valuinta on oikein
$allowed_languages = ['fi', 'en'];
$current_lang = isset($_GET['lang']) && in_array($_GET['lang'], $allowed_languages) ? $_GET['lang'] : 'fi';

// Haetaan käännökset valitun kielen mukaan
$productNameColumn = ($current_lang == 'en') ? 'nimi_en' : 'nimi';
$productDescriptionColumn = ($current_lang == 'en') ? 'kuvaus_en' : 'kuvaus';

try {
    // Yhdistetään tietokantaab
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch product details using prepared statements
    $stmt = $pdo->prepare("SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, hinta, varastomäärä, kuva FROM tuotteet WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Tarkistetaan onko kuva olemassa ennenkuin muokataan sen path 
        if (!empty($product['kuva']) && strpos($product['kuva'], 'kuvat/') === false) {
            $product['kuva'] = 'kuvat/' . $product['kuva'];
        }

        // Palautetaan tuotteen tiedot JSON iin
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Tuotetta ei löytynyt']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Tietokantavirhe: ' . $e->getMessage()]);
}
?>