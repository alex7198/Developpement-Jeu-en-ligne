<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Attente</title>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <script src="js/attente.js"></script>
    </head>
    
    <body>
        <header>
            <?php global $vues; include($vues['header']); ?>
        </header>

        <section class="sec_contenu">
            <input type="hidden" id="IDPartie" value="<?php echo $id?>">
            <input type="hidden" id="Createur" value="<?php echo $id?>">
            
            <h1 id="h_partie_creation"><?php $infos = $m->recupererNomCreateurPartie($id); echo "Partie ".$infos["Nom"]." de ".$infos["Createur"];?></h1>
            <label id="label_nbJoueurs"><?php echo "/".$m->recupererJoueursMaxPartie($id); ?></label><br><br><br>
            <p id="joueurs_manquant"></p>

            <h2>Inviter un joueur :</h2>
            <table>
                <tr id="invitations">
                    <td><input type="text" id="PseudoInvite"></td>
                    <td><input type="button" id="btn_inviter" value="Inviter"></td>
                    <td id="msg_retour_invitation"></td>
                </tr>
            </table>

            <h2>Liste des joueurs :</h2>
            <table id="tab_joueurs_attente"></table>
            
            <div id="div_button">
                <form method="post" action="index.php?action=quitter_partie">
                    <input type="submit" class="btn_co" value="Quitter" id="btn_quitter">
                    <input type="hidden" value="<?php echo $id;?>" name="IDPartie">
                    <input type="hidden" value="<?php echo $_COOKIE["IDJoueur"];?>" name="IDJoueur">
                </form>
                
                <form id="form_lancer" style="display:none" method="post" action="index.php?action=lancer_partie">
                    <input type="hidden" value="<?php echo $id;?>" name="IDPartie">
                    <input type="submit" class="btn_co" value="Lancer" id="btn_lancer" disabled>
                </form>
            </div>
        </section>
        
        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
