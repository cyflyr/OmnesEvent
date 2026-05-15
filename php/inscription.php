<?php
session_start();
$racine = '../';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - OmnesEvent</title>
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
            <h2>Inscription</h2>
            <p class="sous-texte">Créez votre compte pour accéder aux événements</p>

            <?php
            //affichage des erreurs
            if (isset($_GET['erreur'])) {
                $erreurs = array(
                    'mdp' => 'Les mots de passe ne correspondent pas.',
                    'utilisateur_existe' => 'Ce nom d\'utilisateur est déjà pris.',
                    'champs' => 'Veuillez remplir tous les champs.'
                );
                $code = htmlspecialchars($_GET['erreur']);
                if (isset($erreurs[$code])) {
                    echo '<div class="message-erreur">' . $erreurs[$code] . '</div>';
                }
            }
            ?>

            <form action="verif_inscription.php" method="POST">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" placeholder="Votre prénom" required>
                </div>

                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" placeholder="Votre nom" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="votre@email.fr" required>
                </div>

                <div class="form-group">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="nom_utilisateur" placeholder="Choisissez un identifiant" required>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Votre mot de passe" required>
                </div>

                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" placeholder="Retapez votre mot de passe" required>
                </div>

                <div class="form-group">
                    <label>Type de compte</label>
                    <select name="role">
                        <option value="participant">Participant (étudiant / personnel)</option>
                        <option value="organisateur">Organisateur (association / BDE)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-full">S'inscrire</button>
            </form>

            <p class="form-lien">Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
