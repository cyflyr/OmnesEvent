<?php
session_start();
require_once('connexion.php');
$racine = '../';

//l'utilisateur doit être connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

//on récupère les inscriptions à venir
$req_avenir = $bdd->prepare("SELECT e.*, i.date_inscription AS date_inscr, i.statut AS statut_inscr
    FROM inscriptions i
    JOIN evenements e ON i.evenement_id = e.id
    WHERE i.utilisateur_id = ? AND i.statut = 'inscrit' AND e.date_event >= CURDATE()
    ORDER BY e.date_event ASC");
$req_avenir->execute(array($user_id));
$billets_avenir = $req_avenir->fetchAll(PDO::FETCH_ASSOC);

//on récupère les événements passés
$req_passes = $bdd->prepare("SELECT e.*, i.date_inscription AS date_inscr
    FROM inscriptions i
    JOIN evenements e ON i.evenement_id = e.id
    WHERE i.utilisateur_id = ? AND i.statut = 'inscrit' AND e.date_event < CURDATE()
    ORDER BY e.date_event DESC");
$req_passes->execute(array($user_id));
$billets_passes = $req_passes->fetchAll(PDO::FETCH_ASSOC);

//si l'utilisateur est organisateur, on récupère ses événements créés
$mes_events = array();
if ($_SESSION['role'] === 'organisateur' || $_SESSION['role'] === 'administrateur') {
    $req_orga = $bdd->prepare("SELECT e.*,
        (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
        FROM evenements e
        WHERE e.organisateur_id = ?
        ORDER BY e.date_event DESC");
    $req_orga->execute(array($user_id));
    $mes_events = $req_orga->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/profil.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-profil">

            <!-- En-tête profil -->
            <div class="profil-header">
                <div class="profil-avatar">
                    <?php echo strtoupper(htmlspecialchars($_SESSION['prenom'])[0]); ?>
                </div>
                <div class="profil-infos">
                    <h1><?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></h1>
                    <p><?php echo ucfirst($_SESSION['role']); ?> &bull; @<?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?></p>
                </div>
            </div>

            <!-- Onglets -->
            <div class="onglets">
                <button class="onglet actif" data-onglet="avenir">À venir (<?php echo count($billets_avenir); ?>)</button>
                <button class="onglet" data-onglet="passes">Passés (<?php echo count($billets_passes); ?>)</button>
            </div>

            <!-- Billets à venir -->
            <div class="contenu-onglet visible" id="onglet-avenir">
                <?php if (count($billets_avenir) > 0): ?>
                    <div class="liste-billets">
                        <?php foreach ($billets_avenir as $billet): ?>
                            <div class="billet-carte">
                                <div class="billet-infos">
                                    <h3><?php echo htmlspecialchars($billet['titre']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($billet['date_event'])); ?> à <?php echo date('H\hi', strtotime($billet['heure'])); ?></p>
                                    <p><?php echo htmlspecialchars($billet['lieu']); ?></p>
                                </div>
                                <div class="billet-actions">
                                    <a href="evenement.php?id=<?php echo $billet['id']; ?>" class="btn btn-outline btn-small">Voir</a>
                                    <form action="annuler_inscription.php" method="POST">
                                        <input type="hidden" name="evenement_id" value="<?php echo $billet['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-small">Annuler</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="aucun-billet">Aucun événement à venir. Explorez le catalogue !</p>
                <?php endif; ?>
            </div>

            <!-- Billets passés -->
            <div class="contenu-onglet" id="onglet-passes">
                <?php if (count($billets_passes) > 0): ?>
                    <div class="liste-billets">
                        <?php foreach ($billets_passes as $billet): ?>
                            <div class="billet-carte billet-annule">
                                <div class="billet-infos">
                                    <h3><?php echo htmlspecialchars($billet['titre']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($billet['date_event'])); ?> à <?php echo date('H\hi', strtotime($billet['heure'])); ?></p>
                                    <p><?php echo htmlspecialchars($billet['lieu']); ?></p>
                                </div>
                                <div class="billet-actions">
                                    <a href="evenement.php?id=<?php echo $billet['id']; ?>" class="btn btn-outline btn-small">Voir</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="aucun-billet">Aucun événement passé.</p>
                <?php endif; ?>
            </div>

            <!-- Section organisateur : mes événements créés -->
            <?php if ($_SESSION['role'] === 'organisateur' || $_SESSION['role'] === 'administrateur'): ?>
                <div class="section-orga">
                    <h2>Mes événements créés</h2>

                    <?php if (count($mes_events) > 0): ?>
                        <?php foreach ($mes_events as $event): ?>
                            <div class="orga-event">
                                <div class="orga-event-infos">
                                    <h3><?php echo htmlspecialchars($event['titre']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($event['date_event'])); ?> - <?php echo htmlspecialchars($event['lieu']); ?></p>
                                </div>
                                <div class="orga-event-stats">
                                    <span class="nombre"><?php echo $event['nb_inscrits']; ?></span>
                                    <p>/ <?php echo $event['capacite_max']; ?> inscrits</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="aucun-billet">Vous n'avez pas encore créé d'événement.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
    <script src="../js/onglets.js"></script>
</body>
</html>
