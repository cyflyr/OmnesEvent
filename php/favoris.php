<?php
session_start();
require_once('connexion.php');
$racine = '../';

// On récupère tous les événements à venir
$req = $bdd->prepare("
    SELECT e.*, u.prenom AS orga_prenom, u.nom AS orga_nom,
    (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
    FROM evenements e
    JOIN utilisateurs u ON e.organisateur_id = u.id
    WHERE e.date_event >= CURDATE()
    ORDER BY e.date_event ASC
");
$req->execute();
$evenements = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes favoris - OmnesEvent</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/accueil.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>

<body>

<?php include_once('menu.php'); ?>

<div class="contenu-principal">

    <h1 class="section-titre">Mes favoris</h1>

    <p class="texte-favoris">
        Voici les événements que vous avez ajoutés à vos favoris.
    </p>

    <section class="grille-events">

        <?php foreach ($evenements as $event): ?>

            <a href="evenement.php?id=<?php echo $event['id']; ?>" 
               class="carte-event carte-favori" 
               data-id="<?php echo $event['id']; ?>">

                <?php if ($event['affiche']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($event['affiche']); ?>" 
                         alt="<?php echo htmlspecialchars($event['titre']); ?>" 
                         class="carte-event-image">
                <?php else: ?>
                    <div class="carte-event-image carte-event-placeholder"></div>
                <?php endif; ?>

                <div class="carte-event-body">
                    <span class="badge badge-<?php echo $event['categorie']; ?>">
                        <?php echo ucfirst($event['categorie']); ?>
                    </span>

                    <h3 class="carte-event-titre">
                        <?php echo htmlspecialchars($event['titre']); ?>
                    </h3>

                    <p class="carte-event-info">
                        <?php echo date('d/m/Y', strtotime($event['date_event'])); ?> 
                        à <?php echo date('H\hi', strtotime($event['heure'])); ?>
                    </p>

                    <p class="carte-event-info">
                        <?php echo htmlspecialchars($event['lieu']); ?>
                    </p>
                </div>
            </a>

        <?php endforeach; ?>

    </section>

    <p id="aucun-favori" class="aucun-event">
        Vous n'avez pas encore ajouté d'événement aux favoris.
    </p>

</div>

<?php include_once('footer.php'); ?>

<script src="../js/menu.js"></script>
<script src="../js/mes_favoris.js"></script>

</body>
</html>