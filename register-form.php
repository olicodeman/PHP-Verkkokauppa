<?php
	session_start();
?>
<?php
	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
		echo '<ul class="err">';
		foreach($_SESSION['ERRMSG_ARR'] as $msg) {
			echo '<li>',$msg,'</li>'; 
		}
		echo '</ul>';
		unset($_SESSION['ERRMSG_ARR']);
	}
?>
<form id="loginForm" name="loginForm" method="post" action="index.php?page=register-exec">
  <table width="300" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <th>Etunimi</th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
    </tr>
    <tr>
      <th>Sukunimi</th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
    </tr>
    <tr>
      <th>Sähköpostiosoite</th>
      <td><input name="email" type="email" class="textfield" id="email" /></td>
    </tr>
    <tr>
      <th width="124">Käyttäjätunnus</th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th width="124">Osoite</th>
      <td width="168"><input name="address" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th width="124">Puhelinnumero</th>
      <td width="168"><input name="number" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <th>Password</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <th>Confirm Password </th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Register" /></td>
    </tr>
  </table>
  <br>
<p style="text-align: center;"><b>Onko tili jo olemassa?</b></p>
<p style="text-align: center;"><a href="index.php?page=login-form">Kirjaudu sisään</a>
</form>

