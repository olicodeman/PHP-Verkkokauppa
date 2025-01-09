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

    try {
        $categoryStmt = $pdo->prepare("SELECT id, nimi FROM kategoriat");
        $categoryStmt->execute();
        $categories = $categoryStmt->fetchAll();
    } catch (PDOException $e) {
        echo '<p class="error">Error fetching categories: ' . $e->getMessage() . '</p>';
        $categories = [];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT 
                t.id, t.nimi, t.kuvaus, t.kuva, t.hinta, t.varastomäärä, 
                COALESCE(GROUP_CONCAT(k.nimi SEPARATOR ','), '') AS categories
            FROM tuotteet t
            LEFT JOIN tuote_kategoria tk ON t.id = tk.tuote_id
            LEFT JOIN kategoriat k ON tk.kategoria_id = k.id
            GROUP BY t.id
        ");
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
    transition: transform 0.3s, background-color 0.3s;
    padding: 20px; /* Lisää vähän tilaa ympärille */
}

        .product:hover {
            transform: scale(1.05);
            background-color: darkcyan;
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
    <h1 style="text-align: center;">Tervetuloa Kitchen Gadget tuote sivulle! Katsaise tuotteita ja osta!</h1>
    <br>
    <div class="search-bar-container">
        <input type="text" id="searchInput" class="search-bar" placeholder="Etsi tuotteita...">
        <button class="search-btn" onclick="searchProduct()">Search</button>
    </div>

    <div class="category-container">
    <select id="categorySelect" class="category-select" onchange="filterByCategory()">
        <option value="all">Kaikki kategoriat</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['nimi']) ?>"><?= htmlspecialchars($category['nimi']) ?></option>
        <?php endforeach; ?>
    </select>
</div>

    <form>
    <!-- Tuoteruudukko -->
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product" 
            data-id="<?= htmlspecialchars($product['id']) ?>" 
            data-categories="<?= htmlspecialchars($product['categories']) ?>" 
            onclick="showPopup('<?= htmlspecialchars($product['id']) ?>', '<?= htmlspecialchars($product['nimi']) ?>', '<?= htmlspecialchars($product['kuvaus']) ?>', '<?= htmlspecialchars($product['kuva']) ?>', '<?= htmlspecialchars($product['hinta']) ?>', '<?= htmlspecialchars($product['varastomäärä']) ?>')">
            <img src="<?= htmlspecialchars($product['kuva']) ?>" alt="<?= htmlspecialchars($product['nimi']) ?>">
            <p style="color: gold;" class="name"><?= htmlspecialchars($product['nimi']) ?></p>
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
</div>

<script>
  // Show the product popup
  // Show the product popup
function showPopup(id, title, description, imageUrl, price, stock) {
    // Set the popup details
    document.getElementById('popup-title').textContent = title;
    document.getElementById('popup-description').textContent = description;
    document.getElementById('popup-img').src = imageUrl;
    document.getElementById('popup-price').textContent = "Hinta: €" + parseFloat(price).toFixed(2);
    document.getElementById('popup-varastomaara').textContent = "Varastossa: " + stock + " kpl";

    // Set the product ID for later use (for adding to cart)
    document.getElementById('popup').setAttribute('data-product-id', id);

    // Show the popup and the overlay
    document.getElementById('popup').classList.add('show');
    document.getElementById('overlay').classList.add('show');
}


  // Hide the popup when clicked
  // Hide the popup when clicked
function hidePopup(event) {
    if (event) {
        event.preventDefault(); 
    }
    document.getElementById('popup').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
}


  // Add product to cart from popup
  function addToCartFromPopup() {
    // Get data from popup
    const title = document.getElementById('popup-title').textContent;
    const price = document.getElementById('popup-price').textContent.replace('Hinta: €', '');
    const stock = document.getElementById('popup-varastomaara').textContent.replace('Varastossa: ', '').replace(' kpl', '');
    const imageUrl = document.getElementById('popup-img').src;
    const productID = document.getElementById('popup').getAttribute('data-product-id');

    // Prepare product data
    const productData = {
        title: title.trim(),
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
        body: JSON.stringify({
            id: productID,
            name: title,
            price: parseFloat(price),
            stock: parseInt(stock),
            image: imageUrl  // Include image URL in the request
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Tuote lisätty ostoskoriin!');
        } else {
            alert('Ennen ostoskoriin lisäämistä, kirjaudu sisään.' + data.message);
        }
    })
    .catch(error => {
        console.error('Virhe:', error);
        alert('Yhteysvirhe. Yritä myöhemmin uudelleen.');
    });
}

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
    </div>
  </form>
</body>
</html>