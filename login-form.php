<?php
session_start();

if (isset($_SESSION['message'])) {
  echo "<p style='color: red; text-align: center;'>" . $_SESSION['message'] . "</p>";
  unset($_SESSION['message']);
}

	//Include database connection details
	require_once('config.php');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration handling code here

	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Connect to mysql server
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysqli_select_db($link, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($link, $str) {
		$str = @trim($str);
		return mysqli_real_escape_string($link, $str);
	}
	
	//Sanitize the POST values
	$login = clean($link, $_POST['login']);
	$password = clean($link, $_POST['password']);
	
	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Login ID missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php?page=login-form");
		exit();
	}
	
	//Create query
	$qry="SELECT * FROM Members WHERE username='$login' AND password='".md5($_POST['password'])."'";
	$result=mysqli_query($link, $qry);
	
	//Check whether the query was successful or not
	if($result) {
		if(mysqli_num_rows($result) == 1) {
			//Login Successful
			session_regenerate_id();
			$member = mysqli_fetch_assoc($result);
			$_SESSION['SESS_MEMBER_ID'] = $member['member_id'];
			$_SESSION['SESS_FIRST_NAME'] = $member['firstname'];
			$_SESSION['SESS_LAST_NAME'] = $member['lastname'];
			$_SESSION['SESS_LOGIN'] = $member['username'];
			$_SESSION['SESS_EMAIL'] = $member['email'];
			$_SESSION['loggedin'] = true;
			session_write_close();
			$login_name = $_SESSION['SESS_LOGIN'];
			if ($login_name == 'admin') {
				header("location: admin-panel.php");
			}else {
				header("location: index.php?page=profiili");
				exit();
			}
		}else {
			$_SESSION['message'] = "Kirjautuminen epäonnistunut";
			header("location: index.php?page=login-form");
			echo "Kirjautuminen epäonnistunut";
			exit();
		}
	}else {
		die("Query failed");
	}
}
?>
<body>
<p>&nbsp;</p>
<form id="loginForm" name="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
  <table width="300" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <td width="112"><b><?= addslashes($current_lang['Login']); ?></b></td>
      <td width="188"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    <tr>
      <td><b><?= addslashes($current_lang['Password']); ?></b></td>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="<?= addslashes($current_lang['LogIn']); ?>" /></td>
    </tr>
  </table>
</form>
<br>
<div style="display: flex; justify-content: center;">
    <div style="text-align: center;">
        <p><b><?= addslashes($current_lang['noAccount']); ?></b></p>
        <p><a href="index.php?page=register-form"><?= addslashes($current_lang['PressHere']); ?></a><?= addslashes($current_lang['ToCreate']); ?></p>
    </div>
</div>
</body>





