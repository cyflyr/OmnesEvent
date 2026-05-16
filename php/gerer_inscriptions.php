<?php
session_start();
require_once('connexion.php');
$racine = '../';

//seuls les organisateurs et admins peuvent gérer les inscriptions
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id_event = (int)$_GET['id'];

//on récupère l'événement
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

//on récupère la liste des inscrits
$req_inscrits = $bdd->prepare("SELECT i.*, u.prenom, u.nom, u.email, u.nom_utilisateur
    FROM inscriptions i
    JOIN utilisateurs u ON i.utilisateur_id = u.id
    WHERE i.evenement_id = ? AND i.statut = 'inscrit'
    ORDER BY i.date_inscription ASC");
$req_inscrits->execute(array($id_event));
$inscrits = $req_inscrits->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les inscriptions - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/gestion.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-gestion">

            <a href="evenement.php?id=<?php echo $event['id']; ?>" class="lien-retour">&larr; Retour à l'événement</a>

            <h1>Gestion des inscriptions</h1>
            <p class="sous-texte-gestion"><?php echo htmlspecialchars($event['titre']); ?> &mdash; <?php echo date('d/m/Y', strtotime($event['date_event'])); ?></p>

            <!-- Messages -->
            <?php if (isset($_GET['succes'])): ?>
                <div class="message-succes">
                    <?php
                    if ($_GET['succes'] === 'presence') echo 'Les présences ont été mises à jour.';
                    ?>
                </div>
            <?php endif; ?>

            <div class="stats-gestion">
                <div class="stat-box">
                    <span class="stat-nombre"><?php echo count($inscrits); ?></span>
                    <span class="stat-label">Inscrits</span>
                </div>
                <div class="stat-box">
                    <span class="stat-nombre"><?php echo $event['capacite_max']; ?></span>
                    <span class="stat-label">Places max</span>
                </div>
                <div class="stat-box">
                    <span class="stat-nombre"><?php echo $event['capacite_max'] - count($inscrits); ?></span>
                    <span class="stat-label">Restantes</span>
                </div>
            </div>

            <?php if (count($inscrits) > 0): ?>
                <form action="traitement_presence.php" method="POST">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">

                    <table class="table-inscrits">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Présent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $num = 1; ?>
                            <?php foreach ($inscrits as $inscrit): ?>
                                <tr>
                                    <td><?php echo $num++; ?></td>
                                    <td><?php echo htmlspecialchars($inscrit['prenom'] . ' ' . $inscrit['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($inscrit['email']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($inscrit['date_inscription'])); ?></td>
                                    <td>
                                        <input type="checkbox" name="presents[]" value="<?php echo $inscrit['utilisateur_id']; ?>" <?php if ($inscrit['present']) echo 'checked'; ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary mt-20">Enregistrer les présences</button>
                </form>
            <?php else: ?>
                <p class="aucun-inscrit">Aucun inscrit pour le moment.</p>
            <?php endif; ?>

        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
</body>
</html>
