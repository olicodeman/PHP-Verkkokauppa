<?php  
    require_once("auth.php");
    require_once("config.php");

    $login = $_SESSION['SESS_LOGIN'];

    if ($login !== 'admin') {
        header('location: index.php?page=error');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator panel</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div style="text-align: center; color: white;">
        <h1>Administrator Panel</h1>
        <a href="index.php?page=logout">Kirjaudu ulos</a>
        <h3>Muokkaa tai poista tuotteita verkkokaupasta</h3>
        <a href="lisaa-tuote.php" class="edit-btn">Lisää tuotteita</a>
    </div>
</body>
</html>
