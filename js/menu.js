//gestion du menu hamburger pour mobile
document.addEventListener('DOMContentLoaded', function() {
    var btnHamburger = document.getElementById('btn-hamburger');
    var menuMobile = document.getElementById('menu-mobile');

    if (btnHamburger && menuMobile) {
        btnHamburger.addEventListener('click', function() {
            menuMobile.classList.toggle('visible');
        });
    }

    //gestion du menu déroulant profil
    var btnProfil = document.getElementById('btn-profil');
    var menuProfil = document.getElementById('menu-profil');

    if (btnProfil && menuProfil) {
        btnProfil.addEventListener('click', function(e) {
            e.stopPropagation();
            menuProfil.classList.toggle('visible');
        });

        //on ferme le menu si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!menuProfil.contains(e.target)) {
                menuProfil.classList.remove('visible');
            }
        });
    }
});
