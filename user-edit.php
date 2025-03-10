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

<h1 style="text-align:center;"><?= $current_lang['UserDetails']; ?></h1>
<p style="text-align: center;">
    <a href="index.php?page=profiili" style="text-decoration: none;"><?= $current_lang['back']; ?></a> | 
    <a href="index.php?page=logout" style="text-decoration: none;"><?= $current_lang['logout']; ?></a>
</p>
<h4 style="text-align:center; color: white;"><?= $current_lang['EditPageInfo']; ?></h4>

<form method="POST" class="edit-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
    <div style="margin-bottom: 15px;">
        <label for="fname"><b><?= $current_lang['FirstName']; ?></label>
        <input class="edit-form-input" type="text" name="fname" value="<?php echo htmlspecialchars($fname);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="lname"><b><?= $current_lang['LastName']; ?></b></label>
        <input class="edit-form-input" type="text" name="lname" value="<?php echo htmlspecialchars($lname);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="email"><b><?= $current_lang['Email']; ?></b></label>
        <input class="edit-form-input" type="email" name="email" value="<?php echo htmlspecialchars($email);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="username"><b><?= $current_lang['User']; ?></b></label>
        <input class="edit-form-input" type="text" name="username" value="<?php echo htmlspecialchars($username);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="address"><b><?= $current_lang['Address']; ?></b></label>
        <input class="edit-form-input" type="text" name="address" value="<?php echo htmlspecialchars($address);?>" required>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="phone"><b><?= $current_lang['PhoneNmb']; ?></b></label>
        <input class="edit-form-input" type="text" name="phone" value="<?php echo htmlspecialchars($phone);?>" required>
    </div>

    <div style="text-align: center;">
        <input class="edit-btn" type="submit" value="<?= $current_lang['Save2']; ?>" name="sendData" style="background-color: #4CAF50; border: none;">
    </div>
</form>

<form method="POST" class="edit-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?page=<?php echo htmlspecialchars($_GET['page']); ?>">
    <label for="pwd"><b><?= $current_lang['pwdchangemsg']; ?></b></label>
    <input class="edit-form-input" type="password" name="pwd">
    <br><br>
    <input class="edit-btn" type="submit" value="<?= $current_lang['pwdchange']; ?>" name="pwdChange" style="background-color: #4CAF50; border: none;">
</form>


<?php
    if (isset($_POST['sendData']) && !isset($_POST['pwdChange'])) {
        $newFname = test_input($_POST['fname']);
        $newLname = test_input($_POST['lname']);
        $newEmail = test_input($_POST['email']);
        $newUsername = test_input($_POST['username']);
        $newAddress = test_input($_POST['address']);
        $newPhone = test_input($_POST['phone']);

        // Check if the new username already exists in the database (excluding current user)
        $qry = "SELECT 1 FROM members WHERE username = ? AND member_id != ?";

        if ($stmt = $link->prepare($qry)) {
            $stmt->bind_param("si", $newUsername, $userID); // Bind username and userID
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                // Username already exists, show error message
                echo "<p style='text-align: center; color: red;'>" . $current_lang['usernameExists'] . "</p>";
            } else {
                // Proceed if the username is unique
                $oldData = array($fname, $lname, $email, $address, $phone, $username);
                $newData = array($newFname, $newLname, $newEmail, $newAddress, $newPhone, $newUsername);

                if ($newData == $oldData) {
                    echo "<p style='text-align: center; color: red;'>" . $current_lang['editalert1'] . "</p>";
                } else {
                    // Proceed with the update if data has changed
                    $qry = "UPDATE members SET firstname = ?, lastname = ?, email = ?, address = ?, phonenumber = ?, username = ? WHERE member_id = ?";

                    if ($stmt = $link->prepare($qry)) {
                        $stmt->bind_param("ssssssi", $newFname, $newLname, $newEmail, $newAddress, $newPhone, $newUsername, $userID);

                        if ($stmt->execute()) {
                            // On success, update session data
                            echo "<p style='text-align: center; color: lime;'>" . $current_lang['editalert2'] . "</p>";
                            $_SESSION['SESS_FIRST_NAME'] = $newFname;
                            $_SESSION['SESS_LAST_NAME'] = $newLname;
                            $_SESSION['SESS_LOGIN'] = $newUsername;
                            $_SESSION['SESS_EMAIL'] = $newEmail;
                            
                            // Update local variables
                            $fname = $newFname;
                            $lname = $newLname;
                            $username = $newUsername;
                            $email = $newEmail;
                        } else {
                            echo "<p style='text-align: center; color: red;'>" . $current_lang['editalert3'] . "</p>" . $stmt->error;
                        }

                        // Close the statement
                        $stmt->close();
                    } else {
                        echo "<p style='text-align: center; color: red;'>" . $current_lang['editalert4'] . "</p>" . $link->error;
                    }
                }
            }
        } else {
            echo "<p style='text-align: center; color: red;'>" . $current_lang['editalert4'] . "</p>" . $link->error;
        }
    }

    if (isset($_POST['pwdChange']) && !isset($_POST['sendData'])) { 
        $newPassword = test_input($_POST['pwd']);

        if (empty($newPassword)) {
            echo "<p style='text-align: center; color: red;'>" . $current_lang['pwdalert1'] . "</p>";
        } else {
            $newPasswordHashed = md5($newPassword);

            $qry = "UPDATE members SET password = ? WHERE member_id = ?";

            if ($stmt = $link->prepare($qry)) {
                $stmt->bind_param("si", $newPasswordHashed, $userID);

                if ($stmt->execute()) {
                    echo "<p style='text-align: center; color: lime;'>" . $current_lang['pwdalert2'] . "</p>";
                } else {
                    echo "<p style='text-align: center; color: red;'>" . $current_lang['pwdalert3'] . " " . $stmt->error . "</p>";
                }

                $stmt->close();
            } else {
                echo "<p style='text-align: center; color: red;'>" . $current_lang['editalert4'] . " " . $link->error . "</p>";
            }
        }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>
