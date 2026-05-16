<?php
session_start();
require_once('connexion.php');

//vérification du rôle
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];

    //on vérifie que l'événement appartient bien à cet utilisateur
    $req = $bdd->prepare("SELECT organisateur_id FROM evenements WHERE id = ?");
    $req->execute(array($event_id));
    $event = $req->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header('Location: ../index.php');
        exit();
    }

    if ($_SESSION['role'] !== 'administrateur' && $event['organisateur_id'] != $_SESSION['id']) {
        header('Location: ../index.php');
        exit();
    }

    //on récupère les cases cochées (présents)
    $presents = isset($_POST['presents']) ? $_POST['presents'] : array();

    //on met tout le monde à absent d'abord
    $bdd->prepare("UPDATE inscriptions SET present = 0 WHERE evenement_id = ? AND statut = 'inscrit'")->execute(array($event_id));

    //puis on met à présent ceux qui sont cochés
    if (count($presents) > 0) {
        foreach ($presents as $user_id) {
            $user_id = (int)$user_id;
            $bdd->prepare("UPDATE inscriptions SET present = 1 WHERE evenement_id = ? AND utilisateur_id = ? AND statut = 'inscrit'")->execute(array($event_id, $user_id));
        }
    }

    header('Location: gerer_inscriptions.php?id=' . $event_id . '&succes=presence');
    exit();
}

header('Location: ../index.php');
exit();
?>
