<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 04/04/2019
 * Time: 17:24
 */

//Controller de l'administrateur-> même fonctionnement que celui de l'utilisateur
class AdminController
{
    function __construct()
    {
        global $vues;
        try {
            $action = $_REQUEST['action'];
            switch ($action) {
                case 'ban_joueurs':
                    $this->bannir_joueurs();
                    break;
                case 'valider_ban':
                    $this->valider_ban_joueur();
                    break;
            }
        }
        catch(Exception $e) { }
    }

    /**
     * Quand on clique sur le bouton "admin" de la page principale
     * Accessible seulement pour un utilisateur qui est Admin
     */
    private function bannir_joueurs()
    {
        global $vues;
        $m = new UserModele();
        if($m->est_admin($_COOKIE["IDJoueur"])){ require($vues['admin']); }
        else { require($vues['principal']); }
    }

    /**
     * Quand on clique sur le bouton valider de la page d'administration
     */
    private function valider_ban_joueur()
    {
        global $vues;
        try{
            if(isset($_REQUEST["aBannir"]) && isset($_REQUEST["msg"])){
                $mail = $_REQUEST["aBannir"];
                $raison = $_REQUEST["msg"];
                $date = date('d m Y H:i');
                $m = new UserModele();
                $m->bannir_joueur($mail, $raison, $date);
                require($vues['principal']);
            }
            else{
                require($vues['admin']);
            }
        }
        catch(Exception $e){
            echo $e;
            require($vues['admin']);
        }
    }

}