<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Contrôleur appelé par les différents scripts JS qui gèrent le jeu à travers AJAX
        
//Aiguillage
if(isset($_GET['fct'])){
    //On charge les autres fichiers et la session
    include_once "../Metier/Partie.php";
    require_once ('../Config/config.php');
    require_once('../Config/Autoload.php');
    Autoload::charger();
    session_id("ProjetWebL3");
    session_start();
        
    $fct = $_GET['fct'];
    switch($fct){
        case "GetTerrain":
            GetTerrain();
            break;
        case "RecupererJoueurs":
            RecupererJoueurs();
            break;
        case "Timer":
            Timer($_GET['joueur']);
            break;
        case "SeDeplacer":
            SeDeplacer($_GET['id'], intval($_GET['dh']), intval($_GET['dv']));
            break;
        case "TournerJoueur":
            TournerJoueur($_GET['id'], $_GET['angle']);
            break;
        case "QuitterPartie":
            QuitterPartie($_GET['id']);
            break;
        case "GetID":
            GetID($_GET['pseudo']);
            break;
        case "TerminerPartie":
            TerminerPartie();
            break;
    }
}

/**
 * Retourne une version XML du terrain
 */
function GetTerrain(){
    header("Content-type: text/xml");
    
    $id = "P".$_REQUEST["IDPartie"];
    echo $_SESSION[$id]['partie']->GetTerrain()->ExporterCarte()->asXML();
}

/**
 * Retourne une version XML de la liste des joueurs
 */
function RecupererJoueurs(){
    header("Content-type: text/xml");

    $id = "P".$_REQUEST["IDPartie"];
    echo $_SESSION[$id]['partie']->GetTerrain()->ExporterJoueurs()->asXML();
}

/**
 * Décrémente le timer et renvoie l'état de la partie
 */
function Timer($idJoueur){
    header("Content-type: text/xml");

    $id = "P".$_REQUEST["IDPartie"];
    echo $_SESSION[$id]['partie']->GetTimer($idJoueur)->asXML();
}

/**
 * Déplace le joueur sur le terrain
 * @param type $idJoueur Identifiant du joueur à bouger
 * @param type $dh Déplacement horizontal
 * @param type $dv Déplacement vertical
 */
function SeDeplacer($idJoueur, $dh, $dv){
    $id = "P".$_REQUEST["IDPartie"];
    $_SESSION[$id]['partie']->GetTerrain()->Deplacer($idJoueur, $dh, $dv);
}

/**
 * Modifie l'orientation du joueur
 * @param type $idJoueur Jouer à tourner
 * @param type $angle Angle de la rotation
 */
function TournerJoueur($idJoueur, $angle){
    $id = "P".$_REQUEST["IDPartie"];
    $joueur = $_SESSION[$id]['partie']->GetTerrain()->TrouverJoueurID($idJoueur);
    $joueur->Tourner($angle);

    echo $joueur->Orientation();
}

/**
 * Retire un joueur de la partie
 * @param type $idJoueur Joueur à retirer
 */
function QuitterPartie($idJoueur){
    $id = "P".$_REQUEST["IDPartie"];
    $joueur = $_SESSION[$id]['partie']->GetTerrain()->TrouverJoueurID($idJoueur);
    $_SESSION[$id]['partie']->GetTerrain()->RetirerJoueur($joueur);
}

/**
 * Retourne l'identifiant in game du pseudo passé en paramètre
 * @param type $pseudo Joueur dont on cherche l'identifiant
 */
function GetID($pseudo){
    header("Content-type: text/plain");
    
    $id = "P".$_REQUEST["IDPartie"];
    echo $_SESSION[$id]['partie']->GetTerrain()->TrouverIDPseudo($pseudo);
}

/**
 * Termine la partie en cours
 */
function TerminerPartie(){
    $id = "P".$_REQUEST["IDPartie"];
    $_SESSION[$id]['partie']->arreterPartie();
    unset($_SESSION[$id]['partie']);
}