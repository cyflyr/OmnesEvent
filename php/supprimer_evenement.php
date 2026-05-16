<?php
session_start();
require_once('connexion.php');

//seuls les organisateurs et admins peuvent supprimer
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];

    //on récupère l'événement pour vérifier le propriétaire
    $req = $bdd->prepare("SELECT organisateur_id FROM evenements WHERE id = ?");
    $req->execute(array($event_id));
    $event = $req->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header('Location: ../index.php');
        exit();
    }

    //un organisateur ne peut supprimer que ses propres événements
    if ($_SESSION['role'] !== 'administrateur' && $event['organisateur_id'] != $_SESSION['id']) {
        header('Location: ../index.php');
        exit();
    }

    //on supprime d'abord les inscriptions liées puis l'événement
    $bdd->prepare("DELETE FROM inscriptions WHERE evenement_id = ?")->execute(array($event_id));
    $bdd->prepare("DELETE FROM evenements WHERE id = ?")->execute(array($event_id));

    header('Location: profil.php?succes=supprime');
    exit();
}

header('Location: ../index.php');
exit();
?>
