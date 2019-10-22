<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "Bonus.php";

/**
 * Bonus de type incognito
 * @type {type}
 */
class Incognito extends Bonus {
    public function __construct($_nom, $_posX, $_posY, $_couleur, $_primitive, $_fin){
        parent::__construct($_nom, $_posX, $_posY, $_couleur, $_primitive, $_fin);
    }
    
    /**
     * Applique un effet à un joueur
     * @param {type} joueur Joueur à qui appliquer l'effet
     */
    public function AppliquerEffet($joueur){
        if(!$joueur->SousBonus()){          //On fait en sorte ici qu'on ne puisse pas cumuler les effets
            $joueur->AppliquerBonus(30);    //On lance le timer du côté du joueur pour la durée de l'effet
            $joueur->DevenirIncognito();    //On applique l'effet
        }
    }
}
