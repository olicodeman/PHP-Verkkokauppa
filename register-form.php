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
  <table width="300" border="0" align="center" cellpadding="5" cellspacing="5">
    <tr>
      <th>Etunimi</th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
      <th>Sukunimi</th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
  </tr>
  
    <tr>
      <th>Sähköpostiosoite</th>
      <td><input name="email" type="email" class="textfield" id="email" /></td>
      <th width="124">Käyttäjätunnus</th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    
    <tr>
      <th width="124">Osoite</th>
      <td width="168"><input name="address" type="text" class="textfield" id="login" /></td>
      <th width="124">Puhelinnumero</th>
      <td width="168"><input name="number" type="text" class="textfield" id="login" /></td>
    </tr>
    
    <tr>
      <th>Salasana</th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
      <th>Varmista salasana</th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="1"><input style="justify-content: center;" type="submit" name="Submit" value="Register" /></td>
    </tr>
  </table>
</form>
<br>
<p style="text-align: center;"><b>Onko tili jo olemassa?</b></p>
<p style="text-align: center;"><a href="index.php?page=login-form">Kirjaudu sisään</a>

