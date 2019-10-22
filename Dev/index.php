<?php
/*
 * On inclue les fichier de configuration et l'autoloader que l'on charge(pour permettre l'inclusion automatique des classes php que l'on a créé)
 * Ensuite, on instancie le FrontController qui va regarder quelle action a été effectuer par l'utilisateur
 */
    require_once ('php/Config/config.php');
    require_once('php/Config/Autoload.php');
    Autoload::charger();
    session_destroy();
    session_id("ProjetWebL3");
    session_start();
    new FrontController();
?>