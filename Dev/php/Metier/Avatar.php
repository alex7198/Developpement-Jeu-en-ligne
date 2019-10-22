<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Un avatar est soit un joueur, soit un spectateur, soit un bonus
 */
class Avatar {
    protected $nom;         //Pseudo du joueur ou id du bonus
    protected $posX;        //Position en x sur la map
    protected $posY;        //Position en y sur la map
    protected $couleur;     //Couleur de l'avatar de l'objet à l'écran
    protected $primitive;   //Apparence de l'avatar de l'objet à l'écran
    
    /**
     * 
     * @param {type} _nom Nom pour représenter l'avatar
     * @param {type} _posX Position en X de l'avatar sur le terrain
     * @param {type} _posY Position en Y de l'avatar sur le terrain
     * @param {type} _couleur Couleur de l'avatar
     * @param {type} _primitive Primitive qui représente l'avatar sous forme de chaîne de caractères
     */
    public function __construct($_nom, $_posX, $_posY, $_couleur, $_primitive){
        $this->nom = $_nom;
        $this->posX = $_posX;
        $this->posY = $_posY;
        $this->couleur = $_couleur;
        $this->primitive = $_primitive;
    }
    
    /**
     * Retourne la position de l'avatar en X
     * @return {unresolved} posX
     */
    public function PositionX(){
        return $this->posX;
    }
    
    /**
     * Retourne la position de l'avatar en Y
     * @return {unresolved} posY
     */
    public function PositionY(){
        return $this->posY;
    }
    
    /**
     * Retourne le pseudo (le nom) de l'avatar
     * @return type
     */
    public function Pseudo(){
        return $this->nom;
    }
    
    /**
     * Retourne la primitive qui représente l'avatar
     * @return type
     */
    public function Primitive(){
        return $this->primitive;
    }
    
    /**
     * Retourne la couleur de la primitive
     * @return type
     */
    public function Couleur(){
        return $this->couleur;
    }
    
    /**
     * Permet de modifier la couleur de la primitive
     * @param type $nouvelle
     */
    public function ChangerCouleur($nouvelle){
        $this->couleur = $nouvelle;
    }
    
    /**
     * Permet de modifier la primitive qui représente l'avatar
     * @param type $nouvelle
     */
    public function ChangerPrimitive($nouvelle){
        $this->primitive = $nouvelle;
    }
}
