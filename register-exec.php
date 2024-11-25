<?php
	//Start session
	session_start();
	
	//Include database connection details
	require_once('config.php');
	
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
	$fname = clean($link, $_POST['fname']);
	$lname = clean($link, $_POST['lname']);
	$email = clean($link, $_POST['email']);
	$address = clean($link, $_POST['address']);
	$number = clean($link, $_POST['number']);
	$login = clean($link, $_POST['login']);
	$password = clean($link, $_POST['password']);
	$cpassword = clean($link, $_POST['cpassword']);
	
	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Etunimi puuttuu</p>';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Sukunimi puuttuu</p>';
		$errflag = true;
	}
	if($email == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Sähköposti puuttuu</p>';
		$errflag = true;
	}
	if($address == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Osoite puuttuu</p>';
		$errflag = true;
	}
	if($number == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Puhelinnumero puuttuu</p>';
		$errflag = true;
	}
	if($login == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Käyttäjätunnus puuttuu</p>';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Salasana puuttuu</p>';
		$errflag = true;
	}
	if($cpassword == '') {
		$errmsg_arr[] = '<p><b style="color: red;">*</b> Salasanan varmistus puuttuu</p>';
		$errflag = true;
	}
	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = '<p>Salansanat eivät ole samoja</p>';
		$errflag = true;
	}
	
	//Check for duplicate login ID
	if($login != '') {
		$qry = "SELECT * FROM Members WHERE username='$login'";
		$result = mysqli_query($link, $qry);
		if($result) {
			if(mysqli_num_rows($result) > 0) {
				$errmsg_arr[] = '<p style="text-align: center;">Tili tällä käyttäjätunnuksella on jo olemassa.<p>';
				$errflag = true;
			}
			@mysqli_free_result($result);
		}
		else {
			die("Query failed");
		}
	}
	
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: index.php?page=register-form");
		exit();
	}

	//Create INSERT query
	$qry = "INSERT INTO Members(firstname, lastname, email, address, phonenumber, username, password) VALUES('$fname','$lname','$email', '$address', '$number', '$login','".md5($_POST['password'])."')";
	$result = @mysqli_query($link, $qry);

	//Check whether the query was successful or not
	if($result) {
		header("location: index.php?page=register-success");
		exit();
	}else {
		die("Query failed");
	}

	
?>