<?php
session_start();
require_once('connexion.php');

//vérification du rôle
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'organisateur' && $_SESSION['role'] !== 'administrateur')) {
    header('Location: ../index.php');
    exit();
}

//vérification des champs obligatoires
if (isset($_POST['titre'], $_POST['description'], $_POST['date_event'], $_POST['heure'], $_POST['lieu'], $_POST['categorie'], $_POST['capacite_max'])) {

    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $date_event = $_POST['date_event'];
    $heure = $_POST['heure'];
    $lieu = htmlspecialchars($_POST['lieu']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $association = htmlspecialchars($_POST['association'] ?? '');
    $capacite_max = (int)$_POST['capacite_max'];
    $organisateur_id = $_SESSION['id'];

    //gestion de l'upload de l'affiche
    $nom_affiche = null;

    if (isset($_FILES['affiche']) && $_FILES['affiche']['error'] === UPLOAD_ERR_OK) {
        $extensions_ok = array('jpg', 'jpeg', 'png', 'webp');
        $nom_fichier = $_FILES['affiche']['name'];
        $extension = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

        //on vérifie que l'extension est autorisée
        if (!in_array($extension, $extensions_ok)) {
            header('Location: creer_evenement.php?erreur=image');
            exit();
        }

        //on génère un nom unique pour éviter les doublons
        $nom_affiche = uniqid('event_') . '.' . $extension;
        $chemin = '../uploads/' . $nom_affiche;

        move_uploaded_file($_FILES['affiche']['tmp_name'], $chemin);
    }

    try {
        $req = $bdd->prepare("INSERT INTO evenements (titre, description, date_event, heure, lieu, categorie, association, affiche, capacite_max, organisateur_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $req->execute(array($titre, $description, $date_event, $heure, $lieu, $categorie, $association, $nom_affiche, $capacite_max, $organisateur_id));

        //on récupère l'id de l'événement créé pour rediriger vers sa page
        $id_event = $bdd->lastInsertId();

        header('Location: evenement.php?id=' . $id_event);
        exit();

    } catch (Exception $e) {
        die('Erreur lors de la création : ' . $e->getMessage());
    }

} else {
    header('Location: creer_evenement.php?erreur=champs');
    exit();
}
?>
