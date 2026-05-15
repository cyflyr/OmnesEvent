<?php
session_start();
require_once('connexion.php');

//on vérifie que le formulaire a bien été soumis
if (isset($_POST['nom_utilisateur']) && isset($_POST['password'])) {

    $nom_u = htmlspecialchars($_POST['nom_utilisateur']);
    $mdp_tape = $_POST['password'];

    //on cherche l'utilisateur dans la base
    $req = $bdd->prepare('SELECT * FROM utilisateurs WHERE nom_utilisateur = ?');
    $req->execute(array($nom_u));
    $utilisateur = $req->fetch();

    //on vérifie le mot de passe et que le compte est validé
    if ($utilisateur && password_verify($mdp_tape, $utilisateur['password'])) {

        //si c'est un organisateur non validé par l'admin
        if ($utilisateur['role'] === 'organisateur' && $utilisateur['valide'] == 0) {
            header('Location: login.php?erreur=non_valide');
            exit();
        }

        //connexion réussie, on régénère la session
        session_regenerate_id();

        $_SESSION['id'] = $utilisateur['id'];
        $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['role'] = $utilisateur['role'];

        //redirection vers l'accueil
        header('Location: ../index.php');
        exit();

    } else {
        header('Location: login.php?erreur=1');
        exit();
    }

} else {
    header('Location: login.php');
    exit();
}
?>
