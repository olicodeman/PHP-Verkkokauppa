<?php
require_once('config.php');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Haetaan tuotteet tietokannasta
try {
    $stmt = $pdo->prepare("SELECT id, nimi, kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
    $products = [];
}

try {
    $categoryStmt = $pdo->prepare("SELECT id, nimi FROM kategoriat");
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching categories: ' . $e->getMessage() . '</p>';
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tuote_id = intval($_POST['tuote_id']);
    $nimi = htmlspecialchars(trim($_POST['nimi']));
    $sähköposti = htmlspecialchars(trim($_POST['sähköposti']));
    $otsikko = htmlspecialchars(trim($_POST['otsikko']));
    $kommentti = htmlspecialchars(trim($_POST['kommentti']));
    $tähtiarvostelu = intval($_POST['tähtiarvostelu']);

    // Tarkistetaan syötetyt asiat
    if (empty($nimi) || empty($sähköposti) || empty($otsikko) || empty($kommentti) || empty($tähtiarvostelu) || empty($tuote_id)) {
        die("Täytä kaikki kentät.");
    }

    // Tarkistetaan onko tuote tietokannassa
    $checkProduct = $pdo->prepare("SELECT COUNT(*) FROM tuotteet WHERE id = ?");
    $checkProduct->execute([$tuote_id]);
    $productExists = $checkProduct->fetchColumn();

    if ($productExists == 0) {
        die("Virhe: Tuote ei ole olemassa.");
    }

    // Lisätään tuotearvostelu tietokantaan
    $sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$tuote_id, $nimi, $sähköposti, $otsikko, $kommentti, $tähtiarvostelu])) {
        echo "Arvostelu tallennettu onnistuneesti!";
    } else {
        echo "Virhe tallentaessa arvostelua.";
    }
}
$productNameColumn = ($_SESSION['lang'] == 'en') ? 'nimi_en' : 'nimi';
$productDescriptionColumn = ($_SESSION['lang'] == 'en') ? 'kuvaus_en' : 'kuvaus';

$stmt = $pdo->prepare("SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
$stmt->execute();
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Review</title>
    <style>
        body {
            color: white;
            background-color: rgb(30, 30, 30);
        }

        .product-details,
        .product-details h2,
        .product-details p {
            color: rgb(45, 45, 102);
        }

        /* Tähti arvostelu tyyli */
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

        .product-image {
            max-width: 200px;
            max-height: 200px;
        }

        .product-details {
            background: white;
            color: rgb(45, 45, 102);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
            padding: 20px;
            margin: 20px auto;
            text-align: center;
        }

        .product-details img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product-details h2 {
            margin: 15px 0 10px;
            font-size: 20px;
        }

        .product-details p {
            margin: 10px 0;
        }

        .product-details .keskita {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .product-image {
            height: 200px;
            width: 100%;
            object-fit: contain;
            border-radius: 5px;
        }
    </style>

</head>

<body>
    <form action="submit_review.php" method="post">
        <label for="product"><?= $current_lang['ChooseProductS']; ?></label>
        <select name="tuote_id" id="product" onchange="fetchProductDetails(this.value)" required>
            <option value=""><?= $current_lang['ChooseProduct']; ?></option>
            <?php foreach ($products as $product): ?>
                <option value="<?= htmlspecialchars($product['id']) ?>">
                    <?= htmlspecialchars($product['nimi']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="product-details">
            <!-- Tuotteen tiedot näkyy tässä -->
        </div>

        <label for="nimi"><?= $current_lang['FirstName']; ?>:</label>
        <input type="text" id="nimi" name="nimi" required>

        <label for="sähköposti"><?= $current_lang['Email']; ?>:</label>
        <input type="email" id="sähköposti" name="sähköposti" required>

        <label for="otsikko"><?= $current_lang['review_title']; ?></label>
        <input type="text" id="otsikko" name="otsikko" required>

        <label for="kommentti"><?= $current_lang['Comment']; ?>:</label>
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

        <button type="submit"><?= $current_lang['leaveReview']; ?></button>
    </form>
    <script>
        function fetchProductDetails(productId) {
            if (productId === '') {
                document.getElementById('product-details').innerHTML = '';
                return;
            }

            fetch('HaeTuoteTiedot.php?id=' + productId + '&lang=' + selectedLang)

                .then(response => response.json())
                .then(data => {
                    // Check if the image field is empty
                    const imageHTML = data.kuva
                        ? `<img class="product-image" src="${data.kuva}" alt="${data.nimi}">`
                        : '<p>No image available for this product.</p>';

                    // Construct HTML for product details
                    const productDetailsHTML = `
            <div class="product-details">
                <h2>${data.nimi}</h2>
                ${imageHTML}
                <p>${data.kuvaus}</p>
                <p>Hinta: €${parseFloat(data.hinta).toFixed(2)}</p>
            </div>
        `;
                    document.getElementById('product-details').innerHTML = productDetailsHTML;
                })
                .catch(error => console.error('Error fetching product details:', error));


    </script>
</body>

</html>