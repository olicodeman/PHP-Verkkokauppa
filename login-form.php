<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Form</title>
<link href="css.css" rel="stylesheet"/>
<link href="loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<nav id="nav01"></nav>
<p>&nbsp;</p>
<form id="loginForm" name="loginForm" method="post" action="login-exec.php">
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <td width="112"><b>Login</b></td>
      <td width="188"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <td><b>Password</b></td>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Login" /></td>
    </tr>
  </table>
</form>
<br>
<div style="display: flex; justify-content: center; gap: 30px;">
    <div style="text-align: center;">
        <p><b>Not a user yet?</b></p>
        <p><a href="register-form.php">Click here</a> to create an account.</p>
    </div>

    <div style="text-align: center;">
        <p><b>Forgot your password?</b></p>
        <p>Reset it <a href="reset-password.php">here</a>.</p>
    </div>
</div>
<footer id="foot01"></footer>
<script src="script.js"></script>
</body>
</html>
