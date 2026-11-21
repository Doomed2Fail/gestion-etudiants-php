<?php
session_start();
define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('FILIERES', ['GI', 'IDIA', 'GE', 'GC']);
define('UTILISATEURS', ['admin' => '1234']);
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function connecte(): bool { return isset($_SESSION['login']); }
function exiger_connexion(): void { if (!connecte()) { header('Location: index.php'); exit; } }
function nettoyer(string $s): string { return trim(str_replace([';', "\r", "\n"], ' ', $s)); }
function lire_etudiants(): array {
    $liste = [];
    foreach (file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $ligne) {
        $c = explode(';', $ligne);
        if (count($c) < 6) continue;
        $id = (int) $c[0];
        $liste[$id] = ['id'=>$id, 'nom'=>$c[1], 'prenom'=>$c[2], 'filiere'=>$c[3], 'note'=>$c[4], 'photo'=>$c[5], 'email'=>$c[6] ?? ''];
    }
    return $liste;
}
function sauver_etudiants(array $liste): void {
    $lignes = [];
    foreach ($liste as $e) $lignes[] = implode(';', [$e['id'], $e['nom'], $e['prenom'], $e['filiere'], $e['note'], $e['photo'], $e['email']]);
    file_put_contents(FICHIER, $lignes ? implode("\n", $lignes) . "\n" : '', LOCK_EX);
}
function supprimer_photo(string $nom): void {
    if ($nom !== '' && basename($nom) === $nom && is_file(__DIR__ . '/uploads/' . $nom)) unlink(__DIR__ . '/uploads/' . $nom);
}
if (isset($_GET['theme']) && in_array($_GET['theme'], ['clair', 'sombre'], true)) {
    setcookie('theme', $_GET['theme'], time() + 30 * 86400, '/');
    $_COOKIE['theme'] = $_GET['theme'];
}
