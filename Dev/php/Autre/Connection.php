<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 05/04/2019
 * Time: 19:04
 */

/**
 * Crée un objet de type PDO avec les paramètres précisés dans le fichier config.php
 */
class Connection
{
    public $dbh;

    function __construct()
    {
        try{
            $this->dbh=new PDO(DSN,USER,PASSWORD);
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}