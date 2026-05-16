<?php
session_start();
require_once('connexion.php');
$racine = '../';

//seuls les organisateurs et admins peuvent modifier
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

//on récupère l'événement
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id_event = (int)$_GET['id'];
$req = $bdd->prepare("SELECT * FROM evenements WHERE id = ?");
$req->execute(array($id_event));
$event = $req->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: ../index.php');
    exit();
}

//on vérifie que c'est bien l'organisateur de cet événement (ou un admin)
if ($_SESSION['role'] !== 'administrateur' && $event['organisateur_id'] != $_SESSION['id']) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'événement - OmnesEvent</title>
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
            <h2>Modifier l'événement</h2>
            <p class="sous-texte">Modifiez les informations de votre événement</p>

            <?php if (isset($_GET['erreur'])): ?>
                <div class="message-erreur">
                    <?php
                    if ($_GET['erreur'] === 'champs') echo 'Veuillez remplir tous les champs obligatoires.';
                    if ($_GET['erreur'] === 'image') echo 'Format d\'image non accepté (JPG, PNG ou WEBP uniquement).';
                    ?>
                </div>
            <?php endif; ?>

            <form action="traitement_modifier_evenement.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">

                <div class="form-group">
                    <label>Titre de l'événement *</label>
                    <input type="text" name="titre" value="<?php echo htmlspecialchars($event['titre']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date_event" value="<?php echo $event['date_event']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Heure *</label>
                    <input type="time" name="heure" value="<?php echo $event['heure']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Lieu *</label>
                    <input type="text" name="lieu" value="<?php echo htmlspecialchars($event['lieu']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Catégorie *</label>
                    <select name="categorie" required>
                        <option value="soiree" <?php if ($event['categorie'] === 'soiree') echo 'selected'; ?>>Soirée</option>
                        <option value="sport" <?php if ($event['categorie'] === 'sport') echo 'selected'; ?>>Sport</option>
                        <option value="culture" <?php if ($event['categorie'] === 'culture') echo 'selected'; ?>>Culture</option>
                        <option value="conference" <?php if ($event['categorie'] === 'conference') echo 'selected'; ?>>Conférence</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Association</label>
                    <input type="text" name="association" value="<?php echo htmlspecialchars($event['association']); ?>">
                </div>

                <div class="form-group">
                    <label>Capacité maximale *</label>
                    <input type="number" name="capacite_max" min="1" value="<?php echo $event['capacite_max']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Changer l'affiche (laisser vide pour garder l'actuelle)</label>
                    <input type="file" name="affiche" accept="image/jpeg,image/png,image/webp">
                    <?php if ($event['affiche']): ?>
                        <p class="texte-muted mt-10">Affiche actuelle : <?php echo htmlspecialchars($event['affiche']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
