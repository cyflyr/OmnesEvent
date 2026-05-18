<!-- Barre de navigation -->
<nav class="navbar">
    <a href="<?php echo $racine; ?>index.php" class="navbar-logo">Omnes<span>Event</span></a>

    <div class="navbar-links">
        <a href="<?php echo $racine; ?>index.php">Accueil</a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'organisateur'): ?>
            <a href="<?php echo $racine; ?>php/creer_evenement.php">Créer un événement</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['id'])): ?>
            <a href="<?php echo $racine; ?>php/profil.php">Mes Billets</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur'): ?>
            <a href="<?php echo $racine; ?>php/admin.php">Admin</a>
        <?php endif; ?>

        <a href="<?php echo $racine; ?>php/contact.php">Contact</a>

        <a href="<?php echo $racine; ?>php/calendrier.php">Calendrier</a>

        <a href="<?php echo $racine; ?>php/favoris.php">Favoris</a>
    </div>

    <!-- Bouton hamburger pour mobile -->
    <button class="menu-hamburger" id="btn-hamburger">&#9776;</button>

    <div class="navbar-right">
        <?php if (isset($_SESSION['id'])): ?>
            <a href="<?php echo $racine; ?>php/profil.php" class="btn btn-outline btn-small">Mon profil</a>
            <a href="<?php echo $racine; ?>php/logout.php" class="btn btn-primary btn-small">Déconnexion</a>
        <?php else: ?>
            <a href="<?php echo $racine; ?>php/login.php" class="btn btn-outline btn-small">Connexion</a>
            <a href="<?php echo $racine; ?>php/inscription.php" class="btn btn-primary btn-small">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Menu mobile (caché par défaut) -->
<div class="menu-mobile" id="menu-mobile">
    <a href="<?php echo $racine; ?>index.php">Accueil</a>

    <a href="<?php echo $racine; ?>php/calendrier.php">Calendrier</a>

    <a href="<?php echo $racine; ?>php/favoris.php">Favoris</a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'organisateur'): ?>
        <a href="<?php echo $racine; ?>php/creer_evenement.php">Créer un événement</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['id'])): ?>
        <a href="<?php echo $racine; ?>php/profil.php">Mes Billets</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur'): ?>
        <a href="<?php echo $racine; ?>php/admin.php">Admin</a>
    <?php endif; ?>

    <a href="<?php echo $racine; ?>php/contact.php">Contact</a>

    <div class="menu-mobile-separator"></div>

    <?php if (isset($_SESSION['id'])): ?>
        <a href="<?php echo $racine; ?>php/profil.php">Mon profil</a>
        <a href="<?php echo $racine; ?>php/logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="<?php echo $racine; ?>php/login.php">Connexion</a>
        <a href="<?php echo $racine; ?>php/inscription.php">Inscription</a>
    <?php endif; ?>
</div>
