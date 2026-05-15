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

    //on passe le statut de l'inscription à "annule"
    $req = $bdd->prepare("UPDATE inscriptions SET statut = 'annule' WHERE utilisateur_id = ? AND evenement_id = ? AND statut = 'inscrit'");
    $req->execute(array($user_id, $event_id));

    header('Location: evenement.php?id=' . $event_id . '&succes=annule');
    exit();

} else {
    header('Location: ../index.php');
    exit();
}
?>
