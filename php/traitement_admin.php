<?php
session_start();
require_once('connexion.php');

//seul l'admin peut utiliser cette page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    //valider un organisateur
    if ($action === 'valider_orga' && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
        $req = $bdd->prepare("UPDATE utilisateurs SET valide = 1 WHERE id = ? AND role = 'organisateur'");
        $req->execute(array($user_id));

        header('Location: admin.php?succes=valide');
        exit();
    }

    //supprimer un utilisateur
    if ($action === 'supprimer_user' && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];

        //on ne supprime pas son propre compte
        if ($user_id == $_SESSION['id']) {
            header('Location: admin.php');
            exit();
        }

        //on supprime d'abord ses inscriptions
        $bdd->prepare("DELETE FROM inscriptions WHERE utilisateur_id = ?")->execute(array($user_id));

        //puis ses événements (et les inscriptions liées)
        $events = $bdd->prepare("SELECT id FROM evenements WHERE organisateur_id = ?");
        $events->execute(array($user_id));
        while ($ev = $events->fetch()) {
            $bdd->prepare("DELETE FROM inscriptions WHERE evenement_id = ?")->execute(array($ev['id']));
        }
        $bdd->prepare("DELETE FROM evenements WHERE organisateur_id = ?")->execute(array($user_id));

        //enfin on supprime l'utilisateur
        $bdd->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute(array($user_id));

        header('Location: admin.php?succes=supprime_user');
        exit();
    }

    //supprimer un événement
    if ($action === 'supprimer_event' && isset($_POST['event_id'])) {
        $event_id = (int)$_POST['event_id'];

        //on supprime les inscriptions liées puis l'événement
        $bdd->prepare("DELETE FROM inscriptions WHERE evenement_id = ?")->execute(array($event_id));
        $bdd->prepare("DELETE FROM evenements WHERE id = ?")->execute(array($event_id));

        header('Location: admin.php?succes=supprime_event');
        exit();
    }
}

header('Location: admin.php');
exit();
?>
