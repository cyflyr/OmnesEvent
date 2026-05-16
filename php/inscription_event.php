<?php
session_start();
require_once('connexion.php');

//l'utilisateur doit être connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['evenement_id'])) {
    $event_id = (int)$_POST['evenement_id'];
    $user_id = $_SESSION['id'];

    //on vérifie que l'événement existe et qu'il reste des places
    $req = $bdd->prepare("SELECT e.capacite_max,
        (SELECT COUNT(*) FROM inscriptions i WHERE i.evenement_id = e.id AND i.statut = 'inscrit') AS nb_inscrits
        FROM evenements e WHERE e.id = ?");
    $req->execute(array($event_id));
    $event = $req->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header('Location: ../index.php');
        exit();
    }

    //vérification : l'événement est complet ?
    if ($event['nb_inscrits'] >= $event['capacite_max']) {
        header('Location: evenement.php?id=' . $event_id . '&erreur=complet');
        exit();
    }

    //vérification : déjà inscrit ?
    $verif = $bdd->prepare("SELECT id FROM inscriptions WHERE utilisateur_id = ? AND evenement_id = ? AND statut = 'inscrit'");
    $verif->execute(array($user_id, $event_id));

    if ($verif->fetch()) {
        header('Location: evenement.php?id=' . $event_id . '&erreur=deja_inscrit');
        exit();
    }

    //on vérifie si l'utilisateur avait annulé une inscription précédente
    $ancien = $bdd->prepare("SELECT id FROM inscriptions WHERE utilisateur_id = ? AND evenement_id = ? AND statut = 'annule'");
    $ancien->execute(array($user_id, $event_id));
    $ancienne_inscr = $ancien->fetch();

    //on génère un code unique pour le billet
    $code_billet = 'OMNES-' . $user_id . '-' . $event_id . '-' . substr(uniqid(), -8);

    if ($ancienne_inscr) {
        //on réactive l'ancienne inscription avec un nouveau code billet
        $update = $bdd->prepare("UPDATE inscriptions SET statut = 'inscrit', date_inscription = NOW(), code_billet = ? WHERE id = ?");
        $update->execute(array($code_billet, $ancienne_inscr['id']));
    } else {
        //nouvelle inscription avec code billet
        $insert = $bdd->prepare("INSERT INTO inscriptions (utilisateur_id, evenement_id, code_billet) VALUES (?, ?, ?)");
        $insert->execute(array($user_id, $event_id, $code_billet));
    }

    header('Location: evenement.php?id=' . $event_id . '&succes=inscrit');
    exit();

} else {
    header('Location: ../index.php');
    exit();
}
?>
