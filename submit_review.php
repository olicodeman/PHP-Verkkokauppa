<?php

session_start();
require_once('config.php');
require 'lang.php';

$current_lang = $lang[$_SESSION['lang']] ?? $lang['en'];  // Default to English

// Yhdistetään tietokantaan
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Tietokantayhteys epäonnistui: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan lomaketiedot
    $tuote_id = intval($_POST['tuote_id']); // Ensure tuote_id is passed
    $nimi = $conn->real_escape_string(trim($_POST['nimi']));
    $sähköposti = $conn->real_escape_string(trim($_POST['sähköposti']));
    $otsikko = $conn->real_escape_string(trim($_POST['otsikko']));
    $kommentti = $conn->real_escape_string(trim($_POST['kommentti']));
    $tähtiarvostelu = intval($_POST['tähtiarvostelu']);
    $kieli = isset($_POST['kieli']) && ($_POST['kieli'] === 'fi' || $_POST['kieli'] === 'en') ? $_POST['kieli'] : 'fi';

error_reporting(E_ALL);
ini_set('display_errors', 1);

    // Varmistetaan, että kaikki tiedot ovat olemassa
    if (empty($nimi) || empty($sähköposti) || empty($otsikko) || empty($kommentti) || empty($tähtiarvostelu) || empty($tuote_id)) {
        $message = "<div class='message error'>Täytä kaikki kentät.</div>";
    } else {
        // Tarkista, että tuote_id löytyy tuotteet-taulusta
        $checkProduct = $conn->prepare("SELECT COUNT(*) FROM tuotteet WHERE id = ?");
        $checkProduct->bind_param('i', $tuote_id);
        $checkProduct->execute();
        $checkProduct->bind_result($productExists);
        $checkProduct->fetch();
        $checkProduct->close();

        if ($productExists === 0) {
            $message = "<div class='message error'>Virhe: Tuote ei ole olemassa.</div>";
        } else {
            // Tallennetaan arvostelu tietokantaan
            $sql = "INSERT INTO arvostelut (tuote_id, nimi, sähköposti, otsikko, kommentti, tähtiarvostelu, kieli) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('issssis', $tuote_id, $nimi, $sähköposti, $otsikko, $kommentti, $tähtiarvostelu, $kieli);
                if ($stmt->execute()) {
                    $message = "<div class='message success'>{$current_lang['review_success']}</div>";
                } else {
                    $message = "<div class='message error'>{$current_lang['review_error']} " . $stmt->error . "</div>";
                }
                $stmt->close();


            } else {
                $message = "<div class='message error'>{$current_lang['db_query_error']} " . $conn->error . "</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $current_lang['SavedReview']; ?></title>
    <style>
        /* Basic Styling */
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://live.staticflickr.com/8230/8410882716_2604e5af6b_b.jpg');
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .center-align {
            text-align: center;
        }

        .message {
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            font-size: 18px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: rgb(45, 45, 102);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            border: 2px solid white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
            border: 2px solid #ffffff;
        }


        form {
            background-color: rgb(45, 45, 102);
            max-width: 50%;
            text-align: center;
            border-radius: 2%;
            border-style: outset;
            border-color: whitesmoke;
            padding-top: 25px;
            margin-top: 75px;
            padding-bottom: 30px;
        }

        .form-buttons a {
            margin-top: 20px;
        }

        .exciting-text {
            font-size: 22px;
            /* Larger text */
            font-weight: bold;
            /* Bold for better visibility */
            color: white;
            /* Bright color */
            text-transform: uppercase;
            /* Make it stand out */
            text-decoration: none;
            /* Remove underline */
            padding: 10px 15px;
            /* Spacing around text */
            background: linear-gradient(45deg, rgbargb(7, 49, 94), rgb(24, 1, 173));
            /* Vibrant gradient */
            border-radius: 8px;
            /* Smooth corners */
            box-shadow: 0 0 10px rgba(218, 214, 255, 0.8);
            /* Soft glowing effect */
            transition: transform 0.2s, box-shadow 0.3s;
        }

        .exciting-text:hover {
            transform: scale(1.1);
            /* Slight zoom effect */
            box-shadow: 0 0 20px rgb(255, 255, 255);
            /* Stronger glow */
        }
    </style>
</head>

<body>

    <form method="POST" action="">
        <div class="center-align">
            <!-- Onnistunut tai virheviesti -->
            <?php if (!empty($message))
                echo $message; ?>
            < <div class="center-align">
                <a class="exciting-text" href="index.php?page=arvosteluSivu">
                    <?= $current_lang['read_reviews']; ?></a>
        </div>
        <br><br>

        <a href="index.php?page=etusivu&lang=<?= $_SESSION['lang']; ?>" class="exciting-text">
            <?= $current_lang['homepage']; ?>
        </a>

        </div>
        </div>
    </form>

</body>

</html>