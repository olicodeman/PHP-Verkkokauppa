<?php
require_once('config.php');
require_once('lang.php');

// Tietokantayhteys
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Tietokantayhteyden muodostaminen epäonnistui: ' . $e->getMessage());
}

// Haetaan tuotteet pudotusvalikon täyttämiseksi
try {
    $stmt = $pdo->prepare("SELECT id, nimi FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
    $lang_filter = isset($_GET['lang_filter']) ? $_GET['lang_filter'] : null;

} catch (PDOException $e) {
    echo '<p class="error">Virhe tuotteiden hakemisessa: ' . $e->getMessage() . '</p>';
    $products = [];
}

// Haetaan arvostelut valitun tuotteen perusteella tai kaikki arvostelut
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

try {
    if ($product_id) {
        // Haetaan arvostelut tietylle tuotteelle
        $stmt = $pdo->prepare("SELECT * FROM arvostelut WHERE tuote_id = :product_id ORDER BY luotu DESC");
        $stmt->bindParam(':product_id', $product_id);
    } else {
        // Haetaan kaikki arvostelut
        $stmt = $pdo->prepare("SELECT * FROM arvostelut ORDER BY luotu DESC");
    }
    $stmt->execute();
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Virhe arvostelujen hakemisessa: ' . $e->getMessage() . '</p>';
    $reviews = [];
}

// Haetaan tuotetiedot, jos tuote on valittu
$product_details = [];
if ($product_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tuotteet WHERE id = :product_id LIMIT 1");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product_details = $stmt->fetch();
    } catch (PDOException $e) {
        echo '<p class="error">Virhe tuotetietojen hakemisessa: ' . $e->getMessage() . '</p>';
    }
}

// Lisätty uusi osio tähdenarvostelun suodatukselle
$rating_filter = isset($_GET['rating_filter']) ? $_GET['rating_filter'] : null;

try {
    $query = "SELECT * FROM arvostelut WHERE 1=1"; // Base query

    // Suodatetaan tuotteen perusteella
    if ($product_id) {
        $query .= " AND tuote_id = :product_id";
    }

    // Suodatetaan arvostelun mukaan
    if ($rating_filter) {
        $query .= " AND tähtiarvostelu = :rating_filter";
    }

    // Suodatetaan kielen mukaan
    if ($lang_filter) {
        $query .= " AND kieli = :lang_filter";
    }

    $query .= " ORDER BY luotu DESC";

    $stmt = $pdo->prepare($query);


    if ($product_id) {
        $stmt->bindParam(':product_id', $product_id);
    }
    if ($rating_filter) {
        $stmt->bindParam(':rating_filter', $rating_filter);
    }
    if ($lang_filter) {
        $stmt->bindParam(':lang_filter', $lang_filter);
    }

    $stmt->execute();
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Virhe arvostelujen hakemisessa: ' . $e->getMessage() . '</p>';
    $reviews = [];
}

// Valitaan columnit kielen mukaan
$productNameColumn = ($_SESSION['lang'] == 'en') ? 'nimi_en' : 'nimi';
$productDescriptionColumn = ($_SESSION['lang'] == 'en') ? 'kuvaus_en' : 'kuvaus';

