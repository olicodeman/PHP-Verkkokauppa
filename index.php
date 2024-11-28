<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verkkokauppa</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php
    // Navigointi scripti
<<<<<<< HEAD
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-exec", "register-success", "tuoteet");
=======
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-exec", "register-success", "user-edit");
>>>>>>> 2109bc9c950eeaeb9b9bf7b34fbf07089d0bd17e
        $page = "etusivu";
        if(isset($_GET['page']))
            $page = $_GET['page'];
    ?>
    <!-- Navigointi valikko, joka muutuu pienellä näytöllä -->
    <nav class="navbar">
        <div class="menu-icon" onclick="toggleMenu()">☰</div>
        <ul class="nav-links">
        <li><a href="index.php?page=etusivu">Etusivu</a></li>
        <li><a href="index.php?page=profiili">Profiili</a></li>
        <li><a href="index.php?page=login-form">Kirjaudu sisään</a></li>
        <li><a href="index.php?page=register-form">Rekisteröidy</a></li>
<<<<<<< HEAD
        <li><a href="index.php?page=tuoteet">Tuotteet</a></li>
=======
        
>>>>>>> 2109bc9c950eeaeb9b9bf7b34fbf07089d0bd17e
    </ul>
    </nav>
    

    <?php
    // Navigointi scripti
<<<<<<< HEAD
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-exec", "register-success", "tuoteet");
=======
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-exec", "register-success", "user-edit");
>>>>>>> 2109bc9c950eeaeb9b9bf7b34fbf07089d0bd17e
        $page = "etusivu";
        if(isset($_GET['page']))
            $page = $_GET['page'];

            if(in_array($page, $pages))
            {
                include($page.".php");
            }
            else {
                include("error.php");
            }
    ?>

    <!-- Navigointi valikkon script toiminta pienellä näytöllä -->
<script>
        function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('hidden');
    }
    </script>
</body>
</html>