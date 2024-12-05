<?php
    require_once("config.php");
    require_once("auth.php");

    $login = $_SESSION['SESS_LOGIN'];

    if ($login !== 'admin') {
        header('location: index.php?page=error');
        exit();
    }

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$conn) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysqli_select_db($conn, DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}

        $sql = "DELETE FROM tuotteet WHERE id=?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id); 
            if ($stmt->execute()) {
                header("Location: admin-panel.php");
                exit;
            } else {
                echo "Error: tuotetta ei pystytty poistaa.";
            }
        } else {
            echo "Error: SQL lause virhe.";
        }
    } else {
        echo "Väärä tuote ID.";
    }

?>