<?php
session_start();
require_once('connexion.php');
$racine = '../';

//seuls les organisateurs et admins peuvent créer des événements
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/formulaires.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-form page-form-large">
            <h2>Créer un événement</h2>
            <p class="sous-texte">Remplissez les informations de votre événement</p>

            <?php if (isset($_GET['erreur'])): ?>
                <div class="message-erreur">
                    <?php
                    if ($_GET['erreur'] === 'champs') echo 'Veuillez remplir tous les champs obligatoires.';
                    if ($_GET['erreur'] === 'image') echo 'Format d\'image non accepté (JPG, PNG ou WEBP uniquement).';
                    ?>
                </div>
            <?php endif; ?>

            <form action="traitement_evenement.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre de l'événement *</label>
                    <input type="text" name="titre" placeholder="Ex: Gala de fin d'année" required>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" placeholder="Décrivez votre événement..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date_event" required>
                </div>

                <div class="form-group">
                    <label>Heure *</label>
                    <input type="time" name="heure" required>
                </div>

                <div class="form-group">
                    <label>Lieu *</label>
                    <input type="text" name="lieu" placeholder="Ex: Amphithéâtre B, Campus Paris" required>
                </div>

                <div class="form-group">
                    <label>Catégorie *</label>
                    <select name="categorie" required>
                        <option value="">-- Choisir --</option>
                        <option value="soiree">Soirée</option>
                        <option value="sport">Sport</option>
                        <option value="culture">Culture</option>
                        <option value="conference">Conférence</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Association</label>
                    <input type="text" name="association" placeholder="Ex: BDE, BDS, Junior Entreprise...">
                </div>

                <div class="form-group">
                    <label>Capacité maximale *</label>
                    <input type="number" name="capacite_max" min="1" placeholder="Nombre de places" required>
                </div>

                <div class="form-group">
                    <label>Affiche (image)</label>
                    <input type="file" name="affiche" accept="image/jpeg,image/png,image/webp">
                </div>

                <button type="submit" class="btn btn-primary btn-full">Publier l'événement</button>
            </form>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
