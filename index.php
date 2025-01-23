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
        // Start session
        session_start();
        
        // Include the language file
        require 'lang.php';
        
        // Set the default language to English
        if (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] = 'en';
        }
        
        // Change language if the user selects one
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $lang)) {
            $_SESSION['lang'] = $_GET['lang'];
        }
        
        // Load the current language
        $current_lang = $lang[$_SESSION['lang']];
        
        $IsLoggedIn = $_SESSION['loggedin'] ?? false;
        session_write_close();

    // Navigointi scripti
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm", "lisaaArvostelu", "arvosteluSivu");
        $page = "etusivu";
        if(isset($_GET['page']))
            $page = $_GET['page'];
    ?>
    <!-- Navigointi valikko, joka muutuu pienellä näytöllä -->
<nav class="navbar">
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
    <ul class="nav-links">
        <!-- Add active class if the current page matches the link -->
        <li><a href="index.php?page=etusivu" class="<?= ($page == 'etusivu') ? 'active' : '' ?>"><?= $current_lang['homepage']; ?></a></li>
        <li><a href="index.php?page=tuoteet" class="<?= ($page == 'tuoteet') ? 'active' : '' ?>"><?= $current_lang['products']; ?></a></li>

        <?php if ($IsLoggedIn): ?>
            <li><a href="index.php?page=profiili" class="<?= ($page == 'profiili') ? 'active' : '' ?>"><?= $current_lang['profile']; ?></a></li>
            <li id="logout-link"><a href="index.php?page=logout" class="<?= ($page == 'logout') ? 'active' : '' ?>"><?= $current_lang['logout']; ?></a></li>  
        <?php else: ?>
            <li><a href="index.php?page=login-form" class="<?= ($page == 'login-form') ? 'active' : '' ?>"><?= $current_lang['login']; ?></a></li>
            <li><a href="index.php?page=register-form" class="<?= ($page == 'register-form') ? 'active' : '' ?>"><?= $current_lang['register']; ?></a></li>
        <?php endif; ?>
        <form method="get" style="display: inline;">
    <select name="lang" onchange="this.form.submit()">
    <img src="" alt="Ostoskori" class="cart-icon"></div></a>
        <option value="en" <?= ($_SESSION['lang'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
        <option value="fi" <?= ($_SESSION['lang'] ?? 'en') === 'fi' ? 'selected' : '' ?>>Suomi</option>
    </select>
</form>

    </ul>
    <a href="index.php?page=ostoskori"><div class="icon">
    <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Ostoskori" class="cart-icon"></div></a>
</nav>
    

    <?php
    // Navigointi scripti
        $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm","lisaaArvostelu", "arvosteluSivu");
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