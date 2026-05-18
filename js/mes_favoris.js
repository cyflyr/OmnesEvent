// On récupère les favoris enregistrés dans le navigateur
let favoris = JSON.parse(localStorage.getItem('favoris')) || [];

// On récupère toutes les cartes de la page favoris
let cartes = document.querySelectorAll('.carte-favori');

let compteur = 0;

cartes.forEach(function(carte) {
    let idEvenement = carte.dataset.id;

    // Si l'événement n'est pas dans les favoris, on cache sa carte
    if (!favoris.includes(idEvenement)) {
        carte.style.display = 'none';
    } else {
        compteur++;
    }
});

// Si aucun événement favori n'est trouvé, on affiche le message
let messageAucun = document.getElementById('aucun-favori');

if (messageAucun) {
    if (compteur === 0) {
        messageAucun.style.display = 'block';
    } else {
        messageAucun.style.display = 'none';
    }
}