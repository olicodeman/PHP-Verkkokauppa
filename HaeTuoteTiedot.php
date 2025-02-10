<?php
require_once('config.php');

// Set content type to JSON
header('Content-Type: application/json');

// Ensure a valid product ID is given
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Virheellinen tuotteen ID']);
    exit;
}

// Ensure language selection is valid
$allowed_languages = ['fi', 'en'];
$current_lang = isset($_GET['lang']) && in_array($_GET['lang'], $allowed_languages) ? $_GET['lang'] : 'fi';

// Choose correct database fields based on language
$productNameColumn = ($current_lang == 'en') ? 'nimi_en' : 'nimi';
$productDescriptionColumn = ($current_lang == 'en') ? 'kuvaus_en' : 'kuvaus';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch product details using prepared statements
    $stmt = $pdo->prepare("SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, hinta, varastomäärä, kuva FROM tuotteet WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Check if image exists before modifying the path
        if (!empty($product['kuva']) && strpos($product['kuva'], 'kuvat/') === false) {
            $product['kuva'] = 'kuvat/' . $product['kuva']; 
        }

        // Return product details as JSON
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Tuotetta ei löytynyt']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Tietokantavirhe: ' . $e->getMessage()]);
}
?>
