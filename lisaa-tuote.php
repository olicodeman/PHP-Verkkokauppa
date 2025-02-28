<?php
require_once("auth.php");
require_once('config.php');

$login = $_SESSION['SESS_LOGIN'];
if ($login !== 'admin') {
    header('location: index.php?page=error');
    exit();
}

// Luodaan PDO yhteys
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8"; // Oikea DSN
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);

    // Asetetaan PDO asetukset
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    //KÃ¤sitellÃ¤Ã¤n mahdollinen tietokanta virhe
    die('Database connection failed: ' . $e->getMessage());
}

// Napataan kategoriat tietokannasta
try {
    $stmt = $pdo->prepare("SELECT id, nimi FROM kategoriat");
    $stmt->execute();
    $kategoriat = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching categories: ' . $e->getMessage() . '</p>';
    $kategoriat = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nimi = $_POST['nimi'];
    $nimi_en = $_POST['nimi_en'];
    $kuvaus = $_POST['kuvaus'];
    $kuvaus_en = $_POST['kuvaus_en'];
    $hinta = $_POST['hinta'];
    $kategoriat_selected = $_POST['kategoriat'];  // Haetaan valitut kategoriat
    $varastomaara = $_POST['varastomaara'];

    //kÃ¤sitellÃ¤Ã¤n kuvan lataaminen
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        // Varmistetaan kuvan tiedot ettei ole vÃ¤Ã¤rÃ¤nlaisia
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageExtension, $validExtensions)) {
            // SiirretÃ¤Ã¤n kuva uploadeihin
            $uploadDir = 'kuvat/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $imageName);
            $imagePath = $uploadDir . $imageName;

            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                // Kuvan lataaminen onnistui, lisÃ¤Ã¤ tuote tietokantaan
                try {
                    $stmt = $pdo->prepare("INSERT INTO tuotteet (nimi, nimi_en, kuvaus, kuvaus_en, hinta, kuva, varastomaara) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nimi, $nimi_en, $kuvaus, $kuvaus_en, $hinta, $imagePath, $varastomaara]);

                    $tuote_id = $pdo->lastInsertId();  // hae viimeksi laitettu ID

                    // LisÃ¤tÃ¤Ã¤n suhteet tuote-kategoiroiden vÃ¤lille jos kategoriat ovat valittu
                    if (!empty($kategoriat_selected)) {
                        foreach ($kategoriat_selected as $kategoria_id) {
                            $stmt = $pdo->prepare("INSERT INTO tuote_kategoria (tuote_id, kategoria_id) VALUES (?, ?)");
                            $stmt->execute([$tuote_id, $kategoria_id]);
                        }
                    }

                    echo '<p class="success">Product added successfully!</p>';
                } catch (PDOException $e) {
                    echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
                }
            } else {
                echo '<p class="error"> Kuvan lataamisessa virhe. YritÃ¤ uudelleen.</p>';
            }
        } else {
            echo '<p class="error"> VÃ¤Ã¤rÃ¤ kuva tyyppi, vain JPG, JPEG, PNG, ja GIF ovat sallittuja.</p>';
        }
    } else {
        echo '<p class="error"> Kuvan lataaminen epÃ¤onnistui.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LisÃ¤Ã¤ tuote</title>
    <link href="style.css" rel="stylesheet">

    <style>
        .kategoria-nappi {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .kategoria-nappi.valittu {
            background-color: #4CAF50;
            /*vihreÃ¤ valitulle kategorialle */
            color: white;
        }
    </style>
</head>

<body>
    <div style="color: white; text-align: center;">
        <h1>LisÃ¤Ã¤ tuote</h1>
        <a href="admin-panel.php">Back</a> | <a href="index.php?page=logout">Logout</a>
        <form action="" method="POST" enctype="multipart/form-data">

            <label for="nimi">Tuotteen nimi (Suomi):</label>
            <input type="text" id="nimi" name="nimi" required>

            <label for="nimi_en">Product Name (English):</label>
            <input type="text" id="nimi_en" name="nimi_en" required>

            <label for="kuvaus">Tuotteen kuvaus (Suomi):</label>
            <textarea id="kuvaus" name="kuvaus" rows="4" required></textarea>

            <label for="kuvaus_en">Product Description (English):</label>
            <textarea id="kuvaus_en" name="kuvaus_en" rows="4" required></textarea>


            <label for="hinta">Tuotteen Hinta (â‚¬):</label>
            <input type="number" id="hinta" name="hinta" step="0.01" required>

            <label for="varastomaara">Varastomaara:</label>
            <input type="number" id="varastomaara" name="varastomaara" step="1.00" required>

            <h2>Valitse kategoria</h2>
            <div id="kategoriat">
                <?php if (!empty($kategoriat)): ?>
                    <?php foreach ($kategoriat as $kategoria): ?>
                        <label>
                            <input type="checkbox" name="kategoriat[]" value="<?= $kategoria['id'] ?>">
                            <?= htmlspecialchars($kategoria['nimi']) ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Ei valittavia kategorioita</p>
                <?php endif; ?>
            </div>

            <label for="image">Tuotteen kuva:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit">LisÃ¤Ã¤ Tuote</button>
        </form>

    </div>
</body>

</html>