<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container">
<h1>Lisää tuote</h1>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //tietokannan asetukset
    $host = 'localhost';
    $db = 'tuotteet';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try{
        $pdo = new PDO($dsn, $user, $pass, $options);
        $nimi = $_POST['nimi'];
        $kuvaus = $_POST['kuvaus'];
        $hinta = $_POST['hinta'];
        $kategoriat = $_POST['kategoriat'];

        //Lisää tuote
        $stmt = $pdo->prepare ("INSERT INTO tuotteet (nimi, kuvaus, hinta) VALUES (?,?,?)");
        $stmt->execute([$nimi, $kuvaus,$hinta]);
        
        $tuote_id = $pdo->lastInsertId();
        
        //lisätään tuote_kategorisa_linkit
        foreach($kategoriat as $kategoria_id){
            $stmt =$pdo->prepare("INSERT INTO tuote_kategoria (tuote_id, kategoria_id)VALUES (?,?");
            $stmt->execute([$tuote_id, $kategoria_id]);
    }
    echo '<p class="success">Lisäsit tuotteen onnistuneesti!</p>';
}
}
?>
<form action="" method="POST">

<label for="nimi">Tuotteen nimi:</label>
<input type="text" id="nimi" name="nimi" required>


<label for="kuvaus">Tuotteen kuvaus:</label>
<input id="kuvaus" name="kuvaus" rows="4" required>


<label for="hinta">Tuotteen Hinta (€):</label>
<input type="number" id="hinta" name="hinta" step="0.01" required>
 

<label for="kategoriat">Valitse kategoria(T):</label>
<input id="kategoria" name="kategoriat[]" required>
<?php
//haetaan kategoriat tietokannasta

try{
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
<button type="submit">Lisää Tuote</button>
</form>
    </div>
</body>
</html>