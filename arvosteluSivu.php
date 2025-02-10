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

    // Filter by product if selected
    if ($product_id) {
        $query .= " AND tuote_id = :product_id";
    }

    // Filter by rating if selected
    if ($rating_filter) {
        $query .= " AND tähtiarvostelu = :rating_filter";
    }

    // Filter by language if selected
    if ($lang_filter) {
        $query .= " AND kieli = :lang_filter";
    }

    $query .= " ORDER BY luotu DESC";

    $stmt = $pdo->prepare($query);

    // Bind parameters
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

// Dynamically select columns based on the language selected
$productNameColumn = ($_SESSION['lang'] == 'en') ? 'nimi_en' : 'nimi';
$productDescriptionColumn = ($_SESSION['lang'] == 'en') ? 'kuvaus_en' : 'kuvaus';

// Fetch product information in the selected language
$stmt = $pdo->prepare("SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
$stmt->execute();
$products = $stmt->fetchAll();


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Trebuchet MS', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        h1 {
            text-align: center;
            color: rgb(45, 45, 102);
            margin-top: 20px;
        }

        .review-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        /* Filter Section */
        .product-filter {
            margin-bottom: 20px;
            text-align: center;
        }

        .product-select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: rgb(45, 45, 102);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(35, 35, 90);
        }

        /* Review Styling */
        .review {
            background: rgb(45, 45, 102);
            color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
            padding: 20px;
            text-align: left;
        }

        .review h3 {
            margin-top: 0;
            font-size: 1.5em;
        }

        .rating {
            color: gold;
            font-size: 1.2em;
            margin: 10px 0;
        }

        .review p {
            line-height: 1.5;
            font-size: 1em;
            margin: 10px 0;
        }

        .review-footer {
            font-size: 0.9em;
            color: #ddd;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .review-container {
                padding: 10px;
            }

            .review {
                padding: 15px;
            }
        }

        p {}

        /* Product Details */
        .product-details {
            background: rgb(45, 45, 102);
            ;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
            padding: 20px;
            margin: 20px auto;
            /* Center on the page */
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

        /* Tähdille css */
        .star-filter {
            text-align: center;
            margin-bottom: 20px;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 30px;
            color: rgb(180, 99, 99);
            cursor: pointer;
            transition: color 0.3s;
        }

        .star-rating input[type="radio"]:checked~label {
            color: rgb(255, 204, 0);
        }

        .star-rating label:hover {
            color: rgb(180, 149, 99);
        }

        label {
            color: white;
        }

        .edit-btn {
            margin-left: 40%;
            background-color: #4545a6;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .edit-btn:hover {
            background-color: rgb(85, 85, 145);
        }

        /* Language Button Styling */
        .lang-icon {
            width: 30px;
            /* Adjust size */
            height: auto;
            border-radius: 50%;
            /* Make it rounded */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button img {
            border: none;
            background: none;
            cursor: pointer;
        }

        button:hover .lang-icon {
            transform: scale(1.1);
            /* Slightly enlarge on hover */
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
        }
        .lang-icon {
    width: 20px;  /* Adjust size */
    height: auto;
    margin-left: 10px; /* Adds spacing between text and flag */
    vertical-align: middle;
}

    </style>
</head>

<body>
    <div class="review-container">
        <h1><?= $current_lang['AllReviews']; ?></h1>

        <!-- Tähtiarvostelujenmukaan suodatus-->
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
                    if ($_SESSION['lang'] == 'en') {  // If the language is English
                        echo htmlspecialchars($product_details['nimi_en']);  // English product name
                    } else {
                        echo htmlspecialchars($product_details['nimi']);  // Default product name (Finnish)
                    }
                    ?>
                </h2>
                <img class="product-image" src="<?= $product_details['kuva'] ?>"
                    alt="<?= htmlspecialchars($_SESSION['lang'] == 'en' ? $product_details['nimi_en'] : $product_details['nimi']) ?>">
                <p>
                    <?php
                    if ($_SESSION['lang'] == 'en') {  // If the language is English
                        echo htmlspecialchars($product_details['kuvaus_en']);  // English description
                    } else {
                        echo htmlspecialchars($product_details['kuvaus']);  // Default description (Finnish)
                    }
                    ?>
                </p>
                <p><?= $current_lang['price']; ?>     <?= number_format($product_details['hinta'], 2) ?></p>
            </div>
        <?php endif; ?>


        <a class="edit-btn" href="index.php?page=lisaaArvostelu">
            <?= $current_lang['leaveReview']; ?></a>

        <!-- Näytetään arvostelut-->
        <?php if (empty($reviews)): ?>
            <h3><?= $current_lang['no_reviews']; ?><?= $current_lang['give_review']; ?><a
                    href="index.php?page=lisaaArvostelu">Tästä</a></h3>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3>
                        <?= htmlspecialchars($review['otsikko']) ?>
                        <?php if ($review['kieli'] == 'fi'): ?>
                            <img src="kuvat/suomenlippu.png" alt="Finnish" class="lang-icon">
                        <?php elseif ($review['kieli'] == 'en'): ?>
                            <img src="kuvat/englantilippu.png" alt="English" class="lang-icon">
                        <?php endif; ?>
                    </h3>
                    <p class="rating">Arvostelu:
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo ($i <= $review['tähtiarvostelu']) ? '★' : '☆';
                        }
                        ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($review['kommentti'])) ?></p>
                    <p><em><?= $current_lang['writer']; ?>: <?= htmlspecialchars($review['nimi']) ?>
                            <br>
                            <?= $current_lang['date']; ?>: <?= $review['luotu'] ?></em></p>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</body>

</html>