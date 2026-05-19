<?php
try {
    $host = 'fdb1031.your-hosting.net';
    $nom_base = '4760740_omneseventing2g3d';
    $utilisateur = '4760740_omneseventing2g3d';
    $mot_de_passe = 'azerty123';

    $bdd = new PDO('mysql:host=' . $host . ';port=3306;dbname=' . $nom_base . ';charset=utf8', $utilisateur, $mot_de_passe,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
