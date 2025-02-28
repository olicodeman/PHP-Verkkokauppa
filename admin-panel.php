<?php
require_once("auth.php");
require_once("config.php");

//Varmistetaan ettÃ¤ ollaan kirjautuneena adminina
$login = $_SESSION['SESS_LOGIN'];

//Jos ei ole admin saadaan error
if ($login !== 'admin') {
    header('location: index.php?page=error');
    exit();
}

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
    die('Failed to connect to server: ' . mysql_error());
}

//Valitaan tietokanta
$db = mysqli_select_db($link, DB_DATABASE);
if (!$db) {
    die("Unable to select database");
}

//Valitaan tuotteen tiedot tietokannasta
$qry = "SELECT * FROM tuotteet";
$result = mysqli_query($link, $qry);

$qry = "SELECT id, nimi, varastomaara FROM tuotteet WHERE varastomaara = 3 OR varastomaara < 3 ORDER BY varastomaara ASC";
$result2 = mysqli_query($link, $qry);

$qry = "SELECT tt.product_id, 
               p.nimi, 
               SUM(tt.quantity) AS total_sold, 
               SUM(tt.quantity * p.hinta) AS total_revenue
        FROM tilaus_tuotteet tt
        JOIN tuotteet p ON tt.product_id = p.id
        GROUP BY tt.product_id
        ORDER BY total_sold DESC
        LIMIT 5";
$result3 = mysqli_query($link, $qry);

$qry = "SELECT c.nimi, SUM(tt.quantity) AS tilausmaara
    FROM tilaus_tuotteet tt JOIN tuotteet p ON tt.product_id = p.id
    JOIN tuote_kategoria tk ON p.id = tk.tuote_id
    JOIN kategoriat c ON tk.kategoria_id = c.id
    GROUP BY c.nimi
    ORDER BY tilausmaara DESC";
$result4 = mysqli_query($link, $qry);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator panel</title>
    <link href="style.css" rel="stylesheet">
