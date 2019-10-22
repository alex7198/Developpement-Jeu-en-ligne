<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once ("Avatar.php");

/**
 * Un participant est un joueur ou un spectateur
 */
abstract class Participant extends Avatar {
    protected $superVue;        //Si true le joueur peut passer en mode super vue
    protected $ID;              //Identifiant unique du participant
    
    public function __construct($_nom, $_posX, $_posY, $_couleur, $_primitive, $_ID){
        parent::__construct($_nom, $_posX, $_posY, $_couleur, $_primitive);
        $this->superVue = false;
        $this->ID = $_ID;
    }
    
    /**
     * Autorise le joueur à pouvoir passer en super vue
     */
    public function ObtenirSuperVue(){
        $this->superVue = true;
    }
    
    /**
     * Indique si le joueur possède la super vue ou non
     * @return type
     */
    public function EstSuperVue(){
        return $this->superVue;
    }
    
    /**
     * Retourne l'identifiant unique du joueur
     * @return type
     */
    public function GetID(){
        return $this->ID;
    }
}
