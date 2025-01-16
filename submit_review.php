<?php
require_once('config.php');
session_start();

// Tarkistetaan kirjautuminen
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Kirjaudu sisään jättääksesi arvostelun.");
}

// Yhdistetään tietokantaan
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Tietokantayhteys epäonnistui: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan lomaketiedot
    $tuote_id = intval($_POST['tuote_id']); // Ensure tuote_id is passed
    $nimi = $conn->real_escape_string(trim($_POST['nimi']));
    $sähköposti = $conn->real_escape_string(trim($_POST['sähköposti']));
    $otsikko = $conn->real_escape_string(trim($_POST['otsikko']));
    $kommentti = $conn->real_escape_string(trim($_POST['kommentti']));
    $tähtiarvostelu = intval($_POST['tähtiarvostelu']);

    // Varmistetaan, että kaikki tiedot ovat olemassa
    if (empty($nimi) || empty($sähköposti) || empty($otsikko) || empty($kommentti) || empty($tähtiarvostelu) || empty($tuote_id)) {
        die("Täytä kaikki kentät.");
    }

    // Tarkista, että tuote_id löytyy tuotteet-taulusta
    $checkProduct = $conn->prepare("SELECT COUNT(*) FROM tuotteet WHERE id = ?");
    $checkProduct->bind_param('i', $tuote_id);
    $checkProduct->execute();
    $checkProduct->bind_result($productExists);
    $checkProduct->fetch();
    $checkProduct->close();

    if ($productExists === 0) {
        die("Virhe: Tuote ei ole olemassa.");
    }

    // Tallennetaan arvostelu tietokantaan
    $sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('issssi', $tuote_id, $nimi, $sähköposti, $otsikko, $kommentti, $tähtiarvostelu);
        if ($stmt->execute()) {
            echo "Arvostelu tallennettu onnistuneesti! Kiitos palautteesta! ";
        } else {
            echo "Virhe tallentaessa arvostelua: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Virhe tietokantakyselyssä: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="center-align">
            <a class="edit-btn" id="register-btn" href="index.php?page=arvosteluSivu">Lue arvosteluja täältä</a>
        </div>
    
        <div class="center-align">
            <a class="edit-btn" id="register-btn" href="index.php">Etusivulle</a>
        </div>
</body>
</html>
