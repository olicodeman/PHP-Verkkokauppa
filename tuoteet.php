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

    // Haetaan tuotteet tietokannasta
    try {
        $stmt = $pdo->prepare("SELECT id, nimi, kuvaus, kuva, hinta, varastomäärä FROM tuotteet");
        $stmt->execute();
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<p class="error">Error fetching products: ' . $e->getMessage() . '</p>';
        $products = [];
    }
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
    transition: transform 0.2s ease-in-out;
    padding: 20px; /* Lisää vähän tilaa ympärille */
}

        .product:hover {
            transform: scale(1.05);
        }

        .product img {
    width: 100%;
    height: auto;
    object-fit: cover; /* Varmistaa, että kuva täyttää alueen ilman venyttämistä */
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
            background: rgb(45, 45, 102) ;
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
    </style>
</head>
<script>   
    function showDetails (name, description) {
        document.getElementById('product-details').innerHTML = `
        <h2>${name}</h2>
        <p>${description}</p>
        `;
    }
</script>
<body>
    <h1>Tervetuloa Kitchen Gadget tuote sivulle! Katsaise tuotteita ja osta!</h1>

    <form>
    <!-- Tuoteruudukko -->
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product" onclick="showPopup('<?= htmlspecialchars($product['nimi']) ?>', '<?= htmlspecialchars($product['kuvaus']) ?>', '<?= htmlspecialchars($product['kuva']) ?>', '<?= htmlspecialchars($product['hinta']) ?>', '<?= htmlspecialchars($product['varastomäärä']) ?>')">
                <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>">
                <p class="price">€<?= number_format($product['hinta'], 2) ?></p>

            </div>
        <?php endforeach; ?>
    </div>
</form>


    <!-- Tummennettu tausta -->
    <div class="overlay" id="overlay" onclick="hidePopup()"></div>
    <div class="popup" id="popup">
        <button type="button" class="close-btn" onclick="hidePopup(event)">×</button>
        <img id="popup-img" src="" alt="Tuotteen kuva">
        <h2 id="popup-title"></h2>
        <p id="popup-description"></p>
        <p id="popup-price"></p>
        <p id="popup-varastomaara"></p>

        
    <!-- Ostoskoriin lisääminen -->
        <div class="icon">
    <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" 
         alt="Lisää ostoskoriin" 
         onclick="addToCartFromPopup()">
</div>
    
 

    
    <!-- Script popupin toiminnalle, piilottaa ja näyttää-->
<script>
  function showPopup(title, description, imageUrl, price, varastomaara) {
    document.getElementById('popup-title').textContent = title;
    document.getElementById('popup-description').textContent = description;
    document.getElementById('popup-img').src = imageUrl;
    document.getElementById('popup-price').textContent = "Hinta: €" + parseFloat(price).toFixed(2);
    document.getElementById('popup-varastomaara').textContent = "Varastossa: " + varastomaara + " kpl";
    document.getElementById('popup').classList.add('show');
    document.getElementById('overlay').classList.add('show');
}

        //Estetään se ettei sivu ohjaannu etusivulle, kun suljetaan pop-up
    function hidePopup(event) {
        if(event) {
            event.preventDefault(); 
        }
      document.getElementById('popup').classList.remove  ('show');
      document.getElementById('overlay').classList.remove  ('show');
    }
    //ostoskoriin lisäämis script
    function addToCartFromPopup() {
    // Hae tiedot pop-upista
    const title = document.getElementById('popup-title').textContent;
    const price = document.getElementById('popup-price').textContent.replace('Hinta: €', '');
    const stock = document.getElementById('popup-varastomaara').textContent.replace('Varastossa: ', '').replace(' kpl', '');

    // Lähetä tiedot palvelimelle
    fetch('lisaa-ostoskoriin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: title,
            price: parseFloat(price),
            stock: parseInt(stock),
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tuote lisätty ostoskoriin!');
        } else {
            alert('Virhe lisättäessä tuotetta ostoskoriin.');
        }
    })
    .catch(error => {
        console.error('Virhe:', error);
        alert('Yhteysvirhe. Yritä myöhemmin uudelleen.');
    });
}

</script>
    </div>
  </form>
</body>
</html>