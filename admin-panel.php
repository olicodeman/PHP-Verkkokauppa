<?php  
    require_once("auth.php");
    require_once("config.php");

    $login = $_SESSION['SESS_LOGIN'];

    if ($login !== 'admin') {
        header('location: index.php?page=error');
        exit();
    }

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysqli_select_db($link, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

	$qry = "SELECT * FROM tuotteet";
	$result = mysqli_query($link, $qry);
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
        <?php
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<table border="1" cellpadding="10" cellspacing="5" class="adminproductview">';
        echo '<tr><th>Tuote ID</th><th>Nimi</th><th>Kuvaus</th><th>Hinta</th><th>Varastomäärä</th><th>Toiminnot</th></tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nimi']) . '</td>';
            echo '<td>' . htmlspecialchars($row['kuvaus']) . '</td>';
            echo '<td>' . htmlspecialchars($row['hinta']) . ' €</td>';
            echo '<td>' . htmlspecialchars($row['varastomäärä']) . '</td>';
            echo '<td>';
            echo '<a href="edit-tuote.php?id=' . $row['id'] . '" class="admin-btn">Muokkaa</a> ';
            echo ' | ';
            echo '<a href="poista-tuote.php?id=' . $row['id'] . '" class="admin-btn" onclick="return confirm(\'Haluatko varmasti poistaa tämän tuotteen?\')">Poista</a>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Ei tuotteita saatavilla.</p>';
    }
    ?>
    <br>
        <a href="lisaa-tuote.php" class="edit-btn">Lisää tuotteita</a>
    </div>
</body>
</html>
