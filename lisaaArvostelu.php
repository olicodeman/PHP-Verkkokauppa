<?php
require_once('config.php');

try {
    // Database connection
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Fetch products
    $stmt = $pdo->prepare("SELECT id, nimi FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
    $products = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tuote_id = intval($_POST['tuote_id']); // Ensure that tuote_id is an integer
    $nimi = htmlspecialchars(trim($_POST['nimi']));
    $sähköposti = htmlspecialchars(trim($_POST['sähköposti']));
    $otsikko = htmlspecialchars(trim($_POST['otsikko']));
    $kommentti = htmlspecialchars(trim($_POST['kommentti']));
    $tähtiarvostelu = intval($_POST['tähtiarvostelu']);

    // Validate inputs
    if (empty($nimi) || empty($sähköposti) || empty($otsikko) || empty($kommentti) || empty($tähtiarvostelu) || empty($tuote_id)) {
        die("Täytä kaikki kentät.");
    }

    // Check if the product exists in the database
    $checkProduct = $pdo->prepare("SELECT COUNT(*) FROM tuotteet WHERE id = ?");
    $checkProduct->execute([$tuote_id]);
    $productExists = $checkProduct->fetchColumn();

    if ($productExists == 0) {
        die("Virhe: Tuote ei ole olemassa.");
    }

    // Insert review into the database
    $sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$tuote_id, $nimi, $sähköposti, $otsikko, $kommentti, $tähtiarvostelu])) {
        echo "Arvostelu tallennettu onnistuneesti!";
    } else {
        echo "Virhe tallentaessa arvostelua.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Review</title>
    <style>
        .star-rating {
            display: flex;
            direction: row-reverse;
            justify-content: center;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
        }

        .star-rating input:checked~label {
            color: #f5c518;
        }

        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #f5c518;
        }
    </style>
</head>

<body>
    <form action="submit_review.php" method="post">
        <label for="product">Valitse tuote valikosta</label>
        <select name="tuote_id" id="product" required>
            <option value="">-- Valitse tuote --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= htmlspecialchars($product['id']) ?>">
                    <?= htmlspecialchars($product['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>


        <label for="nimi">Nimi:</label>
        <input type="text" id="nimi" name="nimi" required>

        <label for="sähköposti">Sähköposti:</label>
        <input type="email" id="sähköposti" name="sähköposti" required>

        <label for="otsikko">Otsikko:</label>
        <input type="text" id="otsikko" name="otsikko" required>

        <label for="kommentti">Kommentti:</label>
        <textarea id="kommentti" name="kommentti" required></textarea>

        <div class="star-rating">
            <input type="radio" id="star5" name="tähtiarvostelu" value="5" required>
            <label for="star5">★</label>
            <input type="radio" id="star4" name="tähtiarvostelu" value="4">
            <label for="star4">★</label>
            <input type="radio" id="star3" name="tähtiarvostelu" value="3">
            <label for="star3">★</label>
            <input type="radio" id="star2" name="tähtiarvostelu" value="2">
            <label for="star2">★</label>
            <input type="radio" id="star1" name="tähtiarvostelu" value="1">
            <label for="star1">★</label>
        </div>

        <button type="submit">Lähetä arvostelu</button>
    </form>
</body>

</html>