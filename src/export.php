<?php
require_once 'functions.php'; exiger_connexion();
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="etudiants.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','nom','prenom','filiere','note','photo','email'], ';', '"', '');
foreach (lire_etudiants() as $e) fputcsv($out, [$e['id'],$e['nom'],$e['prenom'],$e['filiere'],$e['note'],$e['photo'],$e['email']], ';', '"', '');
fclose($out); exit;
