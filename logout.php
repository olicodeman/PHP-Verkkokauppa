<?php
	//Start session
	session_start();
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
?>

<body>
<h1>Logout </h1>
<p align="center">&nbsp;</p>
<h4 align="center" class="err">Sinut on kirjauduttu ulos.</h4>
<p align="center">Kirjaudu sisään painamalla <a href="index.php?page=login-form">tästä</a></p>
</body>
</html>