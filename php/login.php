<?php
session_start();
$racine = '../';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/formulaires.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-form">
            <h2>Connexion</h2>
            <p class="sous-texte">Connectez-vous pour réserver vos places</p>

            <?php
            //affichage du message de succès après inscription
            if (isset($_GET['inscription']) && $_GET['inscription'] === 'reussie') {
                echo '<div class="message-succes">Inscription réussie ! Vous pouvez maintenant vous connecter.</div>';
            }

            //affichage des erreurs
            if (isset($_GET['erreur'])) {
                echo '<div class="message-erreur">Identifiant ou mot de passe incorrect.</div>';
            }
            ?>

            <form action="verif_login.php" method="POST">
                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="nom_utilisateur" placeholder="Votre identifiant" required>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Votre mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
            </form>

            <p class="form-lien">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
