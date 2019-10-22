<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Classe abstraite qui représente un bonus à récupérer
 */
abstract class Bonus extends Avatar {
    private $fin;       //Valeur du timer à laquelle il faudra supprimer le bonus
    
    /**
     * Constructeur d'un bonus
     * @param type $_nom Identifiant du bonus
     * @param type $_posX Position en x sur la carte
     * @param type $_posY Position en y sur la carte
     * @param type $_couleur Couleur de la primitive représentant le bonus
     * @param type $_primitive Primitive représentant le bonus
     * @param type $_fin Valeur du timer à laquelle il faudra supprimer le bonus
     */
    public function __construct($_nom, $_posX, $_posY, $_couleur, $_primitive, $_fin){
        parent::__construct($_nom, $_posX, $_posY, $_couleur, $_primitive);
        $this->fin = $_fin;
    }
    
    /**
     * Indique si le temps d'apparition du bonus a expiré (auquel cas il faudra le retirer de la carte)
     * @param type $temps
     * @return type
     */
    public function EstTermine($temps){
        return $temps < $this->fin;
    }
    
    abstract public function AppliquerEffet($joueur);
}
