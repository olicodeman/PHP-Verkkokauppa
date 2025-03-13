<?php
require_once('config.php');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8"; // Ensure UTF-8 charset
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Kieli valinta haetaan sessiosta tai laitetaan oletukseksi suomeksi
$language = isset($_SESSION['lang']) && $_SESSION['lang'] === 'en' ? 'en' : 'fi';

// Valitaan käännökset valitun kielen mukaan
$fieldName = $language === 'en' ? 'nimi_en' : 'nimi';

// Haetaan tiedot valitun kielen mukaan
try {
    $stmt = $pdo->prepare("SELECT id, $fieldName AS nimi FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    $products = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tuote_id = intval($_POST['tuote_id']);
    $nimi = htmlspecialchars(trim($_POST['nimi']), ENT_QUOTES, 'UTF-8');
    $sähköposti = htmlspecialchars(trim($_POST['sähköposti']), ENT_QUOTES, 'UTF-8');
    $otsikko = htmlspecialchars(trim($_POST['otsikko']), ENT_QUOTES, 'UTF-8');
    $kommentti = htmlspecialchars(trim($_POST['kommentti']), ENT_QUOTES, 'UTF-8');
    $tahtiarvostelu = intval($_POST['tähtiarvostelu']);
    $kieli = htmlspecialchars(trim($_POST['kieli']), ENT_QUOTES, 'UTF-8'); // Get selected language

    // Tarkistetaan syötetyt asiat
    if (empty($nimi) || empty($sähköposti) || empty($otsikko) || empty($kommentti) || empty($tahtiarvostelu) || empty($tuote_id) || empty($kieli)) {
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
    $sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu, kieli) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$tuote_id, $nimi, $sähköposti, $otsikko, $kommentti, $tahtiarvostelu, $kieli])) {
        echo "Arvostelu tallennettu onnistuneesti!";
    } else {
        echo "Virhe tallennettaessa arvostelua.";
    }
}
?>





    <title>Leave a Review</title>




    <form action="submit_review.php" method="post">
        <label for="product"><?= htmlspecialchars($current_lang['ChooseProductS'], ENT_QUOTES, 'UTF-8'); ?></label>
        <select name="tuote_id" id="product" onchange="fetchProductDetails(this.value)" required>
            <option value=""><?= htmlspecialchars($current_lang['ChooseProduct'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php foreach ($products as $product): ?>
                <option value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($product['nimi'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="product-details">
            <!-- Tuotteen tiedot näkyy tässä -->
        </div>

        <label for="nimi"><?= htmlspecialchars($current_lang['FirstName'], ENT_QUOTES, 'UTF-8'); ?>:</label>
        <input type="text" id="nimi" name="nimi" required>

        <label for="sähköposti"><?= htmlspecialchars($current_lang['Email'], ENT_QUOTES, 'UTF-8'); ?>:</label>
        <input type="email" id="sähköposti" name="sähköposti" required>

        <label for="otsikko"><?= htmlspecialchars($current_lang['review_title'], ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="text" id="otsikko" name="otsikko" required>

        <label for="kommentti"><?= htmlspecialchars($current_lang['Comment'], ENT_QUOTES, 'UTF-8'); ?>:</label>
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

        <!-- Kieli valinta napit-->
        <div>
            <label for="kieli"><?= htmlspecialchars($current_lang['SelectLanguage'], ENT_QUOTES, 'UTF-8'); ?>:</label>
            <input type="radio" id="fi" name="kieli" value="fi" checked> Finnish
            <input type="radio" id="en" name="kieli" value="en"> English
        </div>

        <button type="submit"><?= htmlspecialchars($current_lang['leaveReview'], ENT_QUOTES, 'UTF-8'); ?></button>
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

// Näytetään tuotteen tiedot dynaamisesti
const productDetailsHTML = `
    <div class="product-details">
        <h2>${data.nimi}</h2>
        ${imageHTML}
        <p>${data.kuvaus}</p>
        <p><?= $current_lang['price']; ?>: €${parseFloat(data.hinta).toFixed(2)}</p>
    </div>
`;
document.getElementById('product-details').innerHTML = productDetailsHTML;
})
.catch(error => console.error('Error fetching product details:', error));
}

    </script>
    
