<html lang="fr">
    <head>
        <script src="../../js/three.min.js" ></script>
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
            <div id="div_validation_compte">
                <?php echo'<h1 id="h_notification">'.$_SESSION[$_COOKIE["IDJoueur"]]['notification'].'</h1></br>';?>
                <h2 id="h_validation_compte">Cliquez <a href="index.php?action=arrivee">ici</a> pour vous connecter</h2>
            </div>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
