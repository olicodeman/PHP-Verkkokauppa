<?php
require_once('config.php');

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch products to populate the dropdown for filtering
try {
    $stmt = $pdo->prepare("SELECT id, nimi FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
    $products = [];
}

// Fetch reviews based on selected product or all reviews
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

try {
    if ($product_id) {
        // Fetch reviews for a specific product
        $stmt = $pdo->prepare("SELECT * FROM arvostelut WHERE tuote_id = :product_id ORDER BY luotu DESC");
        $stmt->bindParam(':product_id', $product_id);
    } else {
        // Fetch all reviews
        $stmt = $pdo->prepare("SELECT * FROM arvostelut ORDER BY luotu DESC");
    }
    $stmt->execute();
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching reviews: ' . $e->getMessage() . '</p>';
    $reviews = [];
}
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
        p{
        }
    </style>
</head>

<body>
    <div class="review-container">
        <h1>Kaikki arvostelut</h1>
        <!-- Filter by Product -->
        <div class="product-filter">
        <form method="GET" action="index.php">
    <input type="hidden" name="page" value="arvosteluSivu">
    <select name="product_id" id="product_id" class="product-select">
        <option value="">Kaikki tuotteet</option>
        <?php foreach ($products as $product): ?>
            <option value="<?= $product['id'] ?>" <?= $product['id'] == $product_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($product['nimi']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Hae</button>
</form>

        </div>
        <script>
    document.getElementById('reviewForm').addEventListener('submit', function (e) {
        e.target.action = "index.php?page=arvosteluSivu";
    });
</script>

        <!-- Display Reviews -->
        <?php if (empty($reviews)): ?>
            <h3>Ei arvosteluja. Anna arvostelu <a   href="index.php?page=lisaaArvostelu">Tästä</a></h3>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3><?= htmlspecialchars($review['otsikko']) ?></h3>
                    <p class="rating">Arvostelu: 
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['tähtiarvostelu']) {
                                echo '★'; // Filled star
                            } else {
                                echo '☆'; // Empty star
                            }
                        }
                        ?>
                    </p>
                    <p><?= nl2br(htmlspecialchars($review['kommentti'])) ?></p>
                    <p><em>Kirjoittaja: <?= htmlspecialchars($review['nimi']) ?> 
                    <br>
                    Pvm <?= $review['luotu'] ?></em></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>
