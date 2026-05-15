<?php
session_start();
require_once('connexion.php');
$racine = '../';

//seul l'admin peut accéder à cette page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../index.php');
    exit();
}

//récupération des organisateurs en attente de validation
$req_attente = $bdd->prepare("SELECT * FROM utilisateurs WHERE role = 'organisateur' AND valide = 0 ORDER BY date_inscription DESC");
$req_attente->execute();
$orga_attente = $req_attente->fetchAll(PDO::FETCH_ASSOC);

//récupération de tous les utilisateurs
$req_users = $bdd->prepare("SELECT * FROM utilisateurs ORDER BY date_inscription DESC");
$req_users->execute();
$tous_users = $req_users->fetchAll(PDO::FETCH_ASSOC);

//récupération de tous les événements
$req_events = $bdd->prepare("SELECT e.*, u.nom_utilisateur AS orga_pseudo,
    (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
    FROM evenements e
    JOIN utilisateurs u ON e.organisateur_id = u.id
    ORDER BY e.date_creation DESC");
$req_events->execute();
$tous_events = $req_events->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="page-admin">
            <h1>Tableau de bord</h1>

            <?php if (isset($_GET['succes'])): ?>
                <div class="message-succes">
                    <?php
                    if ($_GET['succes'] === 'valide') echo 'Organisateur validé avec succès.';
                    if ($_GET['succes'] === 'supprime_user') echo 'Utilisateur supprimé.';
                    if ($_GET['succes'] === 'supprime_event') echo 'Événement supprimé.';
                    ?>
                </div>
            <?php endif; ?>

            <!-- Onglets admin -->
            <div class="admin-onglets">
                <button class="admin-onglet actif" data-section="attente">En attente (<?php echo count($orga_attente); ?>)</button>
                <button class="admin-onglet" data-section="utilisateurs">Utilisateurs (<?php echo count($tous_users); ?>)</button>
                <button class="admin-onglet" data-section="evenements">Événements (<?php echo count($tous_events); ?>)</button>
            </div>

            <!-- Section : organisateurs en attente -->
            <div class="admin-section visible" id="section-attente">
                <?php if (count($orga_attente) > 0): ?>
                    <table class="admin-tableau">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Prénom / Nom</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orga_attente as $orga): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($orga['nom_utilisateur']); ?></td>
                                <td><?php echo htmlspecialchars($orga['prenom'] . ' ' . $orga['nom']); ?></td>
                                <td><?php echo htmlspecialchars($orga['email']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($orga['date_inscription'])); ?></td>
                                <td class="admin-actions">
                                    <form action="traitement_admin.php" method="POST">
                                        <input type="hidden" name="action" value="valider_orga">
                                        <input type="hidden" name="user_id" value="<?php echo $orga['id']; ?>">
                                        <button type="submit" class="btn-admin btn-valider">Valider</button>
                                    </form>
                                    <form action="traitement_admin.php" method="POST">
                                        <input type="hidden" name="action" value="supprimer_user">
                                        <input type="hidden" name="user_id" value="<?php echo $orga['id']; ?>">
                                        <button type="submit" class="btn-admin btn-supprimer">Refuser</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="texte-muted texte-centre mt-20">Aucun organisateur en attente.</p>
                <?php endif; ?>
            </div>

            <!-- Section : tous les utilisateurs -->
            <div class="admin-section" id="section-utilisateurs">
                <table class="admin-tableau">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Prénom / Nom</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tous_users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nom_utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <?php if ($user['valide']): ?>
                                    <span class="statut-valide">Actif</span>
                                <?php else: ?>
                                    <span class="statut-attente">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-actions">
                                <?php if ($user['id'] != $_SESSION['id']): ?>
                                    <form action="traitement_admin.php" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                        <input type="hidden" name="action" value="supprimer_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn-admin btn-supprimer">Supprimer</button>
                                    </form>
                                <?php else: ?>
                                    <span class="texte-muted">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Section : tous les événements -->
            <div class="admin-section" id="section-evenements">
                <table class="admin-tableau">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Catégorie</th>
                            <th>Organisateur</th>
                            <th>Inscrits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tous_events as $ev): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ev['titre']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($ev['date_event'])); ?></td>
                            <td><span class="badge badge-<?php echo $ev['categorie']; ?>"><?php echo ucfirst($ev['categorie']); ?></span></td>
                            <td><?php echo htmlspecialchars($ev['orga_pseudo']); ?></td>
                            <td><?php echo $ev['nb_inscrits']; ?> / <?php echo $ev['capacite_max']; ?></td>
                            <td class="admin-actions">
                                <a href="evenement.php?id=<?php echo $ev['id']; ?>" class="btn-admin btn-valider">Voir</a>
                                <form action="traitement_admin.php" method="POST" onsubmit="return confirm('Supprimer cet événement ?');">
                                    <input type="hidden" name="action" value="supprimer_event">
                                    <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                    <button type="submit" class="btn-admin btn-supprimer">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>
