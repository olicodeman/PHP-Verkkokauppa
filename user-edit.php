<?php
    include("config.php");
    include("auth.php");

    $fname = $_SESSION['SESS_FIRST_NAME'];
    $lname = $_SESSION['SESS_LAST_NAME'];
    $username = $_SESSION['SESS_LOGIN'];
	$email = $_SESSION['SESS_EMAIL'];
    $userID = $_SESSION['SESS_MEMBER_ID'];

    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

    if(!$link) {
        die('Failed to connect to server: ' . mysqli_connect_error());
    }

    $db = mysqli_select_db($link, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

	$qry = "SELECT address, phonenumber FROM members WHERE member_id = " . intval($_SESSION['SESS_MEMBER_ID']);
    $result = mysqli_query($link, $qry);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $address = $row['address'];
        $phone = $row['phonenumber'];
    }

	
?>

<h1 style="text-align:center;">Käyttäjätietosi</h1>
<p style="text-align: center;">
    <a href="index.php?page=profiili" style="text-decoration: none;">Takaisin profiiliin</a> | 
    <a href="index.php?page=logout" style="text-decoration: none;">Kirjaudu ulos</a>
</p>
<h4 style="text-align:center; color: white;">Tällä sivulla voit päivittää omat käyttäjätietosi.</h4>

<form method="POST" class="edit-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
    <div style="margin-bottom: 15px;">
        <label for="fname"><b>Etunimi: </b></label>
        <input class="edit-form-input" type="text" name="fname" value="<?php echo htmlspecialchars($fname);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="lname"><b>Sukunimi: </b></label>
        <input class="edit-form-input" type="text" name="lname" value="<?php echo htmlspecialchars($lname);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="email"><b>Sähköposti: </b></label>
        <input class="edit-form-input" type="email" name="email" value="<?php echo htmlspecialchars($email);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="username"><b>Käyttäjänimi: </b></label>
        <input class="edit-form-input" type="text" name="username" value="<?php echo htmlspecialchars($username);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="address"><b>Osoite: </b></label>
        <input class="edit-form-input" type="text" name="address" value="<?php echo htmlspecialchars($address);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="phone"><b>Puhelinnumero: </b></label>
        <input class="edit-form-input" type="text" name="phone" value="<?php echo htmlspecialchars($phone);?>" required>
    </div>

    <div style="text-align: center;">
        <input class="edit-btn" type="submit" value="Tallenna muutokset" name="sendData" style="background-color: #4CAF50; border: none;">
    </div>
</form>

<form method="POST" class="edit-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
    <label for="pwd"><b>Syötä uusi salasana: </b></label>
    <input class="edit-form-input" type="password" name="pwd">
    <br><br>
    <input class="edit-btn" type="submit" value="Vaihda salasana" name="pwdChange" style="background-color: #4CAF50; border: none;">
</form>


<?php
    if (isset($_POST['sendData']) && !isset($_POST['pwdChange'])) {
        $newFname = test_input($_POST['fname']);
        $newLname = test_input($_POST['lname']);
        $newEmail = test_input($_POST['email']);
        $newUsername = test_input($_POST['username']);
        $newAddress = test_input($_POST['address']);
        $newPhone = test_input($_POST['phone']);


        $oldData = array($fname, $lname, $email, $address, $phone, $username);
        $newData = array($newFname, $newLname, $newEmail, $newAddress, $newPhone, $newUsername);

        if ($newData == $oldData) {
            echo "<p style='text-align: center; color: red;'>Päivitä tiedot vaihtamalla niitä</p>";
        } else {
            $qry = "UPDATE members SET firstname = ?, lastname = ?, email = ?, address = ?, phonenumber = ?, username = ?
            WHERE member_id = ?";

        if ($stmt = $link->prepare($qry)) {
            $stmt->bind_param("ssssssi", $newFname, $newLname, $newEmail, $newAddress, $newPhone, $newUsername, $userID);

            if ($stmt->execute()) {
            echo "<p style='text-align: center; color: lime;'>Muutokset tallennettu onnistuneesti!</p>";

            $_SESSION['SESS_FIRST_NAME'] = $newFname;
            $_SESSION['SESS_LAST_NAME'] = $newLname;
            $_SESSION['SESS_LOGIN'] = $newUsername;
            $_SESSION['SESS_EMAIL'] = $newEmail;
            
            $fname = $newFname;
            $lname = $newLname;
            $username = $newUsername;
            $email = $newEmail;
        } else {
            echo "<p style='text-align: center; color: red;'>Virhe tiedon syöttämisessä: </p>" . $stmt->error;
        }

        // Close the statement
        $stmt->close();
        } else {
        echo "<p style='text-align: center; color: red;'>Virhe SQL lauseessa: </p>" . $conn->error;
        }
        }
     }

     if (isset($_POST['pwdChange']) && !isset($_POST['sendData'])) { 
        $newPassword = test_input($_POST['pwd']);
        $newPasswordHashed = md5($newPassword);

        $qry = "UPDATE members SET password = ? WHERE member_id = ?";

        if ($stmt = $link->prepare($qry)) {
            $stmt->bind_param("si", $newPasswordHashed, $userID);

            if ($stmt->execute()) {
            echo "<p style='text-align: center; color: lime;'>Salasana vaihdettu onnistuneesti!</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Virhe salasanan vaihdossa: " . $stmt->error . "</p>";
        }

        
        $stmt->close();
    } else {
        echo "<p style='text-align: center; color: red;'>Virhe SQL lauseessa: " . $link->error . "</p>";
        }
    }
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }
?>