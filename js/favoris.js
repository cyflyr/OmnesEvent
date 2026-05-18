// Fonctionnalité favoris simple avec localStorage
// Le localStorage permet de garder les favoris dans le navigateur

let boutonFavori = document.querySelector('.btn-favori');

if (boutonFavori) {
    let idEvenement = boutonFavori.dataset.id;

    // On récupère les favoris déjà enregistrés
    let favoris = JSON.parse(localStorage.getItem('favoris')) || [];

    // Si cet événement est déjà dans les favoris, on change le texte du bouton
    if (favoris.includes(idEvenement)) {
        boutonFavori.classList.add('actif');
        boutonFavori.textContent = '♥ Retirer des favoris';
    }

    boutonFavori.addEventListener('click', function() {
        favoris = JSON.parse(localStorage.getItem('favoris')) || [];

        if (favoris.includes(idEvenement)) {
            // Retirer des favoris
            favoris = favoris.filter(function(id) {
                return id !== idEvenement;
            });

            boutonFavori.classList.remove('actif');
            boutonFavori.textContent = '♡ Ajouter aux favoris';
        } else {
            // Ajouter aux favoris
            favoris.push(idEvenement);

            boutonFavori.classList.add('actif');
            boutonFavori.textContent = '♥ Retirer des favoris';
        }

        // On sauvegarde la nouvelle liste dans le navigateur
        localStorage.setItem('favoris', JSON.stringify(favoris));
    });
}