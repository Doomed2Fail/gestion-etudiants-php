<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= h($titre ?? 'Gestion des étudiants') ?></title><link rel="stylesheet" href="style.css"></head>
<body class="<?= ($_COOKIE['theme'] ?? '') === 'sombre' ? 'sombre' : '' ?>">
<header><h1>Gestion des étudiants</h1><nav><?php if (connecte()): ?><a href="liste.php">Liste</a> <a href="ajouter.php">Ajouter</a> <a href="export.php">Exporter CSV</a> <a href="logout.php">Déconnexion</a><?php endif; ?> <a href="?theme=clair">Clair</a> <a href="?theme=sombre">Sombre</a></nav></header><main>