// Haetaan tuotteen tiedot valitun kielen mukaan
$stmt = $pdo->prepare("SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
$stmt->execute();
$products = $stmt->fetchAll();


?>




    <title>All Reviews</title>



    <div class="review-container">
        <h1><?= $current_lang['AllReviews']; ?></h1>

        <!-- Tähdinarvostelujen mukaan suodatus-->
        <div class="star-filter">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="arvosteluSivu">
                <label><?= $current_lang['FilterStar']; ?></label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating_filter" value="5" <?= isset($_GET['rating_filter']) && $_GET['rating_filter'] == '5' ? 'checked' : '' ?>>
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating_filter" value="4" <?= isset($_GET['rating_filter']) && $_GET['rating_filter'] == '4' ? 'checked' : '' ?>>
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating_filter" value="3" <?= isset($_GET['rating_filter']) && $_GET['rating_filter'] == '3' ? 'checked' : '' ?>>
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating_filter" value="2" <?= isset($_GET['rating_filter']) && $_GET['rating_filter'] == '2' ? 'checked' : '' ?>>
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating_filter" value="1" <?= isset($_GET['rating_filter']) && $_GET['rating_filter'] == '1' ? 'checked' : '' ?>>
                    <label for="star1">★</label>
                </div>
                <button type="submit"><?= $current_lang['Search']; ?></button>
            </form>
        </div>

        <!-- Suodatus kielen mukaan -->
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="arvosteluSivu">
            <label><?= $current_lang['FilterLanguage']; ?></label>
            <button type="submit" name="lang_filter" value="fi">
                <img src="kuvat/suomenlippu.png" alt="Suomi" class="lang-icon">
            </button>
            <button type="submit" name="lang_filter" value="en">
                <img src="kuvat/englantilippu.png" alt="English" class="lang-icon">
            </button>
        </form>

        <!-- Suodatus tuotteen mukaan -->
        <div class="product-filter">
            <form method="GET" action="index.php">

                <label><?= $current_lang['FilterProduct']; ?></label>
                <input type="hidden" name="page" value="arvosteluSivu">
                <select name="product_id" id="product_id" class="product-select">
                    <option value=""><?= $current_lang['AllProducts']; ?></option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>" <?= $product['id'] == $product_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($product['nimi']) ?>
                            </option>
        <?php endforeach; ?>
        </select>
        <button type="submit"><?= $current_lang['Search']; ?></button>
        </form>
        </div>

        <!-- Tuotteen tiedot-->
        <?php if ($product_details): ?>
            <div class="product-details">
                <h2>
                    <?php
                    if ($_SESSION['lang'] == 'en') {  // If language is English
                        echo htmlspecialchars($product_details['nimi_en']);  // English product name
                    } else {
                        echo htmlspecialchars($product_details['nimi']);  // Finnish product name
                    }
                    ?>
                </h2>
                <!-- Tuotteen kuva-->
                <img class="product-image" src="<?= $product_details['kuva'] ?>"
                    alt="<?= htmlspecialchars($_SESSION['lang'] == 'en' ? $product_details['nimi_en'] : $product_details['nimi']) ?>">
                <p>
                    <?php
                    if ($_SESSION['lang'] == 'en') {  // If language is English
                        echo htmlspecialchars($product_details['kuvaus_en']);  // English product description
                    } else {
                        echo htmlspecialchars($product_details['kuvaus']);  // Finnish product description
                    }
                    ?>
                </p>
                <!-- Hinta-->
                <p><?= $current_lang['price']; ?> <?= number_format($product_details['hinta'], 2) ?></p>
            </div>
        <?php endif; ?>

        <!-- Linkki arvostelun lisäämiseen-->
        <a class="edit-btn" href="index.php?page=lisaaArvostelu">
            <?= $current_lang['leaveReview']; ?></a>

        <!-- Näytetään arvostelut-->
        <?php if (empty($reviews)): ?>
            <h3><?= $current_lang['no_reviews']; ?><?= $current_lang['give_review']; ?>
                <!-- Arvostelun antaminen-->
                <a href="index.php?page=lisaaArvostelu">Täältä</a>
            </h3>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3>
                        <!-- Arvostelun tiedot-->
                        <?= htmlspecialchars($review['otsikko']) ?>
                        <?php if ($review['kieli'] == 'fi'): ?>
                            <img src="kuvat/suomenlippu.png" alt="Finnish" class="lang-icon">
                        <?php elseif ($review['kieli'] == 'en'): ?>
                            <img src="kuvat/englantilippu.png" alt="English" class="lang-icon">
                        <?php endif; ?>
                    </h3>
                    <!-- Tähdillä arviointi-->
                    <p class="rating">Arvostelu:
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $review['tähtiarvostelu']) ? '★' : '☆';
                        }
                        ?>
                    </p>
                    <!-- Arvostelun kommentti, otsikko, kirjoittaja ja julkaisu aika-->
                    <p><?= nl2br(htmlspecialchars($review['kommentti'])) ?></p>
                    <p><em><?= $current_lang['writer']; ?>: <?= htmlspecialchars($review['nimi']) ?>
                            <br>
                            <?= $current_lang['date']; ?>: <?= $review['luotu'] ?></em></p>
                </div>
            <?php endforeach; ?>

<?php endif; ?>

</div>



