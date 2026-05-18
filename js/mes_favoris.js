//on récupère les favoris stockés dans le navigateur
var favoris = JSON.parse(localStorage.getItem('favoris')) || [];
var cartes = document.querySelectorAll('.carte-favori');
var compteur = 0;

//on cache les cartes qui ne sont pas en favori
cartes.forEach(function(carte) {
    var idEvenement = carte.dataset.id;
    if (!favoris.includes(idEvenement)) {
        carte.style.display = 'none';
    } else {
        compteur++;
    }
});

//si aucun favori, on affiche le message
var messageAucun = document.getElementById('aucun-favori');
if (messageAucun) {
    messageAucun.style.display = (compteur === 0) ? 'block' : 'none';
}
