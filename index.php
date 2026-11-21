<?php
require_once 'functions.php';
if (connecte()) { header('Location: liste.php'); exit; }
$login = $_COOKIE['dernier_user'] ?? '';
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        session_regenerate_id(true);
        $_SESSION['login'] = $login;
        setcookie('dernier_user', $login, time() + 7 * 86400, '/');
        header('Location: liste.php'); exit;
    }
    $erreur = 'Identifiants incorrects.';
}
$titre = 'Connexion'; include 'header.php';
?>
<div class="carte"><h2>Connexion</h2><?php if ($erreur): ?><p class="erreur"><?= h($erreur) ?></p><?php endif; ?>
<form method="post"><label>Login <input name="login" value="<?= h($login) ?>" required></label><label>Mot de passe <input type="password" name="mdp" required></label><button>Se connecter</button></form></div>
<?php include 'footer.php'; ?>
