<?php
session_start();
require_once('connexion.php');

// variable pour les chemins car on est dans le dossier php
$racine = '../';

// Récupération du mois affiché
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('Y-m');

// Sécurité : si le format n'est pas bon, on remet le mois actuel
if (!preg_match('/^\d{4}-\d{2}$/', $mois)) {
    $mois = date('Y-m');
}

$premierJour = DateTime::createFromFormat('Y-m-d', $mois . '-01');
$debutMois = $premierJour->format('Y-m-01');
$finMois = $premierJour->format('Y-m-t');

$moisPrecedent = (clone $premierJour)->modify('-1 month')->format('Y-m');
$moisSuivant = (clone $premierJour)->modify('+1 month')->format('Y-m');

$nomMois = [
    1 => 'Janvier',
    2 => 'Février',
    3 => 'Mars',
    4 => 'Avril',
    5 => 'Mai',
    6 => 'Juin',
    7 => 'Juillet',
    8 => 'Août',
    9 => 'Septembre',
    10 => 'Octobre',
    11 => 'Novembre',
    12 => 'Décembre'
];

$titreMois = $nomMois[(int)$premierJour->format('n')] . ' ' . $premierJour->format('Y');

// Récupération des événements du mois
$sql = "SELECT e.*, u.prenom AS orga_prenom, u.nom AS orga_nom,
        (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
        FROM evenements e
        JOIN utilisateurs u ON e.organisateur_id = u.id
        WHERE e.date_event BETWEEN ? AND ?
        ORDER BY e.date_event ASC, e.heure ASC";

$req = $bdd->prepare($sql);
$req->execute([$debutMois, $finMois]);
$evenements = $req->fetchAll(PDO::FETCH_ASSOC);

// On range les événements par date
$eventsParDate = [];

foreach ($evenements as $event) {
    $date = $event['date_event'];

    if (!isset($eventsParDate[$date])) {
        $eventsParDate[$date] = [];
    }

    $eventsParDate[$date][] = $event;
}

// Infos pour construire le calendrier
$nbJoursMois = (int)$premierJour->format('t');
$jourSemaineDebut = (int)$premierJour->format('N'); // 1 = lundi, 7 = dimanche
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - OmnesEvent</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/calendrier.css">
</head>
<body>

<?php include_once('menu.php'); ?>

<div class="contenu-principal">

    <section class="calendrier-hero">
        <h1>Calendrier des événements</h1>
        <p>Explorez les événements Omnes par mois et cliquez sur un jour pour voir les détails.</p>
    </section>

    <section class="calendrier-container">

        <div class="calendrier-header">
            <a href="calendrier.php?mois=<?php echo $moisPrecedent; ?>" class="btn-mois">← Mois précédent</a>

            <h2><?php echo $titreMois; ?></h2>

            <a href="calendrier.php?mois=<?php echo $moisSuivant; ?>" class="btn-mois">Mois suivant →</a>
        </div>

        <div class="jours-semaine">
            <div>Lun</div>
            <div>Mar</div>
            <div>Mer</div>
            <div>Jeu</div>
            <div>Ven</div>
            <div>Sam</div>
            <div>Dim</div>
        </div>

        <div class="calendrier-grille">

            <?php for ($i = 1; $i < $jourSemaineDebut; $i++): ?>
                <div class="case-calendrier case-vide"></div>
            <?php endfor; ?>

            <?php for ($jour = 1; $jour <= $nbJoursMois; $jour++): ?>
                <?php
                $dateComplete = $premierJour->format('Y-m') . '-' . str_pad($jour, 2, '0', STR_PAD_LEFT);
                $aEvenement = isset($eventsParDate[$dateComplete]);
                $estAujourdhui = ($dateComplete === date('Y-m-d'));
                ?>

                <?php
$imageCase = '';

if ($aEvenement && !empty($eventsParDate[$dateComplete][0]['affiche'])) {
    $imageCase = '../uploads/' . htmlspecialchars($eventsParDate[$dateComplete][0]['affiche']);
}
?>

<div class="case-calendrier <?php echo $aEvenement ? 'avec-event' : ''; ?> <?php echo $estAujourdhui ? 'aujourdhui' : ''; ?>"
     onclick="ouvrirJour('<?php echo $dateComplete; ?>')">

    <?php if ($imageCase !== ''): ?>
        <img src="<?php echo $imageCase; ?>" alt="Affiche événement" class="image-event-calendrier">
    <?php endif; ?>

    <div class="overlay-calendrier"></div>

    <div class="numero-jour"><?php echo $jour; ?></div>

    <?php if ($aEvenement): ?>
        <div class="pastille-event">
            <?php echo count($eventsParDate[$dateComplete]); ?> event<?php echo count($eventsParDate[$dateComplete]) > 1 ? 's' : ''; ?>
        </div>
    <?php endif; ?>

</div>
            <?php endfor; ?>

        </div>
    </section>

    <section class="details-jours">

        <?php if (count($evenements) > 0): ?>

            <?php foreach ($eventsParDate as $date => $events): ?>
                <div class="bloc-jour" id="jour-<?php echo $date; ?>">
                    <h3>
                        <?php echo date('d/m/Y', strtotime($date)); ?>
                    </h3>

                    <div class="liste-events-calendrier">
                        <?php foreach ($events as $event): ?>
                            <?php
                            $placesRestantes = $event['capacite_max'] - $event['nb_inscrits'];
                            ?>

                            <a href="evenement.php?id=<?php echo $event['id']; ?>" class="event-calendrier">
                                <div>
                                    <span class="badge-calendrier badge-<?php echo htmlspecialchars($event['categorie']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($event['categorie'])); ?>
                                    </span>

                                    <h4><?php echo htmlspecialchars($event['titre']); ?></h4>

                                    <p>
                                        <?php echo date('H\hi', strtotime($event['heure'])); ?>
                                        — <?php echo htmlspecialchars($event['lieu']); ?>
                                    </p>

                                    <p>
                                        Organisé par <?php echo htmlspecialchars($event['association']); ?>
                                    </p>
                                </div>

                                <div class="places-calendrier">
                                    <?php if ($placesRestantes <= 0): ?>
                                        <span class="complet">Complet</span>
                                    <?php else: ?>
                                        <span><?php echo $placesRestantes; ?> place<?php echo $placesRestantes > 1 ? 's' : ''; ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p class="aucun-event">Aucun événement prévu ce mois-ci.</p>
        <?php endif; ?>

    </section>

</div>

<?php include_once('footer.php'); ?>

<script src="../js/menu.js"></script>

<script>
function ouvrirJour(date) {
    const bloc = document.getElementById('jour-' + date);

    if (bloc) {
        document.querySelectorAll('.bloc-jour').forEach(function(element) {
            element.classList.remove('bloc-actif');
        });

        bloc.classList.add('bloc-actif');
        bloc.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}
</script>

</body>
</html>