<html lang="fr">
    <head>
        <script src="js/principal.js"></script>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <meta charset="UTF-8">
    </head>

    <body id="body_principal">
        <header>
            <?php global $vues; include($vues['header']); ?>
        </header>

        <section class="sec_contenu" id="sec_principal">
            <?php echo'<div id="div_validation_compte"><h1 id="h_notification">'.$_SESSION[$_COOKIE["IDJoueur"]]['notification'].'</h1></div>'; ?>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
