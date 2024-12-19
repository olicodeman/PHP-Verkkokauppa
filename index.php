<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KG Keittiövälineet</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php
        session_start();
        $IsLoggedIn = $_SESSION['loggedin'] ?? false;
        session_write_close();

    // Navigointi scripti
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm");
        $page = "etusivu";
        if(isset($_GET['page']))
            $page = $_GET['page'];
    ?>
    <!-- Navigointi valikko, joka muutuu pienellä näytöllä -->
<nav class="navbar">
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
    <ul class="nav-links">
        <!-- Add active class if the current page matches the link -->
        <li><a href="index.php?page=etusivu" class="<?= ($page == 'etusivu') ? 'active' : '' ?>">Etusivu</a></li>
        <li><a href="index.php?page=tuoteet" class="<?= ($page == 'tuoteet') ? 'active' : '' ?>">Tuotteet</a></li>

        <?php if ($IsLoggedIn): ?>
            <li><a href="index.php?page=profiili" class="<?= ($page == 'profiili') ? 'active' : '' ?>">Profiili</a></li>
            <li id="logout-link"><a href="index.php?page=logout" class="<?= ($page == 'logout') ? 'active' : '' ?>">Kirjaudu ulos</a></li>  
        <?php else: ?>
            <li><a href="index.php?page=login-form" class="<?= ($page == 'login-form') ? 'active' : '' ?>">Kirjaudu sisään</a></li>
            <li><a href="index.php?page=register-form" class="<?= ($page == 'register-form') ? 'active' : '' ?>">Rekisteröidy</a></li>
        <?php endif; ?>
    </ul>
    <a href="index.php?page=ostoskori"><div class="icon">
    <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Ostoskori" class="cart-icon"></div></a>
</nav>
    

    <?php
    // Navigointi scripti
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm");
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