<?php
require_once('config.php');

// Tarkistetaan, että tuotteen ID on annettu URL-osoitteessa
if (!isset($_GET['id'])) {
    die('Tuotteen ID:tä ei ole annettu.');
}

try {
    // Yhdistetään tietokantaan PDO:lla
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8", DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Haetaan tuotteen tiedot tietokannasta tuotteen ID:n perusteella
    $stmt = $pdo->prepare("SELECT id, nimi, kuvaus, hinta, varastomäärä, kuva FROM tuotteet WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Lisätään 'kuvat/' polun alkuun, jos se ei ole jo mukana kuvassa
        if (strpos($product['kuva'], 'kuvat/') === false) {
            $product['kuva'] = 'kuvat/' . $product['kuva']; 
        }

        // Palautetaan tuotteen tiedot JSON-muodossa
        echo json_encode($product);
    } else {
        // Jos tuotetta ei löydy, palautetaan virheilmoitus JSON-muodossa
        echo json_encode(['error' => 'Tuotetta ei löytynyt']);
    }

} catch (PDOException $e) {
    // Jos tietokantavirhe tapahtuu, palautetaan virheviesti JSON-muodossa
    echo json_encode(['error' => 'Tietokantavirhe: ' . $e->getMessage()]);
}
?>
