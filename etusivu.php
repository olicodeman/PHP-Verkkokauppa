<?php
require_once('config.php');
session_start();
$IsLoggedIn = $_SESSION['loggedin'] ?? false;

// 
$lang = $_SESSION['lang'] ?? 'fi'; // Suomi oletuskielenä

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Haetaan kolme uusinta tuotetta tietokannasta oikeilla käyttäjätunnuksilla
$products = [];
$productNameColumn = ($lang === 'en') ? 'nimi_en' : 'nimi'; // Valitaan nimi kielen mukaan
$productDescriptionColumn = ($lang === 'en') ? 'kuvaus_en' : 'kuvaus'; // Valitaan kuvaus kielen mukaan

// Päivitetään SQL query hakemaan tuotteen tiedot myös oikealla kielellä 
$sql = "SELECT id, $productNameColumn AS nimi, $productDescriptionColumn AS kuvaus, hinta, kuva, varastomäärä FROM tuotteet ORDER BY id DESC LIMIT 3";
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
$sql = "SELECT r.id, r.nimi, r.otsikko, r.kommentti, r.tähtiarvostelu, r.luotu, t.$productNameColumn AS tuote_nimi 
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

<div style="text-align: center; color: white;">
    <br> <br>
    <img src="kuvat/KGiconi.png" alt="KG" class="KG">
    <h3><?= $current_lang['subtitle']; ?></h3>
    <p><?= $current_lang['description']; ?></p>

    <!-- Tarkistetaan onko käyttäjä kirjautuneena ja näytetään linkit jos ei ole-->
    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <a style="margin-right: 10px;" id="login-btn" class="edit-btn" href="index.php?page=login-form">
            <?= $current_lang['login']; ?>
        </a>
        <a class="edit-btn" href="index.php?page=register-form">
            <?= $current_lang['register']; ?>
        </a>
    <?php endif; ?>

    <br><br>
    <!-- Näytetään uusimmat tuotteet-->
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
                    <!-- Tuotteen kuva -->
                    <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>"
                        style="width: 100%; height: auto; border-radius: 5px;">

                    <!-- Tuotteen nimi valitulla kielellä -->
                    <h4 style="color: gold;"><?= htmlspecialchars($product['nimi']) ?></h4>

                    <!-- Tuotteen hinta ja "hinta" käännös -->
                    <p><strong><?= $current_lang['price']; ?>:</strong> €<?= number_format($product['hinta'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Ei tuotteita saatavilla ilmoitus -->
            <p><?= $current_lang['no_products']; ?></p>
        <?php endif; ?>
    </div>
</div>



<!-- Popup modal. Näytetään tuotteen tiedot -->
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

    <!-- Ostoskori iconi -->
    <div class="icon">
        <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Lisää ostoskoriin" onclick="addToCart()"
            style="width: 40px; height: 40px; cursor: pointer; margin-top: 10px;">
    </div>
    
    <!-- Arvostelun lisäys nappi -->
    <div class="center-align">
        <a class="edit-btn" href="index.php?page=lisaaArvostelu">
            <?= $current_lang['leaveReview']; ?></a>

    <!-- Arviostelujen lukemis nappi -->
        <div class="center-align">
            <a class="edit-btn" href="index.php?page=arvosteluSivu">
                <?= $current_lang['read_reviews']; ?></a>
        </div>
        </a>
    </div>
</div>

<!-- Näytetään uusimmat arvostelut -->
<div style="text-align: center; color: white; margin-top: 50px;">
    <h2><?= $current_lang['latest_reviews']; ?></h2>

    <a class="edit-btn" href="index.php?page=lisaaArvostelu">
        <?= $current_lang['leaveReview']; ?></a>
    <a class="edit-btn" href="index.php?page=arvosteluSivu">
        <?= $current_lang['read_reviews']; ?></a>
    <div class="reviews-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h4><?= htmlspecialchars($review['nimi']) ?> - <?= htmlspecialchars($review['tuote_nimi']) ?></h4>
                    <p><strong>
                            <a><?= $current_lang['review_title']; ?></a></strong> <?= htmlspecialchars($review['otsikko']) ?>
                    </p>
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
        // Pop upin tietojen määrittely
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

            // Päivitetään popup sisältöä
            popupTitle.textContent = title || "No title";
            popupDescription.textContent = description || "No description";
            popupImg.src = imageUrl || "";
            popupPrice.textContent = "<?= $current_lang['price']; ?>: €" + parseFloat(price).toFixed(2);
            popupStock.textContent = "<?= $current_lang['Stock']; ?>: " + stock + " kpl";

            // Asetetaan product ID ostoskoriin lisäämistä varten 
            document.getElementById('popup').setAttribute('data-product-id', id);

            const addToCartIcon = document.querySelector('.popup .icon img');
            const quantityInput = document.getElementById('popup-quantity');
            const quantityLabel = document.querySelector('.keskita label');

            // Lisätään ostoskoriin ja jos tuotteita ei ole tarpeeksi annetaan ilmoitus
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

            // Näytetään pop up
            popup.classList.add('show');
            overlay.classList.add('show');
        }

        function addToCart() {
            // Haetaan tiedot popupista
            const title = document.getElementById('popup-title').textContent;
            const price = document.getElementById('popup-price').textContent.replace('<?= $current_lang['price']; ?>: €', '');
            const stock = parseInt(document.getElementById('popup-stock').textContent.replace('<?= $current_lang['Stock']; ?>: ', '').replace(' kpl', ''), 10); // Convert stock to number
            const quantity = parseInt(document.getElementById('popup-quantity').value, 10); // Get and parse quantity
            const productID = document.getElementById('popup').getAttribute('data-product-id');
            const imageUrl = document.getElementById('popup-img').src;

            // Tarkistetaan ylittääkö määrä varaston.
            if (quantity > stock) {
                alert('<?= $current_lang['NotEnoughProducts']; ?>');
                return;
            }

            // Lähetetään tuotteen tiedot
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
                    quantity: quantity, // Lisätään määrä tietoihin
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

        function searchProduct() {
            // Haetaan search input määrä
            const searchValue = document.getElementById('searchInput').value.toLowerCase();

            // Haetaan kaikki tuotteen elementit
            const products = document.querySelectorAll('.product');

            // Loop kaikkien tuotteiden läpi
            products.forEach(product => {
                // Haetaan tuotteen nimi
                const productName = product.querySelector('.name').textContent.toLowerCase();

                // Tarkistetaan sopiiko tuotteen nimi hakukenttään 
                if (searchValue == "") {
                    product.style.display = 'block';
                }
                else if (productName.includes(searchValue)) {
                    // Näytetään tuote jos sopii
                    product.style.display = 'block';
                } else {
                    // Piilotetaan jos ei
                    product.style.display = 'none';
                }
            });
        }

        // Piilotetaan popup
        function hidePopup() {
            const popup = document.getElementById('popup');
            const overlay = document.getElementById('overlay');
            popup.classList.remove('show');
            overlay.classList.remove('show');
        }

        function filterByCategory() {
            const selectedCategory = document.getElementById('categorySelect').value.toLowerCase(); // Haetaan valittu kategoria
            const products = document.querySelectorAll('.product'); // Kaikki tuotteen elementit

            products.forEach(product => {
                const productCategories = product.getAttribute('data-categories').toLowerCase(); // Haetaan tuotteen kategoriat

                // Näytetään tai piilotetaan kategorian perusteella
                if (selectedCategory === "all" || productCategories.includes(selectedCategory)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

    </script>
