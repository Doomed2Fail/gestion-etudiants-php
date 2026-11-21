<?php
require_once 'functions.php'; exiger_connexion();
$id = (int) ($_GET['id'] ?? 0); $liste = lire_etudiants();
if (isset($liste[$id])) { supprimer_photo($liste[$id]['photo']); unset($liste[$id]); sauver_etudiants($liste); $_SESSION['flash'] = 'Étudiant supprimé.'; }
header('Location: liste.php'); exit;
