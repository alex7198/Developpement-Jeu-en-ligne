<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 04/04/2019
 * Time: 17:59
 */

//Va permettre de charger automatiquement les classes php.
//Evite d'inclure à chaque fois les classes en début de ficher

class Autoload
{


    public static function charger()
    {
        //On spécifie que la fonction chargementClasses est celle utilisée pour l'autoloader.
        spl_autoload_register(self::chargementClasses(),false);
    }

    public static function chargementClasses()
    {
        //On récupère les variables globales rep et classes spécifiées dans le fichier de config
        global $rep;
        global $classes;
        //Pour chaque classe, on inclue le fichier
        foreach ($classes as $k => $v){
            $file=$rep.$v.'/'.$k;
            if (file_exists($file))
            {
                include_once $file;
            }
        }

    }
}