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


// Kieli valinta haetaan sessiosta tai laitetaan oletukseksi suomeksi
$language = isset($_SESSION['lang']) && $_SESSION['lang'] === 'en' ? 'en' : 'fi';

// Valitaan kÃ¤Ã¤nnÃ¶sket valitun kielen mukaan
$fieldName = $language === 'en' ? 'nimi_en' : 'nimi';

// Haetaan tiedot valitun kielen mukaan 
try {
    $stmt = $pdo->prepare("SELECT id, $fieldName AS nimi FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
    $products = [];
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tuote_id = intval($_POST['tuote_id']);
    $nimi = htmlspecialchars(trim($_POST['nimi']));
    $sÃ¤hkÃ¶posti = htmlspecialchars(trim($_POST['sÃ¤hkÃ¶posti']));
    $otsikko = htmlspecialchars(trim($_POST['otsikko']));
    $kommentti = htmlspecialchars(trim($_POST['kommentti']));
    $tÃ¤htiarvostelu = intval($_POST['tÃ¤htiarvostelu']);
    $kieli = htmlspecialchars(trim($_POST['kieli'])); // Get selected language

    // Tarkistetaan syÃ¶tetyt asiat
    if (empty($nimi) || empty($sÃ¤hkÃ¶posti) || empty($otsikko) || empty($kommentti) || empty($tÃ¤htiarvostelu) || empty($tuote_id) || empty($kieli)) {
        die("TÃ¤ytÃ¤ kaikki kentÃ¤t.");
    }

    // Tarkistetaan onko tuote tietokannassa
    $checkProduct = $pdo->prepare("SELECT COUNT(*) FROM tuotteet WHERE id = ?");
    $checkProduct->execute([$tuote_id]);
    $productExists = $checkProduct->fetchColumn();

    if ($productExists == 0) {
        die("Virhe: Tuote ei ole olemassa.");
    }

    // LisÃ¤tÃ¤Ã¤n tuotearvostelu tietokantaan
// Insert review into the database
$sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu, kieli) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([$tuote_id, $nimi, $sahkoposti, $otsikko, $kommentti, $tahtiarvostelu, $kieli])) {
    echo "Arvostelu tallennettu onnistuneesti!";
} else {
    echo "Virhe tallennettaessa arvostelua.";
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
        body {
            color: white;
            background-color: rgb(30, 30, 30);
        }

        .product-details,
        .product-details h2,
        .product-details p {
            color: rgb(45, 45, 102);
        }

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
            <!-- Tuotteen tiedot nÃ¤kyy tÃ¤ssÃ¤ -->
        </div>

        <label for="nimi"><?= $current_lang['FirstName']; ?>:</label>
        <input type="text" id="nimi" name="nimi" required>

        <label for="sÃ¤hkÃ¶posti"><?= $current_lang['Email']; ?>:</label>
        <input type="email" id="sÃ¤hkÃ¶posti" name="sÃ¤hkÃ¶posti" required>

        <label for="otsikko"><?= $current_lang['review_title']; ?></label>
        <input type="text" id="otsikko" name="otsikko" required>

        <label for="kommentti"><?= $current_lang['Comment']; ?>:</label>
        <textarea id="kommentti" name="kommentti" required></textarea>

        <div class="star-rating">
            <input type="radio" id="star5" name="tÃ¤htiarvostelu" value="5" required>
            <label for="star5">â˜…</label>
            <input type="radio" id="star4" name="tÃ¤htiarvostelu" value="4">
            <label for="star4">â˜…</label>
            <input type="radio" id="star3" name="tÃ¤htiarvostelu" value="3">
            <label for="star3">â˜…</label>
            <input type="radio" id="star2" name="tÃ¤htiarvostelu" value="2">
            <label for="star2">â˜…</label>
            <input type="radio" id="star1" name="tÃ¤htiarvostelu" value="1">
            <label for="star1">â˜…</label>
        </div>

        <!-- Kieli valinta napit-->
        <div>
            <label for="kieli"><?= $current_lang['SelectLanguage']; ?>:</label>
            <input type="radio" id="fi" name="kieli" value="fi" checked> Finnish
            <input type="radio" id="en" name="kieli" value="en"> English
        </div>

        <button type="submit"><?= $current_lang['leaveReview']; ?></button>
    </form>
    <script>
       function fetchProductDetails(productId) {
    if (!productId) {
        document.getElementById('product-details').innerHTML = '';
        return;
    }


    const currentLang = "<?= $_SESSION['lang'] ?? 'fi' ?>";  // Oletuskieli on suomi

    // Haetaan tuotteen tiedot valitun kielen mukaan
    fetch(`HaeTuoteTiedot.php?id=${productId}&lang=${currentLang}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('product-details').innerHTML = `<p>${data.error}</p>`;
                return;
            }

            const imageHTML = data.kuva
                ? `<img class="product-image" src="${data.kuva}" alt="${data.nimi}">`
                : '<p>No image available for this product.</p>';

            // NÃ¤ytetÃ¤Ã¤n tuotteen tiedot dynaamisesti
            const productDetailsHTML = `
                <div class="product-details">
                    <h2>${data.nimi}</h2>
                    ${imageHTML}
                    <p>${data.kuvaus}</p>
                    <p><?= $current_lang['price']; ?>: â‚¬${parseFloat(data.hinta).toFixed(2)}</p>
                </div>
            `;
            document.getElementById('product-details').innerHTML = productDetailsHTML;
        })
        .catch(error => console.error('Error fetching product details:', error));
}

    </script>
    
</body>

</html>
