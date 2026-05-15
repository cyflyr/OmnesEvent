//gestion des onglets sur la page profil
document.addEventListener('DOMContentLoaded', function() {
    var onglets = document.querySelectorAll('.onglet');
    var contenus = document.querySelectorAll('.contenu-onglet');

    onglets.forEach(function(onglet) {
        onglet.addEventListener('click', function() {
            var cible = this.getAttribute('data-onglet');

            //on retire la classe actif de tous les onglets
            onglets.forEach(function(o) {
                o.classList.remove('actif');
            });

            //on cache tous les contenus
            contenus.forEach(function(c) {
                c.classList.remove('visible');
            });

            //on active l'onglet cliqué et son contenu
            this.classList.add('actif');
            document.getElementById('onglet-' + cible).classList.add('visible');
        });
    });
});