</head>
<style>
    .analyticsPopup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background-color: white;
        border-radius: 15px;
        text-align: center;
        justify-content: center;
        padding: 20px;
        width: 800px;
        height: 600px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        color: darkslateblue;
        overflow-y: auto;
    }

    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .analytictables {
        width: 70%;
        border-collapse: collapse;
        font-size: 16px;
        text-align: center;
        margin: 20px auto;
    }

    .analytictables th {
        background-color: darkslateblue;
        color: white;
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .reports-btn {
        background-color: darkslateblue;
        width: 175px;
        color: white;
        padding: 10px;
        border-radius: 15px;
        font-weight: bold;
        overflow: visible;
        position: relative;
        z-index: 1;
        display: inline-block;
        transition: background 0.2s ease-in, transform 0.2s ease-in;
        white-space: nowrap;
    }

    .reports-btn:hover {
        background-color: lightseagreen;
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .adminproductview {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }

    @media (max-width: 480px) {

        .adminproductview th,
        .adminproductview td {
            font-size: 12px;
            padding: 4px;
        }

        .admin-btn {
            padding: 2px;
        }

        .analyticsPopup {
            padding: 10px;
            width: 300px;
            height: 500px;
        }

        .analytictables th {
            padding: 6px 10px;
        }

        .analytictables {
            font-size: 12px;
            margin: 10px auto;
        }
    }
</style>

<body>
    <div style="text-align: center; color: white;">
        <h1>YllÃ¤pitÃ¤jÃ¤ paneli</h1>
        <a href="index.php?page=logout">Kirjaudu ulos</a> | <a href="index.php?page=profiili">Verkkokauppa</a>
        <br><br>
        <a onclick="showAnalytics()" style="cursor: pointer;" class="reports-btn">Raportit ja analytiikka</a>
        <h3>Muokkaa tai poista tuotteita verkkokaupasta</h3>
        <?php

        //NÃ¤ytetÃ¤Ã¤n tuotteen tiedot taulukossa. Poisto ja muokkaus mahdollisuus.
        if ($result && mysqli_num_rows($result) > 0) {
            echo '<table border="1" cellpadding="10" cellspacing="5" class="adminproductview">';
            echo '<tr><th>Tuote ID</th><th>Nimi</th><th>Kuvaus</th><th>Hinta</th><th>Varastomaara</th><th>Toiminnot</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nimi']) . '</td>';
                echo '<td>' . htmlspecialchars($row['kuvaus']) . '</td>';
                echo '<td>' . htmlspecialchars($row['hinta']) . ' â‚¬</td>';
                echo '<td>' . htmlspecialchars($row['varastomaara']) . '</td>';
                echo '<td>';
                echo '<a href="edit-tuote.php?id=' . $row['id'] . '" class="admin-btn">Muokkaa</a> ';
                echo ' | ';
                echo '<a href="poista-tuote.php?id=' . $row['id'] . '" class="admin-btn" onclick="return confirm(\'Haluatko varmasti poistaa tÃ¤mÃ¤n tuotteen?\')">Poista</a>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>Ei tuotteita saatavilla.</p>';
        }
        ?>
        <br>
        <!-- Tuotteen lisÃ¤ys nappi -->
        <a style="margin-bottom: 20px;" href="lisaa-tuote.php" class="edit-btn">LisÃ¤Ã¤ tuotteita</a>
    </div>

    <!-- Myynti ja raportti analytiikkan tarkistus -->
    <div id="overlay">
        <div id="analytics" class="analyticsPopup">
            <a style="color: white; background-color: darkslateblue;" class="close-btn" onclick="closeAnalytics()">Ã—</a>
            <h2>Myynti raportit ja analytiikka</h2>
            <h3>MyydyimmÃ¤t tuotteet</h3>
            <?php if ($result3 && mysqli_num_rows($result3) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="5" class="analytictables">
                    <tr>
                        <th>ID</th>
                        <th>Nimi</th>
                        <th>Myyty mÃ¤Ã¤rÃ¤</th>
                        <th>Kokonaismyynti</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result3)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_id']) ?></td>
                            <td><?= htmlspecialchars($row['nimi']) ?></td>
                            <td><?= htmlspecialchars($row['total_sold']) ?></td>
                            <td><?= number_format(htmlspecialchars($row['total_revenue']), 2) ?> â‚¬</td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>
            <h3>TilausmÃ¤Ã¤rÃ¤ per tuotekatgoria</h3>
            <?php if ($result4 && mysqli_num_rows($result4) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="5" class="analytictables">
                    <tr>
                        <th>Nimi</th>
                        <th>TilausmÃ¤Ã¤rÃ¤</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result4)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nimi']) ?></td>
                            <td><?= htmlspecialchars($row['tilausmaara']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>
            <h3>Alhaisen varastosaldon tuotteet</h3>
            <?php if ($result2 && mysqli_num_rows($result2) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="5" class="analytictables">
                    <tr>
                        <th>ID</th>
                        <th>Nimi</th>
                        <th>Varastomaara</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result2)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['nimi']) ?></td>
                            <td style="color: red; font-weight: bold;"><?= htmlspecialchars($row['varastomaara']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>Ei alhaisen varaston tuotteita saatavilla.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function showAnalytics() {
            const report = document.getElementById('analytics');
            const overlay = document.getElementById('overlay');
            report.style.display = 'block';
            overlay.style.display = 'block';

            overlay.addEventListener('click', function () {
                report.style.display = 'none';
                overlay.style.display = 'none';
            });

            report.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent event from bubbling to the overlay
            });
        }

        function closeAnalytics() {
            const report = document.getElementById('analytics');
            const overlay = document.getElementById('overlay');
            report.style.display = 'none';
            overlay.style.display = 'none';
        }
    </script>
</body>

</html>