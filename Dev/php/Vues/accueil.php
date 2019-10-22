<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Accueil</title>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <script src="js/accueil.js"></script>
    </head>
    
    <body id="body_accueil">
        <header>
            <?php global $vues; include($vues['header']); ?>
        </header>
        
        <section id="sec_accueil" class="sec_contenu">
            <div id="div_btn_co">
                <form method="post" class="form_btn_accueil"action="index.php?action=click_connexion">
                    <input id="btn_connex" class="btn_co" type="submit" value="Connexion"></input>
                </form>
                
                <form method="post" class="form_btn_accueil" action="index.php?action=click_inscription">
                    <input id="btn_inscr" class="btn_co" type="submit" value="Inscription"></input>
                </form>
            </div>
            
            <?php
                if(!isset($_SESSION[$_COOKIE["IDJoueur"]]['mode']))
                {
                    setcookie("IDJoueur",session_id());
                    $_SESSION[$_COOKIE["IDJoueur"]]['mode']=0;
                }
                if(isset($_SESSION[$_COOKIE["IDJoueur"]]['erreur']))
                {
                    echo '<label id="label_erreur">'.$_SESSION[$_COOKIE["IDJoueur"]]['erreur'].'</label>';
                    unset($_SESSION[$_COOKIE["IDJoueur"]]['erreur']);
                }
                else { echo '<label id="label_erreur"></label>'; }
                
                if($_SESSION[$_COOKIE["IDJoueur"]]['mode']==0) { include "php/Vues/Intermediaires/Formulaire_connexion.php"; }
                else{ include "php/Vues/Intermediaires/Formulaire_inscription.php"; }
            ?>
        </section>
        
        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
