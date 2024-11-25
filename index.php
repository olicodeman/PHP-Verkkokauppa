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
        $pages = array("etusivu", "profiili", "login-form", "register-form");
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
    </ul>
    </nav>
    

    <?php
        $pages = array("etusivu", "profiili", "login-form", "register-form");
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