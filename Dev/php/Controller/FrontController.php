<?php
/*
 * Classe permettant de rediriger l'action de l'utilisateur vers le bon Controller
*/
class FrontController
{
    //Tableau des actions des utilisateurs
    private $tab_user_action=array('valider_connexion','arrivee','creation_partie','rejoindre_partie','inscription','retour_principal','attente_partie','rejoindre_attente','quitter_partie','lancer_partie','quitter_jeu','deconnexion','click_inscription','click_connexion','validation_mail', 'liste_joueurs', 'est_createur','recuperer_statut','rejoindre_jeu');
    //Tableau des actions des administrateurs
    private $tab_admin_action=array("ban_joueurs","valider_ban");

    public function __construct()
    {
        try {
            //Si aucune action n'a été envoyée, cela signifie que l'utilisateur vient de se connecter sur le site
            if(!isset($_REQUEST['action']))
            {$_REQUEST['action']='arrivee';}
            //On récupère dans une variable la variable "action" envoyée en post ou en get
            $action = $_REQUEST['action'];
            //Si c'est une action utilisateur-> instancie UserController
            if (in_array($action, $this->tab_user_action)) {
                new UserController();
            }
            //Sinon si c'est une action admin-> instancie AdminController
            else if (in_array($action, $this->tab_admin_action)) {
                new AdminController();
            }
            //Sinon page d'erreur
            else {
                global $vues;
                require($vues['erreur']);
            }
        }
        catch (Exception $e)
        {
            global $vues;
            require($vues['erreur']);
        }
    }
}