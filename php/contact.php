<?php
session_start();
$racine = '../';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-contact">
            <h1>Contactez-nous</h1>
            <p class="sous-texte">Vous avez une question, une suggestion ou vous avez repéré un problème ?<br>
            N'hésitez pas à nous écrire.</p>

            <a href="mailto:contact@omnesevent.fr" class="contact-email">contact@omnesevent.fr</a>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
