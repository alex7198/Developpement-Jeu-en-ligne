<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 05/04/2019
 * Time: 19:17
 */

//Classe s'occupant de la validation des saisies utilisateur
class  Validation
{
    //Validation de mdp
    public static function valideMdp($mdp)
    {
        //On teste si le mdp contient entre 8 et 20 caractères avec au moins une majuscule, 1 minuscule et 1 chiffre
        if ($m=filter_var($mdp,FILTER_VALIDATE_REGEXP,
            array(
                "options" => array("regexp"=>"/^(?=.{8,20}$)(?=(?:.*?[A-Z]){1,18})(?=.*?[a-z])(?=(?:.*?[0-9]){1,18}).*$/")
            ))) {
            return $m;
        }
        else {
            return null;
        }
    }

    //Validation de pseudo
    public static function validePseudo($pseudo)
    {
        //On teste si le pseudo contient entre 8 et 20 caractères : a-zA-Z0-9!?_- sont les caractères autorisés
        if ($p=filter_var($pseudo,FILTER_VALIDATE_REGEXP,
            array(
                "options" => array("regexp"=>"/^[a-zA-Z0-9!?_-]{8,20}$/")
            ))) {
            return $p;
        }
        else {
            return null;
        }
    }

    //Validation email
    public static function valideEmail($lien)
    {
        if ($rep = filter_var($lien, FILTER_VALIDATE_EMAIL)) {
            return $rep;
        } else {
            return null;
        }
    }
}