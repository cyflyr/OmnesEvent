//gestion du menu hamburger pour mobile
document.addEventListener('DOMContentLoaded', function() {
    var btnHamburger = document.getElementById('btn-hamburger');
    var menuMobile = document.getElementById('menu-mobile');

    if (btnHamburger && menuMobile) {
        btnHamburger.addEventListener('click', function() {
            menuMobile.classList.toggle('visible');
        });
    }
});
