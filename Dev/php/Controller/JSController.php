<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Contrôleur appelé par les différents scripts JS à travers AJAX (sauf ceux en rapport avec le jeu qui ont leur propre contrôleur)

//Aiguillage
if(isset($_GET['fct'])){
    //On charge les autres fichiers et la session
    require_once ('../Config/config.php');
    require_once('../Config/Autoload.php');
    Autoload::charger();
    session_id("ProjetWebL3");
    session_start();
        
    $fct = $_GET['fct'];
    switch($fct){
        case "GetAdherents":
            GetAdherents();
            break;
        case "inviter_joueur":
            InviterJoueur();
            break;
    }
}

/**
 * Retourne une version JSON de la table UTILISATEUR de la BD
 */
function GetAdherents(){
    $m = new UserModele();
    echo json_encode($m->get_users());
}

/**
 * Ajoute dans la BD une invitation
 */
function InviterJoueur(){
    $m = new UserModele();
    if(isset($_REQUEST["IDPartie"]) && isset($_REQUEST["Expediteur"]) && isset($_REQUEST["Destinataire"])) {
        $IDPartie = $_REQUEST["IDPartie"];
        $exp = $_REQUEST["Expediteur"];
        $dest = $_REQUEST["Destinataire"];
        $date = date('d m Y H:i');
        echo $m->inviter_joueur($IDPartie,$exp,$dest,$date);
    }
    else{
        echo "Erreur : Impossible d'inviter ce joueur";
    }
}