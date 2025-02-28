<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Start the session
session_start();
?>

<?php
if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
    echo '<ul class="err">';
    foreach($_SESSION['ERRMSG_ARR'] as $msg) {
        echo '<li>', $msg, '</li>'; 
    }
    echo '</ul>';
    unset($_SESSION['ERRMSG_ARR']);
}
?>
<style>
@media (max-width: 1200px) {
    form {
        max-width: 100%;
        margin-left: 0%;
    }

    /* Stack input fields by making them full-width */
    table {
        width: 100%;
    }

    table tr {
        display: block;
        width: 100%;
    }

    table th, 
    table td {
        display: block;
        width: 100%;
        text-align: left;
    }

    input {
        width: 350px;
    }
    input[type="submit"] {
        font-size: 14px;   /* Smaller text */
        padding: 8px;      /* Smaller padding */
        width: auto;       /* Let the button be smaller based on content */
        margin-top: 10px;  /* Ensure space above */
    }
}
</style>
<form id="loginForm" name="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
  <table width="300" border="0" align="center" cellpadding="5" cellspacing="5">
    <tr>
      <th><?= addslashes($current_lang['FirstName']); ?></th>
      <td><input name="fname" type="text" class="textfield" id="fname" /></td>
      <th><?= addslashes($current_lang['LastName']); ?></th>
      <td><input name="lname" type="text" class="textfield" id="lname" /></td>
  </tr>
  
    <tr>
      <th><?= addslashes($current_lang['Email']); ?></th>
      <td><input name="email" type="email" class="textfield" id="email" /></td>
      <th width="124"><?= addslashes($current_lang['User']); ?></th>
      <td width="168"><input name="login" type="text" class="textfield" id="login" /></td>
    </tr>
    
    <tr>
      <th width="124"><?= addslashes($current_lang['Address']); ?></th>
      <td width="168"><input name="address" type="text" class="textfield" id="address" /></td>
      <th width="124"><?= addslashes($current_lang['PhoneNmb']); ?></th>
      <td width="168"><input name="number" type="text" class="textfield" id="number" /></td>
    </tr>
    
    <tr>
      <th><?= addslashes($current_lang['Password']); ?></th>
      <td><input name="password" type="password" class="textfield" id="password" /></td>
      <th><?= addslashes($current_lang['CheckPassword']); ?></th>
      <td><input name="cpassword" type="password" class="textfield" id="cpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="1"><input style="justify-content: center;" type="submit" name="Submit" value="<?= addslashes($current_lang['register']); ?>" /></td>
    </tr>
  </table>
  <br>
<p style="text-align: center;"><b><?= addslashes($current_lang['YesAccount']); ?></b></p>
<p style="text-align: center;"><a href="index.php?page=login-form"><?= addslashes($current_lang['LogIn']); ?></a></p>
</form>

<?php
// Include database connection details
require_once('config.php');

// Registration handling code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Array to store validation errors
    $errmsg_arr = array();
    // Validation error flag
    $errflag = false;

    // Connect to mysql server
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

    // Select database
    $db = mysqli_select_db($link, DB_DATABASE);
    if (!$db) {
        die("Unable to select database");
    }

    // Function to sanitize values received from the form. Prevents SQL injection
    function clean($link, $str) {
        $str = @trim($str);
        return mysqli_real_escape_string($link, $str);
    }

    // Sanitize the POST values
    $fname = clean($link, $_POST['fname']);
    $lname = clean($link, $_POST['lname']);
    $email = clean($link, $_POST['email']);
    $address = clean($link, $_POST['address']);
    $number = clean($link, $_POST['number']);
    $login = clean($link, $_POST['login']);
    $password = clean($link, $_POST['password']);
    $cpassword = clean($link, $_POST['cpassword']);

    // Input Validations
    if($fname == '') {
        $errmsg_arr[] = '<p><b style="color: red;">*</b> Etunimi puuttuu</p>';
        $errflag = true;
    }
    if($lname == '') {
        $errmsg_arr[] = '<p><b style="color: red;">*</b> Sukunimi puuttuu</p>';
        $errflag = true;
    }
    if($email == '') {
        $errmsg_arr[] = '<p><b style="color: red;">*</b> SÃ¤hkÃ¶posti puuttuu</p>';
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
        $errmsg_arr[] = '<p><b style="color: red;">*</b> KÃ¤yttÃ¤jÃ¤tunnus puuttuu</p>';
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
        $errmsg_arr[] = '<p>Salasanat eivÃ¤t ole samoja</p>';
        $errflag = true;
    }

    // Check for duplicate login ID
    if($login != '') {
        $qry = "SELECT * FROM members WHERE username='$login'";
        $result = mysqli_query($link, $qry);
        if($result) {
            if(mysqli_num_rows($result) > 0) {
                $errmsg_arr[] = '<p style="text-align: center;">Tili tÃ¤llÃ¤ kÃ¤yttÃ¤jÃ¤tunnuksella on jo olemassa.<p>';
                $errflag = true;
            }
            @mysqli_free_result($result);
        }
        else {
            die("Query failed");
        }
    }

    // If there are input validations, redirect back to the registration form
    if($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        session_write_close();
        // Use header() for redirection
        header("location: index.php?page=register-form");
        exit();
    }

    // Create INSERT query
    $qry = "INSERT INTO members(firstname, lastname, email, address, phonenumber, username, password) VALUES('$fname','$lname','$email', '$address', '$number', '$login','".md5($_POST['password'])."')";
    $result = @mysqli_query($link, $qry);

    // Check whether the query was successful or not
    if($result) {
        header("location: index.php?page=register-success");
        exit();
    } else {
        die("Query failed");
    }
}

// End output buffering and flush the output
ob_end_flush();
?>
