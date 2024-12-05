
<?php
 session_start();
 $IsLoggedIn = $_SESSION['loggedin'] ?? false;
?>
<div style="text-align: center; color: white;">
    <h1>KG Keittiökalusteet</h1>
    <h3>Innovatiiviset Keittiövälineet</h3>
    <p>Tuomme keittiöösi käytännöllisyyttä ja tyyliä! KG Keittiökalusteet tarjoaa laadukkaita keittiögadgeteja, 
    jotka tekevät arjesta sujuvampaa ja ruoanlaitosta nautinnollisempaa.</p>
    <?php if (!$IsLoggedIn): ?>
        <a style="margin-right: 10px;" id="login-btn" class="edit-btn" href="index.php?page=login-form">Kirjaudu sisään</a>
        <a class="edit-btn" id="register-btn" href="index.php?page=register-form">Rekisteröidy</a>
    <?php endif; ?>
</div>