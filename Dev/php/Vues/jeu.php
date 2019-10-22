<!DOCTYPE html >
<html>
    <head>
        <meta charset="UTF-8">
        <title>Page de jeu</title>
        <meta name="Thinhinane Saidi">
        <link rel="icon" href="img/Pacman.ico" />
        <link href="css/jeu.css" rel="stylesheet" type="text/css"/>
    </head>
    
    <body>
        <header>
                <?php global $vues; include($vues['header']); ?>
        </header>
        
        <section class="sec_contenu" id="section">
            <input type="hidden" id="IDPartie" value="<?php if(isset($id)) { echo $id; } else { echo $_REQUEST['IDPartie']; } ?>">
            <div id="temp" style="font-family: 'Courier New'; font-size: 12px;"></div>
            <div id="msg_general"></div>  
            <div id="infos"></div>
            <div id="bonus"></div>
            <div id="timer"></div>
            <div id="spec"></div>
            <div id="scene"></div>
            <div id="manuel">Pour avancer : Z - Pour reculer : S - Pour tourner : Q et D - Pour activer super vue (après avoir mangé un bonbon magenta) : F</div>
            <form id="form_quitter_jeu" action="index.php">
                <input type="submit" class="btn_co" id="btn_quitter_jeu" value="Quitter">
                <input type="hidden" name="action" value="retour_principal">
            </form>
        </section>
        
        <footer>
               <?php include($vues['footer']); ?>
        </footer>
    </body>

    <script src="js/three.min.js" type="text/javascript"></script>
    <script src="js/Jeu/variables.js" type="text/javascript"></script>
    <script src="js/Jeu/affichageThreeJS.js" type="text/javascript"></script>
    <script src="js/Jeu/communicationServeur.js" type="text/javascript"></script>
    <script src="js/jeu.js" type="text/javascript"></script>
</html>
