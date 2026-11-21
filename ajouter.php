<?php
require_once 'functions.php';
exiger_connexion();

$liste = lire_etudiants();
$id = (int) ($_GET['id'] ?? 0);
$modification = $id > 0;

if ($modification && !isset($liste[$id])) {
    $_SESSION['flash'] = 'Étudiant introuvable.';
    header('Location: liste.php');
    exit;
}

// Pour une modification, on reprend les anciennes valeurs.
$etudiant = $modification ? $liste[$id] : [
    'nom' => '', 'prenom' => '', 'filiere' => 'GI',
    'note' => '', 'email' => '', 'photo' => ''
];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer les valeurs du formulaire.
    foreach (['nom', 'prenom', 'filiere', 'note', 'email'] as $champ) {
        $etudiant[$champ] = nettoyer($_POST[$champ] ?? '');
    }

    // 2. Vérifier les valeurs.
    if ($etudiant['nom'] === '') $erreurs[] = 'Le nom est obligatoire.';
    if ($etudiant['prenom'] === '') $erreurs[] = 'Le prénom est obligatoire.';
    if (!in_array($etudiant['filiere'], FILIERES, true)) $erreurs[] = 'Filière invalide.';
    if (!is_numeric($etudiant['note']) || $etudiant['note'] < 0 || $etudiant['note'] > 20) {
        $erreurs[] = 'La note doit être comprise entre 0 et 20.';
    }
    if ($etudiant['email'] !== '' && !filter_var($etudiant['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'E-mail invalide.';
    }

    foreach ($liste as $autre) {
        if ($autre['id'] !== $id && strcasecmp($autre['nom'], $etudiant['nom']) === 0
            && strcasecmp($autre['prenom'], $etudiant['prenom']) === 0) {
            $erreurs[] = 'Cet étudiant existe déjà.';
        }
    }

    // 3. Vérifier la photo facultative.
    $photo = $_FILES['photo'] ?? null;
    $nouvellePhoto = $photo && $photo['error'] !== UPLOAD_ERR_NO_FILE;
    if ($nouvellePhoto) {
        $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        if ($photo['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = 'Erreur lors de l’envoi de la photo.';
        } else {
            if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) $erreurs[] = 'Photo JPG ou PNG uniquement.';
            if ($photo['size'] > 1024 * 1024) $erreurs[] = 'La photo dépasse 1 Mo.';
            if (!@getimagesize($photo['tmp_name'])) $erreurs[] = 'La photo doit être une image valide.';
        }
    }

    // 4. Enregistrer la photo seulement si les données sont valides.
    if (!$erreurs && $nouvellePhoto) {
        $nomPhoto = uniqid('etu_') . '.' . $extension;
        if (move_uploaded_file($photo['tmp_name'], __DIR__ . '/uploads/' . $nomPhoto)) {
            supprimer_photo($etudiant['photo']);
            $etudiant['photo'] = $nomPhoto;
        } else {
            $erreurs[] = 'Impossible d’enregistrer la photo.';
        }
    }

    // 5. Ajouter ou remplacer l’étudiant, puis revenir à la liste.
    if (!$erreurs) {
        if (!$modification) $id = $liste ? max(array_keys($liste)) + 1 : 1;
        $etudiant['id'] = $id;
        $liste[$id] = $etudiant;
        sauver_etudiants($liste);
        $_SESSION['flash'] = 'Étudiant enregistré avec succès.';
        header('Location: liste.php');
        exit;
    }
}

$titre = $modification ? 'Modifier' : 'Ajouter';
include 'header.php';
?>
<div class="carte">
    <h2><?= h($titre) ?> un étudiant</h2>

    <?php foreach ($erreurs as $erreur): ?>
        <p class="erreur"><?= h($erreur) ?></p>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Nom <input name="nom" value="<?= h($etudiant['nom']) ?>" required></label>
        <label>Prénom <input name="prenom" value="<?= h($etudiant['prenom']) ?>" required></label>
        <label>Filière
            <select name="filiere">
                <?php foreach (FILIERES as $filiere): ?>
                    <option value="<?= h($filiere) ?>" <?= $etudiant['filiere'] === $filiere ? 'selected' : '' ?>>
                        <?= h($filiere) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Note <input type="number" name="note" min="0" max="20" step="0.01" value="<?= h((string) $etudiant['note']) ?>" required></label>
        <label>E-mail (facultatif) <input type="email" name="email" value="<?= h($etudiant['email']) ?>"></label>
        <label>Photo (JPG ou PNG, 1 Mo maximum) <input type="file" name="photo" accept=".jpg,.jpeg,.png"></label>
        <button>Enregistrer</button>
        <a href="liste.php">Annuler</a>
    </form>
</div>
<?php include 'footer.php'; ?>
