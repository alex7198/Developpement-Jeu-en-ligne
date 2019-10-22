<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "Participant.php";

/**
 * Joueur de la partie
 */
include_once ("Participant.php");

class Joueur extends Participant {
    //Attributs généraux
    private $role;              //Kéké ou Blaireau
    private $orientation;       //Orientation de la caméra
    private $dureeBonus;        //Timer qui décompte le temps restant à l'effet appliqué au joueur

    //Attributs liés aux bonus
    private $vitesse;
    private $invisible;
    private $intouchable;
    private $incognito;
    private $bonus;             //Indique si le joueur est actuellement soumis à l'effet d'un bonus
    
    //On sauvegarde les valeurs de base de ces attributs qui peuvent être modifiés par les bonus
    private $couleurDeBase;
    private $primitiveDeBase;
    
    /**
     * Constructeur
     * @param {type} _nom
     * @param {type} _posX
     * @param {type} _posY
     * @param {type} _couleur
     * @param {type} _primitive
     * @param {type} _ID Identifiant unique du joueur
     * @param {type} _role Soit kéké, soit blaireau
     * @return {Joueur}
     */
    public function __construct($_nom, $_posX, $_posY, $_couleur, $_primitive, $_id, $_role){
        parent::__construct($_nom, $_posX, $_posY, $_couleur, $_primitive,$_id);
        $this->role = $_role;
        $this->dureeBonus = 0;
        $this->orientation = 0;
        $this->couleurDeBase = $_couleur;
        $this->primitiveDeBase = $_primitive;
        
        //Paramètres du jeu
        $this->InitParamBonus();
    }
    
    /**
     * Initialise les attributs relatifs aux bonus
     */
    public function InitParamBonus(){
        $this->vitesse = 1;                                 //Vitesse du joueur
        $this->intouchable = false;                         //Si true le joueur ne peut pas être mangé
        $this->superVue = false;                            //Si true le joueur a accès à la caméra "Super Vue"
        $this->invisible = false;                           //Si true le joueur est invisible
        $this->incognito = false;                           //Si true le joueur est incognito
        $this->bonus = false;                               //Si true le joueur est déjà sous l'effet d'un bonus
        $this->ChangerCouleur($this->couleurDeBase);        //Le joueur retrouve sa couleur normale
        $this->ChangerPrimitive($this->primitiveDeBase);    //Le joueur retrouve sa forme normale
    }
            
    /**
     * Déplace le joueur sur le terrain
     * @param {type} nbCasesX Nombre de cases à se déplacer en X
     * @param {type} nbCasesY Nombre de cases à se déplacer en Y
     */
    public function Deplacer($x, $y){
        $this->posX = $x;
        $this->posY = $y;
    }
    
    /**
     * Effectue une rotation de la caméra
     * @param {type} angle Angle de la rotation
     */
    public function Tourner($angle){
        $this->orientation += $angle;
    }
    
    /**
     * Lance le timer qui applique le bonus
     */
    public function AppliquerBonus($duree){
        $this->InitParamBonus();
        $this->bonus = true;
        $this->dureeBonus = $duree;
    }
    
