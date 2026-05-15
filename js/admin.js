//gestion des onglets sur la page admin
document.addEventListener('DOMContentLoaded', function() {
    var onglets = document.querySelectorAll('.admin-onglet');
    var sections = document.querySelectorAll('.admin-section');

    onglets.forEach(function(onglet) {
        onglet.addEventListener('click', function() {
            var cible = this.getAttribute('data-section');

            //on retire la classe actif de tous les onglets
            onglets.forEach(function(o) {
                o.classList.remove('actif');
            });

            //on cache toutes les sections
            sections.forEach(function(s) {
                s.classList.remove('visible');
            });

            //on active l'onglet cliqué et sa section
            this.classList.add('actif');
            document.getElementById('section-' + cible).classList.add('visible');
        });
    });
});
