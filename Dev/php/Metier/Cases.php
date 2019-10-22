<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Case du terrain
 */
class Cases {
    private $navigable;     //Booléen indiquant si un joueur peut se déplacer sur la case ou non
    private $magique;       //Booléen indiquant si la case est magique ou non
    private $estOccupee;    //Chaine de caractères qui indique par quoi est occupée la case (si elle l'est)
    
    /**
     * Si une case est navigable alors le joueur pourra se déplacer dessus
     * Si une case est magique alors un joueur qui se déplace dessus sera téléporté à une autre case magique
     */
    public function __construct(){
        $this->navigable = true;
        $this->magique = false;
        $this->estOccupee = "";
    }
    
    /**
     * Rend la case navigable si elle ne l'était pas
     * Ou non-navigable si elle l'était.
     */
    public function InverserNavigabilite(){
        $this->navigable = !$this->navigable;
    }
    
    /**
     * Rend la case magique si elle ne l'était pas
     * Ou non-magique si elle l'était.
     */
    public function InverserMagie(){
        $this->magique = !$this->magique;
    }
    
    /**
     * Rend la case occupée si elle ne l'était pas
     * Ou non-occupée si elle l'était.
     */
    public function InverserOccupation(){
        $this->estOccupee = "";
    }
    
    /**
     * Rend la case occupée si elle ne l'était pas
     * Ou non-occupée si elle l'était.
     */
    public function RendreOccupee($role){
        $this->estOccupee = $role;
    }
    
    /**
     * Indique si la case est navigable
     * @return {Boolean}
     */
    public function estNavigable(){
        return $this->navigable;
    }
    
    /**
     * Indique si la case est magique
     * @return {Boolean}
     */
    public function estMagique(){
        return $this->magique;
    }
    
    /**
     * Indique si la case est occupée
     * @return {Boolean}
     */
    public function EstOccupee(){
        if(strnatcmp($this->estOccupee, "") == 0){ return false; }
        else { return true; }
    }
    
    /**
     * Renvoie une version réduite de ce par quoi est occupée la case
     * @return string
     */
    public function OccupeePar(){
        $res = "";
        
        if($this->estOccupee == "blaireau") { $res = "B"; }
        else if($this->estOccupee == "kéké") { $res = "K"; }
        else if($this->estOccupee == "bonus") { $res = "G"; }
        
        return $res;
    }
}
