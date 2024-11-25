<?php
	require_once('auth.php');
?>

<title>Profiili</title>
</head>
<body>
<nav id="nav01"></nav>
<br>
<h1>Tervetuloa <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<a href="logout.php">Logout</a>
<h2>Sinun tiedot:</h2>
<p>Etunimi: <?php echo $_SESSION['SESS_FIRST_NAME'];?></p>
<p>Sukunimi: <?php echo $_SESSION['SESS_LAST_NAME'];?></p>
<p>Sähköposti: <?php echo $_SESSION['SESS_EMAIL'];?></p>
<p>Käyttäjänimi: <?php echo $_SESSION['SESS_LOGIN'];?></p>
</body>
</html>
