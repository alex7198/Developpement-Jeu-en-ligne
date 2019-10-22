<html lang="fr">
    <head>
        <title>Page d'erreur</title>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <meta charset="UTF-8">
    </head>
    
    <body id="body_principal">
        <header>
          <?php global $vues; include($vues['header']); ?>
        </header>
        
        <section class="sec_contenu" id="sec_erreur">
            <h1>Oups, une erreur s'est produite !</h1>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
