<?php
require_once('config.php');
require_once('lang.php');
session_start();

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fi'; // Default to Finnish
}
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fi'])) {
    $_SESSION['lang'] = $_GET['lang'];
}



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
    $stmt = $pdo->prepare("SELECT id, nimi, kuvaus, kuva, hinta, varastomaara FROM tuotteet");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
    $products = [];
}

try {
    $lang = $_SESSION['lang']; // Get the language from the session

    // Modify your category query to select the correct column based on the language
    $categoryStmt = $pdo->prepare("
        SELECT id, " . ($lang == 'en' ? "nimi_en" : "nimi") . " AS nimi 
        FROM kategoriat
    ");
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll();    
} catch (PDOException $e) {
    echo '<p class="error">Error fetching categories: ' . $e->getMessage() . '</p>';
    $categories = [];
}

$lang = $_SESSION['lang']; // Haetaan kieli istunnosta
$sql = "
    SELECT 
        t.id, 
        " . ($lang == 'en' ? "t.nimi_en" : "t.nimi") . " AS nimi,
        " . ($lang == 'en' ? "t.kuvaus_en" : "t.kuvaus") . " AS kuvaus,
        t.kuva, t.hinta, t.varastomaara,
        COALESCE(GROUP_CONCAT(" . ($lang == 'en' ? "k.nimi_en" : "k.nimi") . " SEPARATOR ','), '') AS categories,
        COALESCE(AVG(a.arvosana), 0) AS avg_rating
    FROM tuotteet t
    LEFT JOIN tuote_kategoria tk ON t.id = tk.tuote_id
    LEFT JOIN kategoriat k ON tk.kategoria_id = k.id
    LEFT JOIN arvostelut a ON t.id = a.tuote_id
    GROUP BY t.id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Gadget Tuotesivu</title>
    <style>
        /* tuote ikkunan määritykset*/
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px;
        }

        .product {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            text-align: center;
            background-color: darkslateblue;
            transition: transform 0.3s, background-color 0.3s;
            padding: 20px;
            /* Lisää vähän tilaa ympärille */
        }

        .product:hover {
            transform: scale(1.05);
            background-color: darkcyan;
        }

        .product img {
            width: 100%;
            height: auto;
            object-fit: cover;
            /* Varmistaa, että kuva täyttää alueen ilman venyttämistä */
            background-color: slateblue;
        }

        /* Pop-up-ikkuna css */
        .popup {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: rgb(45, 45, 102);
            color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
            padding: 20px;
        }

        .popup img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .popup h2 {
            margin: 15px 0 10px;
            font-size: 20px;
            text-align: center;
        }

        .popup p {
            text-align: center;
            margin: 10px 0 20px;
        }

        .popup .icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .popup .icon img {
            width: 40px;
            height: 40px;
            cursor: pointer;
        }

        /* Rasti poistumiselle*/
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            color: black;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Tummennettu tausta */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
        }

        .show {
            display: block;
        }

        .search-bar-container {
            margin: 20px auto;
            max-width: 500px;
            text-align: center;
        }

        .search-bar {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-btn {
            background-color: darkslateblue;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            border: 1px solid #ccc;
            padding: 10px 10px;
            transition: background-color 0.3s;
        }

        .search-btn:hover {
            background-color: darkcyan;
        }

        .category-container {
            margin: 20px auto;
            max-width: 300px;
            text-align: center;
        }

        .category-select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .keskita {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        /* Keskitetään määrä */
        .keskita label,
        .keskita input {
            max-width: 50px;
            width: 100%;
        }

        .center-align {
            display: flex;
            justify-content: center;
            /* Keskittää vaakasuunnassa */
            align-items: center;
            /* Keskittää pystysuunnassa */
            margin: 20px 0;
            /* Lisää tilaa ylä- ja alapuolelle */
        }

        .edit-btn {
            margin-right: 10px;
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
        .rating {
    color: gold;
    font-size: 18px;
    margin: 5px 0;
}

/* Adjust the popup width on smaller screens */
@media (max-width: 768px) {
    .popup {
        width: 95%; /* Increase width on smaller screens */
        max-width: 450px; /* Make it larger while maintaining some limits */
    }
.product-grid {
        width: 90%; /* Product grid stays wider */
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Bigger products */
    }
form {
    width: 100%;
    max-width: 350px; /* Adjust as needed */
    margin: 0 auto; /* Centers the form */
}

}

/* Adjust the product grid for larger product display on smaller screens */
@media (max-width: 600px) {
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Increase size of products */
    }
}
    </style>
</head>
<script>
    function showDetails(name, description) {
        document.getElementById('product-details').innerHTML = `
        <h2>${name}</h2>
        <p>${description}</p>
        `;
    }
</script>

<body>
    <h1 style="text-align: center;"><?= $current_lang['WelcomeProducts']; ?></h1>
    <br>
    <div class="search-bar-container">
        <input type="text" id="searchInput" class="search-bar" placeholder="<?= $current_lang['SearchProduct']; ?>">
        <button class="search-btn" onclick="searchProduct()"><?= $current_lang['Search']; ?></button>
    </div>

    <div class="category-container">
    <select id="categorySelect" class="category-select" onchange="filterByCategory()">
        <option value="all"><?= $current_lang['allKategories']; ?></option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['nimi']) ?>"><?= htmlspecialchars($category['nimi']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

    <form>
    <!-- Tuoteruudukko -->
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product" data-id="<?= htmlspecialchars($product['id']) ?>"
                data-categories="<?= htmlspecialchars($product['categories']) ?>"
                onclick="showPopup('<?= htmlspecialchars($product['id']) ?>', '<?= htmlspecialchars($product['nimi']) ?>', '<?= htmlspecialchars($product['kuvaus']) ?>', '<?= htmlspecialchars($product['kuva']) ?>', '<?= htmlspecialchars($product['hinta']) ?>', '<?= htmlspecialchars($product['varastomaara']) ?>')">
                
                <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>">

                <!-- Product name -->
                <p style="color: gold;" class="name"><?= htmlspecialchars($product['nimi']) ?></p>

                <!-- Product description -->
                <p class="description"><?= htmlspecialchars($product['kuvaus']) ?></p>

                <!-- Product rating -->
                <p class="rating">
                    <?php
                    $rating = round($product['avg_rating']); // Pyöristetään lähimpään kokonaislukuun
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '★'; // Täytetty tähti
                        } else {
                            echo '☆'; // Tyhjä tähti
                        }
                    }
                    ?>
                </p>

                <!-- Product price -->
                <p class="price">€<?= number_format($product['hinta'], 2) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</form>

    <!-- Tummennettu tausta -->
    <div class="overlay" id="overlay" onclick="hidePopup()"></div>
    <div class="popup" id="popup">
        <button type="button" class="close-btn" onclick="hidePopup(event)">X</button>
        <img id="popup-img" src="" alt="Tuotteen kuva">
        <h2 id="popup-title"></h2>
        <p id="popup-description"></p>
        <p id="popup-price"></p>
        <p id="popup-varastomaara"></p>
        <!-- Määrä jota halutaan ostaa -->
        <div class="keskita">
            <label for="popup-quantity"><?= $current_lang['quantity']; ?>:</label>
            <input id="popup-quantity" type="number" min="1" value="1" step="1" onchange="updateSelectedQuantity()">
        </div>
        <!-- Ostoskoriin lisääminen -->
        <div class="icon">
            <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Lisää ostoskoriin"
                onclick="addToCartFromPopup()">
        </div>
        <div class="center-align">
            <a class="edit-btn" id="register-btn" href="index.php?page=lisaaArvostelu"><?= $current_lang['give_review']; ?></a>
            
        <div class="center-align">
            <a class="edit-btn" id="register-btn" href="index.php?page=arvosteluSivu"><?= $current_lang['read_reviews']; ?></a>
        </div>

        <script>
            
           function filterByCategory() {
            const selectedCategory = document.getElementById('categorySelect').value.toLowerCase();
            const products = document.querySelectorAll('.product');

            products.forEach(product => {
                const productCategories = product.getAttribute('data-categories').toLowerCase();
                if (selectedCategory === 'all' || productCategories.includes(selectedCategory)) {
                    product.style.display = 'block'; // Show the product
                } else {
                    product.style.display = 'none'; // Hide the product
                }
            });
        }

        function searchProduct() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const products = document.querySelectorAll('.product');

        products.forEach(product => {
            const productName = product.querySelector('.name').textContent.toLowerCase();
            const productDescription = product.getAttribute('data-description') || '';

            if (productName.includes(searchInput) || productDescription.includes(searchInput)) {
                product.style.display = 'block'; // Show the product
            } else {
                product.style.display = 'none'; // Hide the product
            }
        });
    }

            // näytetään tuote popup
            function showPopup(id, title, description, imageUrl, price, stock) {
                // popup yksityiskohdat
                document.getElementById('popup-title').textContent = title;
                document.getElementById('popup-description').textContent = description;
                document.getElementById('popup-img').src = imageUrl;
                document.getElementById('popup-price').textContent = "<?= $current_lang['price']; ?>: €" + parseFloat(price).toFixed(2);
                document.getElementById('popup-varastomaara').textContent = "<?= $current_lang['Stock']; ?>: " + stock + " kpl";

                // Asetetaan tuote ID ostoskoriinlisäämistä varten 
                document.getElementById('popup').setAttribute('data-product-id', id);

                // Tarkistetaan varasto ja päivitetään popup sen mukaan 
                const addToCartIcon = document.querySelector('.popup .icon img'); // Lisää ostoskoriin icon
                const quantityInput = document.getElementById('popup-quantity'); // Määrä joka on asetettu 
                const quantityLabel = document.querySelector('.keskita label');

                if (stock == 0) {
                    // Varaston määrä on 0, piilotetaan kohtia jos näin
                    quantityInput.style.display = 'none'; // piilotetaan määrä
                    quantityLabel.style.display = 'none'; // Piilotetaan määrän label
                    const stockMessageElement = document.getElementById('popup-varastomaara'); // Näytetään varastotyhjä viesti
                    stockMessageElement.innerHTML = `<span style="color: red;">Varasto tyhjä</span>, täytämme sen mahdollisimman pian!`;


                    // Muutetaan kuva ja laitetaan niin ettei sitä voida klikata
                    addToCartIcon.src = "https://img.icons8.com/?size=100&id=7850&format=png&color=FFFFFF"; // muutetaan kuva 
                    addToCartIcon.onclick = null; //Ei voi klikata
                } else {
                    // Varastossa on tuote joten näytetään määrä ja miten paljon asiakas haluaa tilata
                    quantityInput.style.display = 'block'; // Näytetään määrä jota voidaan valita
                    quantityLabel.style.display = 'block'; // Näytetään määrä

                    // Muokataan kuva toiminnalliseksi ja laitetaan alkuperäinen kuva
                    addToCartIcon.src = "https://cdn-icons-png.flaticon.com/512/6713/6713719.png";
                    addToCartIcon.onclick = function () { addToCartFromPopup(); }; // toiminnallinen klikkaus
                }

                // näytetään popup ja overlay
                document.getElementById('popup').classList.add('show');
                document.getElementById('overlay').classList.add('show');
            }

            // Piilota popup
            function hidePopup() {
                document.getElementById('popup').classList.remove('show');
                document.getElementById('overlay').classList.remove('show');
            }

            // Sulje napin toiminto
            document.querySelector('.close-btn').addEventListener('click', function (event) {
                event.stopPropagation();
                hidePopup(); // piilotetaan popup kun painetaan poistumista
            });

            //lisätään tuote ostoskoriin popupista
            function addToCartFromPopup() {
                const title = document.getElementById('popup-title').textContent;
                const price = document.getElementById('popup-price').textContent.replace('<?= $current_lang['price']; ?>: €', '');
                const stock = parseInt(document.getElementById('popup-varastomaara').textContent.replace('<?= $current_lang['Stock']; ?>: ', '').replace(' kpl', ''), 10);
                const quantity = parseInt(document.getElementById('popup-quantity').value, 10);
                const productID = document.getElementById('popup').getAttribute('data-product-id');
                const imageUrl = document.getElementById('popup-img').src;

                if (quantity > stock) {
                    alert('<?= $current_lang['NotEnoughProducts']; ?>');
                    return;
                }

                fetch('lisaa-ostoskoriin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: productID,
                        name: title,
                        price: parseFloat(price),
                        stock: stock,
                        image: imageUrl,
                        quantity: quantity,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('<?= $current_lang['AddedToCart']; ?>');
                        } else {
                            alert('<?= $current_lang['BeforeCart']; ?>');
                        }
                    })
                    .catch(error => {
                        console.error('Virhe:', error);
                        alert('Yhteysvirhe. Yritä myöhemmin uudelleen.');
                    });
            }
        </script>