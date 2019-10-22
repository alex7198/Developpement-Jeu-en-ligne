<html lang="fr">
    <head>
        <title>Menu Principal</title>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <meta charset="UTF-8">
    </head>
    
    <body id="body_principal">
        <header>
          <?php global $vues; include($vues['header']); ?>
        </header>
        
        <section class="sec_contenu" id="sec_principal">
            <div id="div_pseudo">
                <?php echo '<label>Bonjour '.$_COOKIE['IDJoueur'].'</label>'?>
            </div>
            <div id="div_button2">
                <div id="div_connexion">
                    <form id="form_button_admin" method="post" action="index.php?action=deconnexion">
                        <input type="submit" class="btn_gris" value="deconnexion"> </input>
                    </form>
                </div>

                <div id="div_admin_button">
                    <form id="form_button_deco" method="post" action="index.php?action=ban_joueurs">
                        <?php 
                            $m = new UserModele();
                            if($m->est_admin($_COOKIE["IDJoueur"])){ echo '<input type="submit" class="btn_gris" value="admin"> </input>'; }
                        ?>
                    </form>
                </div>
            </div>      

            <div id="div_button">
                <form method="post" action="index.php?action=creation_partie">
                    <input id="btn_creation" type="submit" class="btn_principal"value="Créer une partie"></input>
                </form>
                
                <form method="post" action="index.php?action=rejoindre_partie">
                    <input id="btn_rejoindre" class="btn_principal" type="submit" value="Rejoindre une partie"></input>
                </form>
            </div>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
