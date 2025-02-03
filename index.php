<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KG Keittiövälineet</title>
    <link href="style.css" rel="stylesheet">
</head>
<style>
    /* Language Switcher Form */
    .language-switcher {
        display: flex;
        /* Align language icons horizontally */
        align-items: center;
        /* Vertically align with nav links */
        gap: 10px;
        /* Add space between the language buttons */
        margin-left: 20px;
        /* Add spacing to the left of the form */
        margin: 0;
        /* Remove extra margins */
        padding: 0;
        /* Remove extra padding */
        border: none;
        /* Remove any border */

    }

    /* Language Button */
    .language-switcher .lang-button {
        background: none;
        /* Remove button background */
        border: none;
        /* Remove button border */
        cursor: pointer;
        /* Pointer cursor for hover */
        padding: 10px;
        /* Add padding for better click area */
        border-radius: 50%;
        /* Circular hover effect */
        transition: transform 0.2s ease, border 0.2s ease;
    }

    .language-switcher .lang-button:hover {
        transform: scale(1.1);
        /* Enlarge on hover */
    }

    .language-switcher .lang-button.selected {
        border: 2px solid #007bff;
        /* Add border to highlight selection */
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        /* Glow effect */
    }

    /* Language Icon */
    .language-switcher .lang-icon {
        width: 30px;
        height: 30px;
    }

    /* Ensure Cart Icon Stays Right-Aligned */
    .navbar .icon {
        margin-left: auto;
        /* Push cart icon to the far right */
    }
</style>

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

// Ensure the user is logged in
$IsLoggedIn = $_SESSION['loggedin'] ?? false;
session_write_close();

// Define pages
$pages = array("etusivu", "profiili", "login-form", "register-form", "logout", "register-success", "tuoteet", "user-edit", "ostoskori", "maksuForm", "lisaaArvostelu", "arvosteluSivu", "submit_review");
$page = "etusivu";

// Check for current page in query parameters
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
    $page = $_GET['page'];
} else {
    $page = 'etusivu';  // Default page
}
?>

<nav class="navbar">
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
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
        
        <!-- Language Switcher Form -->
        <form method="get" class="language-switcher">
            <input type="hidden" name="page" value="<?= $_GET['page'] ?? 'etusivu'; ?>"> <!-- Preserve current page -->
            <button type="submit" name="lang" value="en"
                    class="lang-button <?= ($_SESSION['lang'] ?? 'en') === 'en' ? 'selected' : '' ?>">
                <img src="kuvat/englantilippu.png" alt="English" class="lang-icon">
            </button>
            <button type="submit" name="lang" value="fi"
                    class="lang-button <?= ($_SESSION['lang'] ?? 'en') === 'fi' ? 'selected' : '' ?>">
                <img src="kuvat/suomenlippu.png" alt="Suomi" class="lang-icon">
            </button>
        </form>

        </ul>
        <div class="nav-right">

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

    if (in_array($page, $pages)) {
        include($page . ".php");
    } else {
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