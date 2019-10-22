<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Rejoindre une partie</title>
        <meta name="Alexis Guyot">
        <link rel="icon" href="img/Pacman.ico" />
        <link rel="stylesheet" href="css/style1.css">
        <script src="js/rejoindre.js" type="text/javascript"></script>
    </head>

    <body>
        <header>
            <?php global $vues; include($vues['header']); ?>
        </header>

        <section class="sec_contenu">
            <h2 id="h_partie_attente">Parties en attente</h2><br><br>
            <div class="div_table_container">
                <table class="parties" id="enAttente">
                    <th align="left">Nom</th>
                    <th align="left">Nombre de joueurs</th>
                    <th align="left">Type de partie</th>
                    <th align="left"></th>
                    <?php include "php/Vues/Intermediaires/Parties_EnAttente.php"; ?>
                </table>
            </div>

            <h2>Parties en cours</h2>
            <div class="div_table_container">
                <table class="parties" id="enCours">
                    <th align="left">Nom</th>
                    <th align="left">Nombre de joueurs</th>
                    <th align="left">Type de partie</th>
                    <th align="left"></th>
                    <?php include "php/Vues/Intermediaires/Parties_EnCours.php"; ?>
                </table>
            </div>

            <h2>Invitations</h2>
            <div class="div_table_container">
                <table class="parties" id="Invitations">
                    <th align="left">Nom</th>
                    <th align="left">Nombre de joueurs</th>
                    <th align="left">Type de partie</th>
                    <th align="left">Invité(e) par</th>
                    <th align="left"></th>
                    <?php include "php/Vues/Intermediaires/Parties_Invitations.php"; ?>
                </table>
            </div>
            
            <form method="post" action="index.php?action=retour_principal">
                <input type="submit" class="btn_co" value="Revenir à la page principale" id="quitter">
            </form>

            <form method="post" action="index.php?action=rejoindre_attente" id="form_rejoindre">
                <input type="hidden" name="IDPartie" value="" id="rejoindrePartie_id">
                <input type="hidden" name="MDPPartie" value="" id="rejoindrePartie_mdp">
                <input type="hidden" name="TypePartie" value="" id="rejoindrePartie_type">
            </form>

            <form method="post" action="index.php?action=rejoindre_jeu" id="form_jeu">
                <input type="hidden" name="IDPartie" value="" id="rejoindreJeu_id">
                <input type="hidden" name="MDPPartie" value="" id="rejoindreJeu_mdp">
                <input type="hidden" name="TypePartie" value="" id="rejoindreJeu_type">
            </form>
        </section>

        <footer>
            <?php include($vues['footer']); ?>
        </footer>
    </body>
</html>
