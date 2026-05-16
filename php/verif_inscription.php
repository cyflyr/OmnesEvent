<?php
session_start();
require_once('connexion.php');

//on vérifie que tous les champs sont remplis
if (isset($_POST['prenom'], $_POST['nom'], $_POST['email'], $_POST['nom_utilisateur'], $_POST['password'], $_POST['password_confirmation'], $_POST['role'])) {

    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $nom_u = trim($_POST['nom_utilisateur']);
    $role = $_POST['role'];

    $mdp = $_POST['password'];
    $mdp_conf = $_POST['password_confirmation'];

    //les mots de passe doivent correspondre
    if ($mdp !== $mdp_conf) {
        header('Location: inscription.php?erreur=mdp');
        exit();
    }

    //on vérifie que le nom d'utilisateur n'existe pas déjà
    $verif = $bdd->prepare('SELECT id FROM utilisateurs WHERE nom_utilisateur = ?');
    $verif->execute(array($nom_u));

    if ($verif->fetch()) {
        header('Location: inscription.php?erreur=utilisateur_existe');
        exit();
    }

    //on hash le mot de passe
    $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);

    //les organisateurs doivent être validés par l'admin
    $valide = ($role === 'organisateur') ? 0 : 1;

    //on s'assure que le rôle est valide
    if ($role !== 'participant' && $role !== 'organisateur') {
        $role = 'participant';
    }

    try {
        $req = $bdd->prepare('INSERT INTO utilisateurs (nom_utilisateur, password, prenom, nom, email, role, valide) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $req->execute(array($nom_u, $mdp_hache, $prenom, $nom, $email, $role, $valide));

        header('Location: login.php?inscription=reussie');
        exit();

    } catch (Exception $e) {
        die('Erreur lors de l\'inscription : ' . $e->getMessage());
    }

} else {
    header('Location: inscription.php?erreur=champs');
    exit();
}
?>
