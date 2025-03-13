<?php
include("config.php");
include("auth.php");

$login = $_SESSION['SESS_LOGIN'];
// Admin tarkistus
if ($login !== 'admin') {
    header('location: index.php?page=error');
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "Virheellinen tuote ID";
    exit();
}
$tuoteID = (intval($_GET["id"]));

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Set character set to UTF-8
mysqli_set_charset($link, 'utf8');

$stmt = $link->prepare("SELECT * FROM tuotteet WHERE id = ?");
$stmt->bind_param('i', $tuoteID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result->num_rows === 0) {
    echo "User not found.";
    $stmt->close();
    $link->close();
    exit;
}
$tuote = $result->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuotteen muokkaus</title>
    <link href="style.css" rel="stylesheet">
</head>
<style>
    @media (max-width: 480px) {
        input {
            width: 150px;
        }
    }
</style>

<body>
    <!-- Tuotteen muokkaus mahdollisuus -->
    <div style="text-align: center; color: white;">
        <h1>Tuotteen muokkaus</h1>
        <a href="admin-panel.php">Takaisin</a> | <a href="index.php?page=logout">Kirjaudu ulos</a>
    </div>
    <form method="POST" enctype="multipart/form-data" action="edit-tuote.php?id=<?php echo htmlspecialchars($tuote['id']); ?>">
        <label for="name"><b>Nimi: </b></label>
        <input type="text" size="30" name="name" value="<?php echo htmlspecialchars($tuote['nimi']); ?>" required>
        <label for="kuvaus"><b>Kuvaus: </b></label>
        <input type="text" size="30" name="kuvaus" value="<?php echo htmlspecialchars($tuote['kuvaus']); ?>" required>
        <label for="hinta"><b>Hinta: </b></label>
        <input type="text" size="30" name="hinta" value="<?php echo htmlspecialchars($tuote['hinta']); ?>" required>
        <label for="varasto"><b>Varastomäärä: </b></label>
        <input type="text" size="30" name="varasto" value="<?php echo htmlspecialchars($tuote['varastomäärä']); ?>" required>
        <label for="image"><b>Tuotteen kuva: </b></label>
        <input type="file" id="image" name="image" accept="image/*">
        <br><br>
        <div style="padding-bottom: 10px;">
            <input style="border: none;" class="admin-btn" type="submit" value="Tallenna muutokset">
        </div>
    </form>

    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <?php
    // Päivitetään muutokset tietokantaan
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newName = test_input($_POST["name"]);
        $newKuvaus = test_input($_POST["kuvaus"]);
        $newHinta = test_input($_POST["hinta"]);
        $newMaara = test_input($_POST["varasto"]);

        $isUpdated = false;

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        // Validate image extension
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageExtension, $validExtensions)) {
            // Create upload directory if not exists
            $uploadDir = 'kuvat/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Sanitize image name and create path
            $imageName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $imageName);
            $imagePath = $uploadDir . $imageName;

            // Move uploaded file to target directory
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                $isUpdated = true;
                $stmt = $link->prepare("UPDATE tuotteet SET nimi = ?, kuvaus = ?, hinta = ?, varastomäärä = ?, kuva = ? WHERE id = ?");
                $stmt->bind_param('ssdiss', $newName, $newKuvaus, $newHinta, $newMaara, $imagePath, $tuoteID);
            }
        }
    }

    // If no image was uploaded, update the product without changing the image
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        if ($newName !== $tuote['nimi'] || $newKuvaus !== $tuote['kuvaus'] || floatval($newHinta) !== floatval($tuote['hinta']) || intval($newMaara) !== intval($tuote['varastomäärä'])) {
            $isUpdated = true;
            $stmt = $link->prepare("UPDATE tuotteet SET nimi = ?, kuvaus = ?, hinta = ?, varastomäärä = ? WHERE id = ?");
            $stmt->bind_param('ssdii', $newName, $newKuvaus, $newHinta, $newMaara, $tuoteID);
        }
    }

    // Check if any product data was actually changed
    if ($isUpdated) {
        if ($stmt->execute()) {
            $_SESSION['message'] = "<p style='text-align: center;'><b style='color: green;'>Tuotteen tiedot päivitetty onnistuneesti!</b></p>";
        } else {
            $_SESSION['message'] = "<p style='text-align: center;'><b style='color: red;'>Virhe päivitettäessä tuotteen tietoja.</b></p>";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "<p style='text-align: center;'><b style='color: red;'>Vaihda tietoja tuotteen päivittämiseen.</b></p>";
    }

    header("Location: edit-tuote.php?id=" . $tuoteID);
    exit();
}

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>
</body>

</html>
