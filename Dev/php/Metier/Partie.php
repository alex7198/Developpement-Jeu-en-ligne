<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "Terrain.php";

/**
 * Partie du jeu
 */
class Partie{
    private $IDPartie;              //Identifiant unique de la partie
    private $Timer;                 //Temps restant
    private $Joueurs;               //Noms des joueurs qui participent à la partie
    private $Terrain;               //Terrain de jeu
    private $Modele;                //Modèle connecté à la BD
    private $Createur;              //Créateur de la partie
    
    /**
     * Constructeur
     * @param {type} $_idPartie Identifiant de la partie
     * @param {type} $_nbParticipants Nombre de participants
     * @param {type} $_duree Durée de la partie
     * @param {type} $_createur Adhérent qui a créé la partie
     * @param {type} $_joueurs Adhérents qui vont jouer à la partie
     * @return {Partie}
     */
    public function __construct($_idPartie, $_createur, $_duree, $_joueurs, $_modele){
        $this->IDPartie = $_idPartie;
        $this->Createur = $_createur;
        $this->Timer = $_duree;
        $this->Joueurs = $_joueurs;
        $this->Terrain = new Terrain($_joueurs);
        $this->Modele = $_modele;
        $this->changerCouleursBD();
        $this->demarrerPartie();
    }
    
    /**
     * Ajoute un adhérent à la liste des participants
     * @param {type} a Adhérent à ajouter
     */
    public function ajouterJoueur($a){
        push_array($this->Joueurs, $a);
    }
    
    /**
     * Evenements de la partie à déclencher en fonction du timer
     */
    public function EvenementsReguliers(){
        //Ajout des bonus sur la map (10% de chances d'en ajouter un par seconde)
        $ran = rand(0,9);
        if($ran == 0) { $this->Terrain->AjoutBonus($this->Timer); }
        
        //Inversion des rôles (1% de chance que ça se produise par seconde)
        $ran = rand(0,99);
        if($ran == 0) { $this->Terrain->inverserRoles(); }
        
        //Décrémenter le temps restant des effets appliqués aux joueurs
        $this->Terrain->TimerEffets();
        
        //Supprimer de sur la carte les bonus qui ont expiré
        $this->Terrain->SupprimeBonus($this->Timer);
    }
    
    /**
     * Décrémente la valeur du timer et renvoie un ensemble d'informations concernant la partie au format XML
     * @return \SimpleXMLElement
     */
    public function GetTimer($idJoueur){
        //On décrémente le timer et on effectue les actions qui demandent une exécution en fonction du timer
        if($this->Timer > 0) { 
            if($idJoueur === $this->Createur){
                $this->Timer--; 
                $this->EvenementsReguliers();
            }
        }
        else { $this->arreterPartie(); }
        
        //On crée la réponse au format XML
        $t = new SimpleXMLElement('<timer/>');
        //On ajout la valeur du timer
        $t->addChild('temps',$this->Timer);
        //On ajoute les joueurs restants pour savoir qui a gagné à la fin
        $joueurs = $t->addChild('joueurs');
        for($i=0; $i<count($this->Terrain->getListeJoueurs()); $i++){
            $joueur = $joueurs->addChild('joueur');
            $joueur->addChild('ID',$this->Terrain->getListeJoueurs()[$i]->GetID());
            $joueur->addChild('pseudo',$this->Terrain->getListeJoueurs()[$i]->Pseudo());
            $joueur->addChild('couleur',$this->Terrain->getListeJoueurs()[$i]->CouleurDeBase());
        }
        //On ajoute les bonus qui ont pu être modifiés
        $b = $t->addChild('lesBonus');
        for($i=0; $i<count($this->Terrain->getListeBonus()); $i++){
            $bonus = $b->addChild('bonus');
            $bonus->addChild('ID',$this->Terrain->getListeBonus()[$i]->Pseudo());
            $bonus->addChild('posX',$this->Terrain->getListeBonus()[$i]->PositionX());
            $bonus->addChild('posY',$this->Terrain->getListeBonus()[$i]->PositionY());
            $bonus->addChild('primitive',$this->Terrain->getListeBonus()[$i]->Primitive());
            $bonus->addChild('couleur',$this->Terrain->getListeBonus()[$i]->Couleur());
        }
        
        return $t;
    }
    
    /**
     * Permet d'accéder au terrain
     * @return type
     */
    public function GetTerrain(){
        return $this->Terrain;
    }
    
    /**
     * Précise la couleur des joueurs dans la BD
     */
    public function changerCouleursBD(){
        foreach($this->GetTerrain()->getListeJoueurs() as $joueur){
            try{ $this->Modele->changer_couleur(substr($this->IDPartie,0),$joueur->Pseudo(),$joueur->CouleurDeBase()); } catch (Exception $e) { echo $e; }
        }
    }
    
    /**
     * Indique à la BD que la partie a démarré
     */
    public function demarrerPartie(){
        try{ $this->Modele->demarrer_partie(substr($this->IDPartie,0)); } catch (Exception $e) { echo $e; }
    }
    
    /**
     * Indique à la BD que la partie est arrêtée
     */
    public function arreterPartie(){
        $nbRouges = 0; $nbBleus = 0;
        foreach($this->GetTerrain()->getListeJoueurs() as $joueur){
            if($joueur->CouleurDeBase() == "red") { $nbRouges++; }
            else { $nbBleus++; }
        }
        if($nbRouges == $nbBleus) { $resultat = "egalite"; }
        else if($nbRouges > $nbBleus) { $resultat = "rouges"; }
        else { $resultat = "bleus"; }
        try { $this->Modele->arreter_partie(substr($this->IDPartie,0),$resultat,date('d m Y H:i')); } catch (Exception $e) { echo $e; }
    }
}
