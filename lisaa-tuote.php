<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lisää tuote</title>
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
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
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
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

            <label for="kategoriat">Valitse kategoria(t):</label>
            <select id="kategoriat" name="kategoriat[]" multiple required>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM kategoriat");
                    $kategoriat = $stmt->fetchAll();
                    foreach ($kategoriat as $kategoria) {
                        echo '<option value="' . $kategoria['id'] . '">' . $kategoria['nimi'] . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<p class="error">Virhe: ' . $e->getMessage() . '</p>';
                }
                ?>
            </select>
        </form>
    </div>
</body>
</html>
