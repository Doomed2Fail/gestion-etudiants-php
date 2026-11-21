document.querySelectorAll('.supprimer').forEach(function (lien) {
    lien.addEventListener('click', function (event) {
        if (!confirm('Voulez-vous vraiment supprimer cet étudiant ?')) event.preventDefault();
    });
});
const recherche = document.querySelector('#recherche');
if (recherche) recherche.addEventListener('input', function () {
    const texte = recherche.value.toLowerCase();
    document.querySelectorAll('.etudiant').forEach(function (ligne) {
        ligne.style.display = ligne.dataset.nom.toLowerCase().includes(texte) ? '' : 'none';
    });
});
