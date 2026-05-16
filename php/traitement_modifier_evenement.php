<?php
session_start();
require_once('connexion.php');

//vérification du rôle
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['id'], $_POST['titre'], $_POST['description'], $_POST['date_event'], $_POST['heure'], $_POST['lieu'], $_POST['categorie'], $_POST['capacite_max'])) {

    $id_event = (int)$_POST['id'];

    //on vérifie que l'événement existe et appartient bien à cet utilisateur
    $verif = $bdd->prepare("SELECT * FROM evenements WHERE id = ?");
    $verif->execute(array($id_event));
    $event = $verif->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        header('Location: ../index.php');
        exit();
    }

    if ($_SESSION['role'] !== 'administrateur' && $event['organisateur_id'] != $_SESSION['id']) {
        header('Location: ../index.php');
        exit();
    }

    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_event = $_POST['date_event'];
    $heure = $_POST['heure'];
    $lieu = trim($_POST['lieu']);
    $categorie = $_POST['categorie'];
    $association = trim($_POST['association'] ?? '');
    $capacite_max = (int)$_POST['capacite_max'];

    //gestion de la nouvelle affiche si on en uploade une
    $nom_affiche = $event['affiche'];

    if (isset($_FILES['affiche']) && $_FILES['affiche']['error'] === UPLOAD_ERR_OK) {
        $extensions_ok = array('jpg', 'jpeg', 'png', 'webp');
        $nom_fichier = $_FILES['affiche']['name'];
        $extension = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensions_ok)) {
            header('Location: modifier_evenement.php?id=' . $id_event . '&erreur=image');
            exit();
        }

        $nom_affiche = uniqid('event_') . '.' . $extension;
        $chemin = '../uploads/' . $nom_affiche;
        move_uploaded_file($_FILES['affiche']['tmp_name'], $chemin);
    }

    try {
        $req = $bdd->prepare("UPDATE evenements SET titre = ?, description = ?, date_event = ?, heure = ?, lieu = ?, categorie = ?, association = ?, affiche = ?, capacite_max = ? WHERE id = ?");
        $req->execute(array($titre, $description, $date_event, $heure, $lieu, $categorie, $association, $nom_affiche, $capacite_max, $id_event));

        header('Location: evenement.php?id=' . $id_event . '&succes=modifie');
        exit();

    } catch (Exception $e) {
        die('Erreur lors de la modification : ' . $e->getMessage());
    }

} else {
    header('Location: ../index.php');
    exit();
}
?>