    /**
     * Action à effectuer quand le joueur entre en contact avec un autre
     * @param {type} autre Autre Avatar
     * Retourne 0 si l'autre est un autre kéké alors qu'on est kéké
     * Retourne -1 si l'autre est un blaireau et qu'on est un kéké
     * Retourne 1 si l'autre est un kéké et qu'on est un blaireau
     * Retourne -2 si l'autre est un blaireau et qu'on est un blaireau
     * Retourne 2 si l'autre est un bonus
     */
    public function EntrerEnContact($autre){
        $res = 0;
        if(get_class($autre) == "Joueur"){   //Si c'est un joueur
            switch($this->role){
                case "kéké":                //Si on est un kéké
                    if($autre->Role() === "blaireau") { $res = -1; }    //Si l'autre est un blaireau on se fait manger
                    if($this->intouchable) { $res = 0; }                //Sauf si on possède le bonus bouclier qui nous rend intouchable
                    break;
                case "blaireau":            //Si on est un blaireau
                    $autreRole = $autre->Role();                        
                    $res = 1;                                                               //Dans tous les cas on mange l'autre
                    if($autreRole === "blaireau" && !$this->intouchable) { $res = -2; }     //Mais si l'autre est un blaireau, on se fait manger aussi
                    if($autreRole === "blaireau" && $this->intouchable) { $res = 1; }       //Sauf si on est intouchable, auquel cas seul l'autre se fait manger
                    break;
            }
        }
        else{       //Si ce n'est pas un joueur c'est forcément un bonus
            $autre->AppliquerEffet($this);      //On applique son effet
            $res = 2;
        }
        
        return $res;
    }
    
    /**
     * Inverse les rôles des kékés et des blaireaux
     */
    public function InverserRole(){
        if($this->role === "kéké") { $this->role = "blaireau"; }
        else if($this->role === "blaireau") { $this->role = "kéké"; }
    }
    
    /**
     * Diminue la valeur du timer qui représente le temps restant à l'effet appliqué
     */
    public function DiminuerTempsEffet(){
        if($this->dureeBonus >= 0) {                                        //Le timer ne peut pas être inférieur à 0
            if($this->dureeBonus == 0) { $this->InitParamBonus(); }         //S'il est égal à 0, il faut réinitialiser les valeurs des attributs relatifs aux bonus
            else { $this->dureeBonus--; }                                   //Sinon il faut décrémenter le timer
        }
    }
    
    /**
     * Rend le joueur intouchable
     */
    public function DevenirIntouchable(){
        $this->intouchable = true;
    }
    
    /**
     * Rend le joueur invisible
     */
    public function DevenirInvisible(){
        $this->invisible = true;
    }
    
    /**
     * Rend le joueur incognito (sa forme et sa couleur change pour un cône blanc)
     */
    public function DevenirIncognito(){
        $this->incognito = true;
        $this->ChangerCouleur("white");
        $this->ChangerPrimitive("cone");
    }
    
    /**
     * Permet au joueur d'aller plus vite
     */
    public function Accelerer(){
        $this->vitesse *= 2;
    }
    
    /**
     * Retourne le rôle du joueur
     * @return {String}
     */
    public function Role(){
        return $this->role;
    }
    
    /**
     * Retourne l'orientation de la caméra pour le joueur
     * @return type
     */
    public function Orientation(){
        return $this->orientation;
    }
    
    /**
     * Retourne la vitesse du joueur
     * @return type
     */
    public function Vitesse(){
        return $this->vitesse;
    }
        
    /**
     * Indique si le joueur peut être mangé ou non
     */
    public function EstIntouchable(){
        return $this->intouchable;
    }
    
    /**
     * Indique si le joueur est incognito ou non
     * @return type
     */
    public function EstIncognito(){
        return $this->incognito;
    }
    
    /**
     * Indique si le joueur est invisible ou non
     * @return type
     */
    public function EstInvisible(){
        return $this->invisible;
    }
    
    /**
     * Retourne la couleur de base du joueur (l'équipe à laquelle il appartient)
     * @return type
     */
    public function CouleurDeBase(){
        return $this->couleurDeBase;
    }
    
    /**
     * Indique si le joueur possède déjà un bonus ou non
     * @return {Boolean}
     */
    public function SousBonus(){
        return $this->bonus;
    }
    
    /**
     * Valeur affichée quand on affiche en PHP un objet de type "Joueur"
     * @return type
     */
    public function __toString() {
        return $this->ID.' - '.$this->nom.' : '.$this->role.', '.$this->couleur.', '.$this->primitive.', '.$this->PositionX().', '.$this->PositionY();
    }
}

