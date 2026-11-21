<?php
require_once 'functions.php'; exiger_connexion();
$q = trim($_GET['q'] ?? ''); $tri = $_GET['tri'] ?? 'ajout';
if (!in_array($tri, ['ajout','nom','filiere','note'], true)) $tri = 'ajout';
$liste = array_values(array_filter(lire_etudiants(), function($e) use ($q) { return stripos($e['nom'], $q) !== false || stripos($e['prenom'], $q) !== false; }));
if ($tri !== 'ajout') usort($liste, function($a,$b) use ($tri) { if ($tri === 'note') return (float)$b['note'] <=> (float)$a['note']; return strcasecmp($a[$tri], $b[$tri]); });
$notes = array_column($liste, 'note'); $nombre = count($liste);
$moyennes = []; foreach ($liste as $e) { $moyennes[$e['filiere']][] = (float)$e['note']; }
$titre = 'Liste des étudiants'; include 'header.php';
?>
<h2>Liste des étudiants</h2>
<?php if (isset($_SESSION['flash'])): ?><p class="succes"><?= h($_SESSION['flash']) ?></p><?php unset($_SESSION['flash']); endif; ?>
<form method="get" class="recherche"><label>Recherche <input id="recherche" name="q" value="<?= h($q) ?>" placeholder="Nom ou prénom"></label><label>Tri <select name="tri"><?php foreach (['ajout'=>'Ordre d’ajout','nom'=>'Nom','filiere'=>'Filière','note'=>'Note décroissante'] as $val=>$lib): ?><option value="<?= $val ?>" <?= $tri === $val ? 'selected' : '' ?>><?= h($lib) ?></option><?php endforeach; ?></select></label><button>Rechercher</button></form>
<p>Étudiants affichés : <?= $nombre ?> | Moyenne : <?= $nombre ? number_format(array_sum($notes)/$nombre, 2) : '—' ?> | Meilleure note : <?= $nombre ? h((string)max($notes)) : '—' ?></p>
<p>Moyennes par filière : <?php if (!$moyennes) echo '—'; foreach ($moyennes as $f=>$n) echo h($f) . ' : ' . number_format(array_sum($n)/count($n), 2) . ' &nbsp; '; ?></p>
<div class="table-wrap"><table><thead><tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Note</th><th>E-mail</th><th>Actions</th></tr></thead><tbody>
<?php if (!$liste): ?><tr><td colspan="7">Aucun étudiant</td></tr><?php endif; ?>
<?php foreach ($liste as $e): ?><tr class="etudiant" data-nom="<?= h($e['nom'].' '.$e['prenom']) ?>"><td><?php if ($e['photo']): ?><img class="photo" src="uploads/<?= h(rawurlencode($e['photo'])) ?>" alt="Photo"><?php else: ?>—<?php endif; ?></td><td><?= h(strtoupper($e['nom'])) ?></td><td><?= h(ucfirst($e['prenom'])) ?></td><td><?= h($e['filiere']) ?></td><td class="<?= (float)$e['note'] >= 10 ? 'vert' : 'rouge' ?>"><?= h((string)$e['note']) ?></td><td><?= h($e['email']) ?></td><td><a href="ajouter.php?id=<?= $e['id'] ?>">Modifier</a> <a class="supprimer" href="supprimer.php?id=<?= $e['id'] ?>">Supprimer</a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include 'footer.php'; ?>
