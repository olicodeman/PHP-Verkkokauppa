
<?php
require_once('config.php');
session_start();
$IsLoggedIn = $_SESSION['loggedin'] ?? false;

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the 3 newest products
$products = [];
$sql = "SELECT id, nimi, kuvaus, hinta, kuva, varastomäärä FROM tuotteet ORDER BY id DESC LIMIT 3";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();

// Yhdistetään tietokantaan uudelleen (käytetään samaa yhteyttä)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hae uusimmat arvostelut (3 viimeisintä)
$reviews = [];
$sql = "SELECT r.id, r.nimi, r.otsikko, r.kommentti, r.tähtiarvostelu, r.luotu, t.nimi AS tuote_nimi 
        FROM arvostelut r
        JOIN tuotteet t ON r.tuote_id = t.id 
        ORDER BY r.luotu DESC LIMIT 3";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
$conn->close();
?>

?>
<style>
    /* Popup and overlay styles */
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: #2d2d66;
        color: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 90%;
        padding: 20px;
        text-align: center;
    }

    .popup img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    .popup h4,
    .popup p {
        margin: 10px 0;
    }

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

    .product:hover {
        transform: scale(1.05);
        background-color: darkcyan;
    }

    .product {
        border: 1px solid white;
        padding: 10px;
        border-radius: 5px;
        width: 200px;
        text-align: left;
        background-color: darkslateblue;
        cursor: pointer;
        transition: transform 0.3s, background-color 0.3s;
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

    .reviews-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        padding: 20px 0;
        max-width: 1200px;
        margin: 0 auto;
    }

    .review {
        background-color: #2d2d66;
        color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        width: 30%;
        text-align: left;
        flex: 1 1 30%;
        box-sizing: border-box;
    }

    .review h4 {
        color: gold;
        margin: 0;
    }

    .review p {
        font-size: 14px;
        line-height: 1.6;
    }

    .review em {
        color: #bbb;
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
</style>

<div style="text-align: center; color: white;">
    <h1><?= $current_lang['title']; ?></h1>
    <h3><?= $current_lang['subtitle']; ?></h3>
    <p><?= $current_lang['description']; ?></p>

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <a style="margin-right: 10px;" id="login-btn" class="edit-btn" href="index.php?page=login-form">
            <?= $current_lang['login']; ?>
        </a>
        <a class="edit-btn" id="register-btn" href="index.php?page=register-form">
            <?= $current_lang['register']; ?>
        </a>
    <?php endif; ?>

    <br><br>
    <h2><?= $current_lang['latest_products']; ?></h2>
    <div style="display: flex; justify-content: center; gap: 20px;">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product" onclick="showPopup(<?= $product['id'] ?>, 
                                    '<?= htmlspecialchars($product['nimi']) ?>', 
                                    '<?= htmlspecialchars($product['kuvaus']) ?>', 
                                    '<?= htmlspecialchars($product['kuva']) ?>', 
                                    '<?= $product['hinta'] ?>', 
                                    '<?= $product['varastomäärä'] ?>')">
                    <!-- Display product image -->
                    <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>"
                        style="width: 100%; height: auto; border-radius: 5px;">

                    <!-- Product name in the selected language -->
                    <h4 style="color: gold;"><?= htmlspecialchars($product['nimi']) ?></h4>

                    <!-- Product price with translation for 'Price' -->
                    <p><strong><?= $current_lang['price']; ?>:</strong> €<?= number_format($product['hinta'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Message when no products are available -->
            <p><?= $current_lang['no_products']; ?></p>
        <?php endif; ?>
    </div>



    <!-- Popup modal -->
    <div class="overlay" id="overlay" onclick="hidePopup()"></div>
    <div class="popup" id="popup">
        <button type="button"
            style="position: absolute; top: 10px; right: 10px; background: white; color: black; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;"
            onclick="hidePopup(event)">×</button>
        <img id="popup-img" src="" alt="Tuotekuva">
        <h4 id="popup-title"></h4>
        <p id="popup-description"></p>
        <p><strong id="popup-price"></strong></p>
        <p id="popup-stock"></p>
        <!-- Määrä jota halutaan ostaa -->
        <div class="keskita">
            <label for="popup-quantity">
                <?= $current_lang['quantity']; ?>
            </label>
            <input id="popup-quantity" type="number" min="1" value="1" step="1" onchange="updateSelectedQuantity()">
        </div>

        <!-- Lisätään ostoskoriin iconi -->
        <div class="icon">
            <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Lisää ostoskoriin"
                onclick="addToCart()" style="width: 40px; height: 40px; cursor: pointer; margin-top: 10px;">
        </div>
        <div class="center-align">
            <a class="edit-btn" id="register-btn" href="index.php?page=lisaaArvostelu">
                <?= $current_lang['leaveReview']; ?></a>

                <div class="center-align">
                    <a class="edit-btn" id="register-btn" href="index.php?page=arvosteluSivu">
                    <?= $current_lang['read_reviews']; ?></a>
        </div>
            </a>
        </div>
    </div>

</div>
<div style="text-align: center; color: white; margin-top: 50px;">
    <h2><?= $current_lang['latest_reviews']; ?></h2>

    <a class="edit-btn" href="index.php?page=lisaaArvostelu">
    <?= $current_lang['leaveReview']; ?></a>
    <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h4><?= htmlspecialchars($review['nimi']) ?> - <?= htmlspecialchars($review['tuote_nimi']) ?></h4>
                    <p><strong>
                    <a><?= $current_lang['review_title']; ?></a></strong> <?= htmlspecialchars($review['otsikko']) ?></p>
                    <p><strong><a><?= $current_lang['review']; ?></a></strong> <?= htmlspecialchars($review['kommentti']) ?></p>
                    <p><strong><a><?= $current_lang['stars']; ?></a></strong> <?= str_repeat("★", $review['tähtiarvostelu']) ?>
                        <?= $review['tähtiarvostelu'] ?>/5
                    </p>
                    <p><em><?= $current_lang['publish_date']; ?><?= date('d.m.Y', strtotime($review['luotu'])) ?></em></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $current_lang['no_reviews']; ?></p>
        <?php endif; ?>
    </div>

    <script>
        function showPopup(id, title, description, imageUrl, price, stock) {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            const popupTitle = document.getElementById('popup-title');
            const popupDescription = document.getElementById('popup-description');
            const popupImg = document.getElementById('popup-img');
            const popupPrice = document.getElementById('popup-price');
            const popupStock = document.getElementById('popup-stock');

            if (!popup || !overlay || !popupTitle || !popupDescription || !popupImg || !popupPrice || !popupStock) {
                console.error("Popup elements missing in the DOM");
                return;
            }

            // Update popup content
            popupTitle.textContent = title || "No title";
            popupDescription.textContent = description || "No description";
            popupImg.src = imageUrl || "";
            popupPrice.textContent = "Hinta: €" + parseFloat(price).toFixed(2);
            popupStock.textContent = "Varastossa: " + stock + " kpl";

            // Set product ID for adding to cart
            document.getElementById('popup').setAttribute('data-product-id', id);

            const addToCartIcon = document.querySelector('.popup .icon img');
            const quantityInput = document.getElementById('popup-quantity');
            const quantityLabel = document.querySelector('.keskita label');

            if (stock == 0) {
                quantityInput.style.display = 'none';
                quantityLabel.style.display = 'none';
                popupStock.innerHTML = `<span style="color: red;">Varasto tyhjä</span>, täytämme sen mahdollisimman pian!`;
                addToCartIcon.src = "https://img.icons8.com/?size=100&id=7850&format=png&color=FFFFFF";
                addToCartIcon.style.opacity = 0.5;
                addToCartIcon.onclick = null;
            } else {
                quantityInput.style.display = 'block';
                quantityLabel.style.display = 'block';
                addToCartIcon.src = "https://cdn-icons-png.flaticon.com/512/6713/6713719.png";
                addToCartIcon.onclick = function () { addToCart(); };
            }

            // Show the popup and overlay
            popup.classList.add('show');
            overlay.classList.add('show');
        }



        function addToCart() {
            // Haetaan tiedot popupista
            const title = document.getElementById('popup-title').textContent;
            const price = document.getElementById('popup-price').textContent.replace('Hinta: €', '');
            const stock = parseInt(document.getElementById('popup-stock').textContent.replace('Varastossa: ', '').replace(' kpl', ''), 10); // Convert stock to number
            const quantity = parseInt(document.getElementById('popup-quantity').value, 10); // Get and parse quantity
            const productID = document.getElementById('popup').getAttribute('data-product-id');
            const imageUrl = document.getElementById('popup-img').src;

            // tarkistetaan ylittääkö määrä varaston.
            if (quantity > stock) {
                alert('Varastossa ei ole tarpeeksi tuotteita.');
                return;
            }

            // lähetetään tuotteen tiedot
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
                    quantity: quantity, // lisätään määrä tietoihin
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tuote lisätty ostoskoriin!');
                    } else {
                        alert('Ennen ostoskoriin lisäämistä, kirjaudu sisään.');
                    }
                })
                .catch(error => {
                    console.error('Virhe:', error);
                    alert('Yhteysvirhe. Yritä myöhemmin uudelleen.');
                });
        }


        // Valmistellaan tuotteen
        const title = document.getElementById('popup-title').textContent;
        const productData = {
            title: title.trim(),
            price: parseFloat(price.trim()),
            stock: parseInt(stock.trim(), 10),
            image: imageUrl
        };

        function searchProduct() {
            // Get the search input value
            const searchValue = document.getElementById('searchInput').value.toLowerCase();

            // Get all product elements
            const products = document.querySelectorAll('.product');

            // Loop through all products
            products.forEach(product => {
                // Get the product name
                const productName = product.querySelector('.name').textContent.toLowerCase();

                // Check if the product name matches the search value
                if (searchValue == "") {
                    product.style.display = 'block';
                }
                else if (productName.includes(searchValue)) {
                    // Show the product if it matches
                    product.style.display = 'block';
                } else {
                    // Hide the product if it doesn't match
                    product.style.display = 'none';
                }
            });
        }

        // Hide the popup
        function hidePopup() {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            popup.classList.remove('show');
            overlay.classList.remove('show');
        }

        function filterByCategory() {
            const selectedCategory = document.getElementById('categorySelect').value.toLowerCase(); // Get selected category
            const products = document.querySelectorAll('.product'); // All product elements

            products.forEach(product => {
                const productCategories = product.getAttribute('data-categories').toLowerCase(); // Get product categories

                // Show or hide products based on selected category
                if (selectedCategory === "all" || productCategories.includes(selectedCategory)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

    </script>