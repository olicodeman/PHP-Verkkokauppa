<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisää tuote</title>

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
            background-color: #4CAF50;
            /* Vihreä väri valitulle napille */
            color: white;
        }

        #uusi-kategoria {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            width: 200px;
        }

        #uusi-kategoria input[type="text"] {
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Lisää tuote</h1>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $host = 'localhost';
            $db = 'verkkokauppadb';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Helpottaa virheiden havaitsemisessa
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //vähentää tarpeetonta käsittelyä
                PDO::ATTR_EMULATE_PREPARES => false, //suojataan sql-injektioilta
            ];

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
                $nimi = $_POST['nimi'];
                $kuvaus = $_POST['kuvaus'];
                $hinta = $_POST['hinta'];
                $kategoriat = $_POST['kategoriat'];

                // Lisää tuote
                $stmt = $pdo->prepare("INSERT INTO tuotteet (nimi, kuvaus, hinta) VALUES (?, ?, ?)");
                $stmt->execute([$nimi, $kuvaus, $hinta]);

                $tuote_id = $pdo->lastInsertId();

                // Lisää tuote_kategoria-linkit
                foreach ($kategoriat as $kategoria_id) {
                    $stmt = $pdo->prepare("INSERT INTO tuote_kategoria (tuote_id, kategoria_id) VALUES (?, ?)");
                    $stmt->execute([$tuote_id, $kategoria_id]);
                }

                echo '<p class="success">Lisäsit tuotteen onnistuneesti!</p>';
            } catch (PDOException $e) {
                echo '<p class="error">Virhe: ' . $e->getMessage() . '</p>';
            }
        }
        ?>
        <form action="" method="POST">

            <button type="submit">Lisää Tuote</button>

            <label for="nimi">Tuotteen nimi:</label>
            <input type="text" id="nimi" name="nimi" required>

            <label for="kuvaus">Tuotteen kuvaus:</label>
            <textarea id="kuvaus" name="kuvaus" rows="4" required></textarea>

            <label for="hinta">Tuotteen Hinta (€):</label>
            <input type="number" id="hinta" name="hinta" step="0.01" required>

            <h2>Valitse kategoria</h2>
            <div id="kategoriat">
                <?php foreach ($kategoriat as $kategoria): ?>
                    <button type="button" class="kategoria-nappi" data-id="<?= $kategoria['id'] ?>"
                        onclick="toggleSelection(this)">
                        <?= $kategoria['nimi'] ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div id="uusi-kategoria">
                <label for="uusi_kategoria">Lisää uusi kategoria</label>
                <input type="text" id="uusi_kategoria" name="uusi_kategoria" placeholder="Kategorian nimi">
                <button type="button" onclick="addCategory()">Lisää kategoria</button>
            </div>
            </select>
        </form>
    </div>




</body>

</html>