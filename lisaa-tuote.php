<?php  
    require_once("auth.php");
    require_once('config.php');
    
    $login = $_SESSION['SESS_LOGIN'];
    if ($login !== 'admin') {
        header('location: index.php?page=error');
        exit();
    }

    // Create PDO connection
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8"; // Correct DSN
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        
        // Set PDO attributes
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        // Handle database connection errors
        die('Database connection failed: ' . $e->getMessage());
    }

    // Fetch categories from the database
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
        $kuvaus = $_POST['kuvaus'];
        $hinta = $_POST['hinta'];
        $kategoriat_selected = $_POST['kategoriat'];  // Get the selected categories

        try {
            // Insert product into the 'tuotteet' table
            $stmt = $pdo->prepare("INSERT INTO tuotteet (nimi, kuvaus, hinta) VALUES (?, ?, ?)");
            $stmt->execute([$nimi, $kuvaus, $hinta]);

            $tuote_id = $pdo->lastInsertId();  // Get the last inserted product ID

            // Insert product-category relationships if categories are selected
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
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisää tuote</title>
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
            background-color: #4CAF50; /* Green for selected category */
            color: white;
        }
    </style>
</head>
<body>
    <div style="color: white; text-align: center;">
        <h1>Lisää tuote</h1>
        <a href="admin-panel.php">Back</a> | <a href="index.php?page=logout">Logout</a>
        <form action="" method="POST">

            <label for="nimi">Tuotteen nimi:</label>
            <input type="text" id="nimi" name="nimi" required>

            <label for="kuvaus">Tuotteen kuvaus:</label>
            <textarea id="kuvaus" name="kuvaus" rows="4" required></textarea>

            <label for="hinta">Tuotteen Hinta (€):</label>
            <input type="number" id="hinta" name="hinta" step="0.01" required>

            <h2>Valitse kategoria</h2>
            <div id="kategoriat">
                <?php if (!empty($kategoriat)):?>
                    <?php foreach ($kategoriat as $kategoria): ?>
                        <label>
                            <input type="checkbox" name="kategoriat[]" value="<?= $kategoria['id'] ?>">
                            <?= htmlspecialchars($kategoria['nimi']) ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Ei valittavia kategorioita</p>
                <?php endif; ?>

                
            <button type="submit">Lisää Tuote</button>
            </form>
            </div>
        </form>
    </div>
</body>
</html>
