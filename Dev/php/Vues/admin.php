<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Accueil</title>
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <script src="js/admin.js"></script>
    </head>
    
    <body>
        <header>
        <?php global $vues; include($vues['header']); ?>
        </header>
    
        <section class="sec_contenu">

            <h1 id="h_admin">Administration du site</h1><br>
            <p id="p_recherche">
                <label class="label_standard" for="recherche">Rechercher un joueur :</label>
                <input  class="input_standard" type="text" name="recherche">
            </p>
            
            <div id="div_tab_joueur" class="div_table_container">
                <table id="tab_joueur"></table>
            </div>
            
            <label id="label_motif"class="label_standard"> Motif du ban : </label><br>
            <textarea id="textarea_ban"></textarea>
            
            <form method="post" action="index.php?action=valider_ban" id="form_bannir">
                <input type="button" id="btn_valider_ban" class="btn_principal" value="Valider"></input>
            </form>

            <form type="submit" action="index.php?action=retour_principal" method="post">
                <input id="btn_page_prec" class="btn_co" type="submit" value="Revenir à la page principale">
            </form>
        </section>
        
        <footer>
            <?php
            include($vues['footer']); ?>
        </footer>
    </body>
</html>
