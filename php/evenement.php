<?php
session_start();
require_once('connexion.php');
$racine = '../';

//on récupère l'id de l'événement
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id_event = (int)$_GET['id'];

//on récupère les infos de l'événement avec le nombre d'inscrits
$req = $bdd->prepare("SELECT e.*, u.prenom AS orga_prenom, u.nom AS orga_nom, u.nom_utilisateur AS orga_pseudo,
    (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
    FROM evenements e
    JOIN utilisateurs u ON e.organisateur_id = u.id
    WHERE e.id = ?");
$req->execute(array($id_event));
$event = $req->fetch(PDO::FETCH_ASSOC);

//si l'événement n'existe pas
if (!$event) {
    header('Location: ../index.php');
    exit();
}

//on vérifie si l'utilisateur est déjà inscrit et on récupère son code billet
$deja_inscrit = false;
$code_billet = null;
if (isset($_SESSION['id'])) {
    $verif = $bdd->prepare("SELECT id, code_billet FROM inscriptions WHERE utilisateur_id = ? AND evenement_id = ? AND statut = 'inscrit'");
    $verif->execute(array($_SESSION['id'], $id_event));
    $inscription = $verif->fetch(PDO::FETCH_ASSOC);
    if ($inscription) {
        $deja_inscrit = true;
        $code_billet = $inscription['code_billet'];
    }
}

$complet = ($event['nb_inscrits'] >= $event['capacite_max']);
$pourcentage = ($event['capacite_max'] > 0) ? round(($event['nb_inscrits'] / $event['capacite_max']) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['titre']); ?> - OmnesEvent</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/evenement.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>

    <?php include_once('menu.php'); ?>

    <div class="contenu-principal">
        <div class="event-detail">

            <?php if ($event['affiche']): ?>
                <img src="../uploads/<?php echo htmlspecialchars($event['affiche']); ?>" alt="<?php echo htmlspecialchars($event['titre']); ?>" class="event-affiche">
            <?php endif; ?>

            <div class="event-header">
    <span class="badge badge-<?php echo $event['categorie']; ?>">
        <?php echo ucfirst($event['categorie']); ?>
    </span>
    <h1><?php echo htmlspecialchars($event['titre']); ?></h1>

    <!-- Bouton favoris -->
    <button class="btn-favori" data-id="<?php echo $event['id']; ?>">
        ♡ Ajouter aux favoris
    </button>
</div>

            <!-- Boutons de gestion pour l'organisateur ou l'admin -->
            <?php if (isset($_SESSION['id']) && ($_SESSION['role'] === 'administrateur' || $event['organisateur_id'] == $_SESSION['id'])): ?>
                <div class="event-actions-orga">
                    <a href="modifier_evenement.php?id=<?php echo $event['id']; ?>" class="btn btn-outline btn-small">Modifier</a>
                    <a href="gerer_inscriptions.php?id=<?php echo $event['id']; ?>" class="btn btn-outline btn-small">Gérer les inscrits</a>
                    <form action="supprimer_evenement.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet événement ?');">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-small">Supprimer</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Messages de succès/erreur -->
            <?php if (isset($_GET['succes'])): ?>
                <div class="message-succes">
                    <?php
                    if ($_GET['succes'] === 'inscrit') echo 'Vous êtes bien inscrit à cet événement !';
                    if ($_GET['succes'] === 'annule') echo 'Votre inscription a bien été annulée.';
                    if ($_GET['succes'] === 'modifie') echo 'L\'événement a bien été modifié.';
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erreur'])): ?>
                <div class="message-erreur">
                    <?php
                    if ($_GET['erreur'] === 'complet') echo 'Désolé, cet événement est complet.';
                    if ($_GET['erreur'] === 'deja_inscrit') echo 'Vous êtes déjà inscrit à cet événement.';
                    if ($_GET['erreur'] === 'non_connecte') echo 'Vous devez être connecté pour vous inscrire.';
                    ?>
                </div>
            <?php endif; ?>

            <!-- Infos de l'événement -->
            <div class="event-infos">
                <div class="event-info-ligne">
                    <strong>Date</strong>
                    <span><?php echo date('d/m/Y', strtotime($event['date_event'])); ?></span>
                </div>
                <div class="event-info-ligne">
                    <strong>Heure</strong>
                    <span><?php echo date('H\hi', strtotime($event['heure'])); ?></span>
                </div>
                <div class="event-info-ligne">
                    <strong>Lieu</strong>
                    <span><?php echo htmlspecialchars($event['lieu']); ?></span>
                </div>
                <div class="event-info-ligne">
                    <strong>Catégorie</strong>
                    <span><?php echo ucfirst($event['categorie']); ?></span>
                </div>
                <?php if ($event['association']): ?>
                <div class="event-info-ligne">
                    <strong>Association</strong>
                    <span><?php echo htmlspecialchars($event['association']); ?></span>
                </div>
                <?php endif; ?>
            </div>


                        <!-- Carte du lieu -->
            <?php if (!empty($event['lieu'])): ?>
                <div class="event-carte">
                    <h3>Localisation</h3>

                    <p class="carte-lieu">
                        <?php echo htmlspecialchars($event['lieu']); ?>
                    </p>

                    <iframe
                        src="https://maps.google.com/maps?q=<?php echo urlencode($event['lieu']); ?>&output=embed"
                        width="100%"
                        height="300"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>

                    <a
                        href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($event['lieu']); ?>"
                        target="_blank"
                        class="btn btn-outline btn-small mt-20">
                        Ouvrir dans Google Maps
                    </a>
                </div>
            <?php endif; ?>

            <!-- Description -->
            <div class="event-description">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            </div>

            <!-- Section réservation -->
            <div class="event-reservation">
                <h3>Réservation</h3>

                <!-- Jauge -->
                <div class="jauge mb-20">
                    <div class="jauge-texte">
                        <span><?php echo $event['nb_inscrits']; ?> / <?php echo $event['capacite_max']; ?> places</span>
                        <span><?php echo $complet ? 'Complet' : $pourcentage . '%'; ?></span>
                    </div>
                    <div class="jauge-barre">
                        <div class="jauge-remplissage <?php echo $complet ? 'rouge' : ($pourcentage < 60 ? 'vert' : ''); ?>" style="width: <?php echo min($pourcentage, 100); ?>%;"></div>
                    </div>
                </div>

                <?php if (!isset($_SESSION['id'])): ?>
                    <p class="texte-muted">Vous devez être connecté pour vous inscrire.</p>
                    <a href="login.php" class="btn btn-primary mt-20">Se connecter</a>

                <?php elseif ($deja_inscrit): ?>
                    <p class="texte-muted">Vous êtes inscrit à cet événement.</p>

                    <!-- QR Code du billet -->
                    <?php if ($code_billet): ?>
                        <div class="qr-section">
                            <p class="qr-label">Votre billet :</p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($code_billet); ?>" alt="QR Code billet" class="qr-image">
                            <p class="qr-code-texte"><?php echo htmlspecialchars($code_billet); ?></p>
                            <a href="https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=<?php echo urlencode($code_billet); ?>" download="billet-<?php echo $code_billet; ?>.png" class="btn btn-outline btn-small mt-10">Télécharger le QR code</a>
                        </div>
                    <?php endif; ?>

                    <form action="annuler_inscription.php" method="POST">
                        <input type="hidden" name="evenement_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn btn-danger mt-20">Annuler mon inscription</button>
                    </form>

                <?php elseif ($complet): ?>
                    <p class="event-complet">Cet événement est complet.</p>

                    <?php else: ?>
    <?php $est_payant = isset($event['prix']) && $event['prix'] > 0; ?>

    <?php if ($est_payant): ?>
        <!-- Prix affiché -->
        <p class="event-prix">Prix : <strong><?php echo number_format($event['prix'], 2, ',', ''); ?> €</strong></p>

        <!-- Formulaire CB simulé -->
        <form action="inscription_event.php" method="POST" id="form-paiement">
            <input type="hidden" name="evenement_id" value="<?php echo $event['id']; ?>">

            <div class="form-group mt-20">
                <label>Numéro de carte</label>
                <input type="text" name="cb_numero" placeholder="1234 5678 9012 3456" maxlength="19" pattern="\d{4} \d{4} \d{4} \d{4}" required>
            </div>
            <div class="cb-ligne">
                <div class="form-group">
                    <label>Date d'expiration</label>
                    <input type="text" name="cb_expiration" placeholder="MM/AA" maxlength="5" pattern="\d{2}/\d{2}" required>
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" name="cb_cvv" placeholder="123" maxlength="3" pattern="\d{3}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-20">Payer <?php echo number_format($event['prix'], 2, ',', ''); ?> € et s'inscrire</button>
        </form>

        <script>
        document.querySelector('[name="cb_numero"]').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = v.match(/.{1,4}/g)?.join(' ') || v;
        });
        document.querySelector('[name="cb_expiration"]').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
            this.value = v;
        });
        </script>

    <?php else: ?>
        <!-- Événement gratuit -->
        <p class="event-prix gratuit">Événement gratuit</p>
        <form action="inscription_event.php" method="POST">
            <input type="hidden" name="evenement_id" value="<?php echo $event['id']; ?>">
            <button type="submit" class="btn btn-primary mt-20">S'inscrire</button>
        </form>
    <?php endif; ?>

<?php endif; ?>
            </div>

            <!-- Organisateur -->
            <div class="event-organisateur">
                Organisé par <strong><?php echo htmlspecialchars($event['orga_prenom'] . ' ' . $event['orga_nom']); ?></strong>
            </div>

        </div>
    </div>

    <?php include_once('footer.php'); ?>

    <script src="../js/menu.js"></script>
<script src="../js/favoris.js"></script>
</body>
</html>
