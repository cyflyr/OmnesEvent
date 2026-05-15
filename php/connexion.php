<?php
try {
    //connexion à la base omnesevent
    $bdd = new PDO(
        'mysql:host=localhost;dbname=omnesevent;charset=utf8mb4',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
?>
