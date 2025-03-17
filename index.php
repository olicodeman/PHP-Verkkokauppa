<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KG Keittiövälineet</title>

    <link href="style.css" rel="stylesheet">

    <?php
    // Dynamically load page-specific CSS if it exists
    $page = isset($_GET['page']) ? $_GET['page'] : 'etusivu';  // Default to 'etusivu'
    $cssFile = "DynamicCss/" . $page . ".css";  

    // Check if the page-specific CSS file exists in the dynamicCss folder
    if (file_exists($cssFile)) {
        echo '<link rel="stylesheet" href="' . $cssFile . '">';
    }
    ?>
</head>
<style>
    /* Kielen vaihto form */
    .language-switcher {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 20px;
        margin: 0;
        padding: 0;
        border: none;
        background: none;
        margin-top: -10px;

    }

    /* Kieli napit */
    .language-switcher .lang-button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: transform 0.2s ease, border 0.2s ease;
    }

        /*hover efekti napeille */
    .language-switcher .lang-button:hover {
        transform: scale(1.1);
    }

        /* Nähdään että nappia on painettu */
    .language-switcher .lang-button.selected {
        border: 2px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
    }

    /* Kieli Iconi */
    .language-switcher .lang-icon {
        width: 30px;
        height: 30px;
    }

    /*Pidetään ostoskori oikealla puolella */
    .navbar .icon {
        margin-left: auto;
    }
    
        /* Logon määritykset */
    .company-logo {
    width: 50px; 
    height: auto;
}

.logo-link {
    display: flex;
    align-items: center;
    text-decoration: none;
}
/* Navigointipalkin perusasetukset */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;

}

/* Navigointilista */
.nav-links {
    list-style: none;
    display: flex;
    gap: 20px;
}

.nav-links li {
    display: inline;
}


/* Menu-kuvake (hamburger) */
.menu-icon {
    font-size: 24px;
    cursor: pointer;
    display: none;
}

/* Piilotetaan valikko pienillä näytöillä */
@media screen and (max-width: 768px) {
    .nav-links {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        padding: 10px 0;
    }

    .nav-links.active {
        display: flex;
    }

    .menu-icon {
        display: block;
    }
}

</style>

<body>
<?php
//Aloitetaan session
session_start();

// Liitettään käännökset
require 'lang.php';

// Aloituskieli asetetaan englanniksi
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

// Muutetaan kieli jos käyttäjä valitsee
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $lang)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Päivitetään kieli
$current_lang = $lang[$_SESSION['lang']];

// Varmistetaan että käyttäjä on kirjautuneena
$IsLoggedIn = $_SESSION['loggedin'] ?? false;
session_write_close();

// Määritellään sivut
$pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm", "lisaaArvostelu", "arvosteluSivu", "submit_review");
$page = "etusivu";

// Tarkistetaan tämänhetkinen sivu query parametreillä
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    $page = $_GET['page'];
} else {
    $page = 'etusivu';  //Oletus sivu
}
?>

        <!-- Navigointi valikko -->
<nav class="navbar">
    <div class="menu-icon" onclick="toggleMenu()">â˜°</div>
    <a href="index.php?page=etusivu" class="logo-link">
        <img src="kuvat/KGiconi.png" alt="Company Logo" class="company-logo">
    </a>
    <ul class="nav-links">
        <li><a href="index.php?page=etusivu" class="<?= ($page == 'etusivu') ? 'active' : '' ?>"><?= $current_lang['homepage']; ?></a></li>
        <li><a href="index.php?page=tuoteet" class="<?= ($page == 'tuoteet') ? 'active' : '' ?>"><?= $current_lang['products']; ?></a></li>
        <?php if ($IsLoggedIn): ?>
            <li><a href="index.php?page=profiili" class="<?= ($page == 'profiili') ? 'active' : '' ?>"><?= $current_lang['profile']; ?></a></li>
            <li id="logout-link"><a href="index.php?page=logout" class="<?= ($page == 'logout') ? 'active' : '' ?>"><?= $current_lang['logout']; ?></a></li>
        <?php else: ?>
            <li><a href="index.php?page=login-form" class="<?= ($page == 'login-form') ? 'active' : '' ?>"><?= $current_lang['login']; ?></a></li>
            <li><a href="index.php?page=register-form" class="<?= ($page == 'register-form') ? 'active' : '' ?>"><?= $current_lang['register']; ?></a></li>
        <?php endif; ?>

        </ul>

        <!-- Kielen vaihto-->
        <form method="get" class="language-switcher">
            <input type="hidden" name="page" value="<?= $_GET['page'] ?? 'etusivu'; ?>"> 
            <button type="submit" name="lang" value="en"
                    class="lang-button <?= ($_SESSION['lang'] ?? 'en') === 'en' ? 'selected' : '' ?>">
                <img src="kuvat/englantilippu.png" alt="English" class="lang-icon">
            </button>
            <button type="submit" name="lang" value="fi"
                    class="lang-button <?= ($_SESSION['lang'] ?? 'en') === 'fi' ? 'selected' : '' ?>">
                <img src="kuvat/suomenlippu.png" alt="Suomi" class="lang-icon">
            </button>
        </form>
        <div class="nav-right">

        <!-- Ostoskori -->
            <a href="index.php?page=ostoskori">
                <div class="icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/6713/6713719.png" alt="Ostoskori"
                        class="cart-icon">
                </div>
            </a>
        </div>
    </nav>


    <?php
    // Navigointi scripti
    $pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm", "lisaaArvostelu", "arvosteluSivu",  "submit_review");
    $page = "etusivu";
    if (isset($_GET['page']))
        $page = $_GET['page'];

    if (in_array($page, $pages) && file_exists($page . ".php")) {
        include($page . ".php");
    } else {
        include("error.php");
    }
    ?>

    <!-- Navigointi valikkon script toiminta pienellä näytöllä -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuIcon = document.querySelector('.menu-icon');
    const navLinks = document.querySelector('.nav-links');

    menuIcon.addEventListener('click', function () {
        navLinks.classList.toggle('active');
    });
});
</script>

</body>