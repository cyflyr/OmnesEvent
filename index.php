<?php
session_start();
require_once('php/connexion.php');

//variable pour les chemins (on est à la racine)
$racine = '';

//on récupère les filtres de recherche si ils existent
$recherche = isset($_GET['recherche']) ? htmlspecialchars($_GET['recherche']) : '';
$categorie = isset($_GET['categorie']) ? htmlspecialchars($_GET['categorie']) : '';
//construction de la requête SQL avec les filtres
$sql = "SELECT e.*, u.prenom AS orga_prenom, u.nom AS orga_nom,
        (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
        FROM evenements e
        JOIN utilisateurs u ON e.organisateur_id = u.id
        WHERE e.date_event >= CURDATE()";
$params = array();

//filtre par recherche texte
if ($recherche !== '') {
    $sql .= " AND (e.titre LIKE ? OR e.lieu LIKE ? OR e.association LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

//filtre par catégorie
if ($categorie !== '' && $categorie !== 'tous') {
    $sql .= " AND e.categorie = ?";
    $params[] = $categorie;
}

//on trie par date la plus proche
$sql .= " ORDER BY e.date_event ASC";

$req = $bdd->prepare($sql);
$req->execute($params);
$evenements = $req->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OmnesEvent - Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/accueil.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>

    <?php include_once('php/menu.php'); ?>

    <div class="contenu-principal">

        <!-- Section hero -->
        <section class="hero">
            <img src="images/omnesevent-logo-final.png" alt="OmnesEvent" class="hero-logo">
            <h1>Tous les events d'<span>Omnes</span>,<br>au même endroit.</h1>
            <p>Découvrez les soirées, tournois, conférences et bien plus. Réservez votre place en un clic.</p>
        </section>

        <!-- Barre de recherche -->
        <section class="search-section">
            <form method="GET" action="index.php">
                <div class="search-bar">
                    <input type="text" name="recherche" placeholder="Rechercher un événement..." value="<?php echo $recherche; ?>">
                    <button type="submit" class="btn btn-primary btn-small">Rechercher</button>
                </div>

                <!-- Filtres par catégorie -->
                <div class="filtres">
                    <a href="index.php" class="filtre-btn <?php if ($categorie === '') echo 'actif'; ?>">Tous</a>
                    <a href="index.php?categorie=soiree<?php echo $recherche ? '&recherche='.$recherche : ''; ?>" class="filtre-btn <?php if ($categorie === 'soiree') echo 'actif'; ?>">Soirée</a>
                    <a href="index.php?categorie=sport<?php echo $recherche ? '&recherche='.$recherche : ''; ?>" class="filtre-btn <?php if ($categorie === 'sport') echo 'actif'; ?>">Sport</a>
                    <a href="index.php?categorie=culture<?php echo $recherche ? '&recherche='.$recherche : ''; ?>" class="filtre-btn <?php if ($categorie === 'culture') echo 'actif'; ?>">Culture</a>
                    <a href="index.php?categorie=conference<?php echo $recherche ? '&recherche='.$recherche : ''; ?>" class="filtre-btn <?php if ($categorie === 'conference') echo 'actif'; ?>">Conférence</a>
                </div>
            </form>
        </section>

        <!-- Liste des événements -->
        <h2 class="section-titre">À venir</h2>

        <?php if (count($evenements) > 0): ?>
            <section class="grille-events">
                <?php foreach ($evenements as $event): ?>
                    <a href="php/evenement.php?id=<?php echo $event['id']; ?>" class="carte-event">

                        <?php if ($event['affiche']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($event['affiche']); ?>" alt="<?php echo htmlspecialchars($event['titre']); ?>" class="carte-event-image">
                        <?php else: ?>
                            <div class="carte-event-image carte-event-placeholder"></div>
                        <?php endif; ?>

                        <div class="carte-event-body">
                            <span class="badge badge-<?php echo $event['categorie']; ?>">
                                <?php echo ucfirst($event['categorie']); ?>
                            </span>
                            <h3 class="carte-event-titre"><?php echo htmlspecialchars($event['titre']); ?></h3>
                            <p class="carte-event-info">
                                <?php echo date('d/m/Y', strtotime($event['date_event'])); ?> à <?php echo date('H\hi', strtotime($event['heure'])); ?>
                            </p>
                            <p class="carte-event-info"><?php echo htmlspecialchars($event['lieu']); ?></p>

                            <!-- Jauge de places -->
                            <?php
                            $pourcentage = ($event['capacite_max'] > 0) ? round(($event['nb_inscrits'] / $event['capacite_max']) * 100) : 0;
                            $classe_jauge = '';
                            if ($pourcentage < 60) $classe_jauge = 'vert';
                            elseif ($pourcentage >= 100) $classe_jauge = 'rouge';
                            ?>
                            <div class="jauge">
                                <div class="jauge-texte">
                                    <span><?php echo $event['nb_inscrits']; ?> / <?php echo $event['capacite_max']; ?> places</span>
                                    <span><?php echo ($pourcentage >= 100) ? 'Complet' : $pourcentage . '%'; ?></span>
                                </div>
                                <div class="jauge-barre">
                                    <div class="jauge-remplissage <?php echo $classe_jauge; ?>" style="width: <?php echo min($pourcentage, 100); ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <p class="aucun-event">Aucun événement trouvé.</p>
        <?php endif; ?>
    </div>

    <?php include_once('php/footer.php'); ?>

    <script src="js/menu.js"></script>
</body>
</html>
