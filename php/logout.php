<?php
session_start();

//on vide et détruit la session
session_unset();
session_destroy();

//retour à l'accueil
header('Location: ../index.php');
exit();
?>
