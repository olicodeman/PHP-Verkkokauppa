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
    $sql = "SELECT nimi, kuvaus, hinta, kuva, varastomäärä FROM tuotteet ORDER BY id DESC LIMIT 3";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    $conn->close();
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

    .popup h4, .popup p {
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

    /* Product hover effect */
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
</style>

<div style="text-align: center; color: white;">
    <h1>KG Keittiövälineet</h1>
    <h3>Innovatiiviset Keittiövälineet</h3>
    <p>Tuomme keittiöösi käytännöllisyyttä ja tyyliä! KG Keittiökalusteet tarjoaa laadukkaita keittiögadgeteja, 
    jotka tekevät arjesta sujuvampaa ja ruoanlaitosta nautinnollisempaa.</p>

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <a style="margin-right: 10px;" id="login-btn" class="edit-btn" href="index.php?page=login-form">Kirjaudu sisään</a>
        <a class="edit-btn" id="register-btn" href="index.php?page=register-form">Rekisteröidy</a>
    <?php endif; ?>
    <br><br>
    <h2>Uusimmat tuotteet</h2>
    <div style="display: flex; justify-content: center; gap: 20px;">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product" onclick="showPopup(
                    '<?= htmlspecialchars($product['nimi']) ?>',
                    '<?= htmlspecialchars($product['kuvaus']) ?>',
                    '<?= htmlspecialchars($product['kuva']) ?>',
                    '<?= htmlspecialchars($product['hinta']) ?>',
                    '<?= htmlspecialchars($product['varastomäärä']) ?>'
                )">
                    <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>" style="width: 100%; height: auto; border-radius: 5px;">
                    <h4 style="color: gold;"><?= htmlspecialchars($product['nimi']) ?></h4>
                    <p><strong>Hinta:</strong> €<?= number_format($product['hinta'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ei uusia tuotteita saatavilla tällä hetkellä.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Popup modal -->
<div class="overlay" id="overlay" onclick="hidePopup()"></div>
<div class="popup" id="popup">
    <button type="button" style="position: absolute; top: 10px; right: 10px; background: white; color: black; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;" onclick="hidePopup()">×</button>
    <img id="popup-img" src="" alt="Tuotekuva">
    <h4 id="popup-title"></h4>
    <p id="popup-description"></p>
    <p><strong id="popup-price"></strong></p>
    <p id="popup-stock"></p>

    <!-- Add to Cart Icon -->
    <div class="icon">
        <img 
            src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" 
            alt="Lisää ostoskoriin" 
            onclick="addToCart()" 
            style="width: 40px; height: 40px; cursor: pointer; margin-top: 10px;">
    </div>
</div>

<script>
    function showPopup(title, description, imageUrl, price, stock) {
        document.getElementById('popup-title').textContent = title;
        document.getElementById('popup-description').textContent = description;
        document.getElementById('popup-img').src = imageUrl;
        document.getElementById('popup-price').textContent = "Hinta: €" + parseFloat(price).toFixed(2);
        document.getElementById('popup-stock').textContent = "Varastossa: " + stock + " kpl";
        document.getElementById('popup').classList.add('show');
        document.getElementById('overlay').classList.add('show');
    }

    function hidePopup() {
        document.getElementById('popup').classList.remove('show');
        document.getElementById('overlay').classList.remove('show');
    }

    function addToCart() {
        // Get data from popup
        const title = document.getElementById('popup-title').textContent;
        const price = document.getElementById('popup-price').textContent.replace('Hinta: €', '');
        const stock = document.getElementById('popup-stock').textContent.replace('Varastossa: ', '').replace(' kpl', '');
        const imageUrl = document.getElementById('popup-img').src;

        // Prepare product data
        const productData = {
            name: title.trim(),
            price: parseFloat(price.trim()),
            stock: parseInt(stock.trim(), 10),
            image: imageUrl
        };

        // Send product data to server
        fetch('lisaa-ostoskoriin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(productData), // Using productData here
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Tuote lisätty ostoskoriin!');
            } else {
                alert(data.message || 'Virhe lisättäessä tuotetta ostoskoriin.');
            }
        })
        .catch(error => {
            console.error('Virhe:', error);
            alert('Yhteysvirhe. Yritä myöhemmin uudelleen.');
        });
    }
</script>
