//gestion du bouton favori sur la page événement
var boutonFavori = document.querySelector('.btn-favori');

if (boutonFavori) {
    var idEvenement = boutonFavori.dataset.id;
    var favoris = JSON.parse(localStorage.getItem('favoris')) || [];

    //si déjà en favori, on met le style actif
    if (favoris.includes(idEvenement)) {
        boutonFavori.classList.add('actif');
        boutonFavori.textContent = '♥ Retirer des favoris';
    }

    //au clic on ajoute ou retire des favoris
    boutonFavori.addEventListener('click', function() {
        favoris = JSON.parse(localStorage.getItem('favoris')) || [];

        if (favoris.includes(idEvenement)) {
            favoris = favoris.filter(function(id) { return id !== idEvenement; });
            boutonFavori.classList.remove('actif');
            boutonFavori.textContent = '♡ Ajouter aux favoris';
        } else {
            favoris.push(idEvenement);
            boutonFavori.classList.add('actif');
            boutonFavori.textContent = '♥ Retirer des favoris';
        }

        localStorage.setItem('favoris', JSON.stringify(favoris));
    });
}
