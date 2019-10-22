<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" href="img/Pacman.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Création d'une partie</title>
    </head>

    <body>
        <header>
            <?php global $vues; include($vues['header']); ?>
        </header>
        
        <section id="sec_creation" class="sec_contenu">
            <h1 id="h_creation">Création d'une partie</h1>

            <form action="index.php?action=attente_partie" method="post">
                <p class="p_creation">
                    <label class="label_connection" for="name">Nom partie :</label>
                    <input class="input_connection" type="text" id="gameName" name="game_name">
                </p>

                <p class="p_creation">
                    <label id="label_partiePrivee" class="label_connection" for="gamePrive">Partie privée :</label>
                    <input type="radio" id="radioButtonPrivateGame" name="gamePrive">
                    <label  id="label_partiePublique" class="label_connection" for="gamePublique">Partie publique :</label>
                    <input type="radio" id="radioButtonPublicGame" name="gamePublique">
                </p>

                <p class="p_creation">
                    <label class="label_connection" for="playerNumber">Nombre de joueurs :</label>
                    <input class="input_connection" type="number" id="playerNumber" name="playerNumber" min="3" max="10" placeholder="3">
                </p>

                <p class="p_creation">
                    <label class="label_connection" for="duree">Durée en minutes :</label>
                    <input class="input_connection" type="number" id="duree" name="duree" min="1" max="60" placeholder="1">
                </p>

                <p class="p_creation" id="mdp" style="display:none">
                    <label class="label_connection" for="password">Mot de passe :</label>
                    <input class="input_connection" type="text" id="password" name="game_password"></input>
                </p>

                <p id="p_btn_creation" class="p_creation">
                    <input class="btn_co" type="submit" value="Créer la partie">
                    <input class="btn_co" id="quitter" type="button" value="Page principale">
                    <input class="btn_co"  type="reset" value="Réinitialiser les paramètres">
                </p>
            </form>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>

        <script src="js/creation.js" type="text/javascript"></script>
    </body>
</html>
