<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once "Cases.php";
include_once "Joueur.php";
include_once "Bottes.php";
include_once "Bouclier.php";
include_once "CapeInvisibilite.php";
include_once "Incognito.php";
include_once "SuperVue.php";
/**
 * Terrain sur lequel se déroule la partie
 */
class Terrain {   
    //Constantes liées au terrain
    const TAILLE = 36;              //Taille de la map (TAILLExTAILLE)
    const NBOBSTACLES = 10;         //Nombre d'obstacles placés sur la map
    const NBCASESMAGIQUES = 10;     //Nombre de cases magiques
    const DUREEBONUS = 30;          //Durée pendant laquelle un bonus reste sur le terrain avant de disparaitre
    
    private $carte;                 //Représentation 2D du terrain
    private $joueurs;               //Liste des joueurs présents sur le terrain
    private $magiques;              //Liste des cases magiques présentes sur le terrain
    private $bonus;                 //Liste des bonus présents sur le terrain
    
    /**
     * Constructeur du terrain
     * @return {Terrain}
     */
    public function __construct($_joueurs){
        //On crée dans la classe une représentation sous forme de tableau à deux dimensions de la carte de jeu
        $this->ConstruitCarte();
        
        //On ajoute les obstacles et les cases magiques
        $this->ObstaclesAleatoires(self::NBOBSTACLES);
        $this->AjoutCasesMagiques(self::NBCASESMAGIQUES);
        
        //On crée les avatars
        $this->bonus = array();
        $this->creerJoueurs($_joueurs);
        $this->placerJoueurs();
    }
    
    /*------------------------------------- Fonctions relatives à la construction du terrain -------------------------------------*/
    
    /**
     * Construit la représentation 2D de la carte
     */
    public function ConstruitCarte(){
        //Construction du terrain
        for($i=0; $i<self::TAILLE; $i++) {
            for($j=0; $j<self::TAILLE; $j++) {
                $this->carte[$i][$j] = new Cases();
            }
        }
        
        //Ajout des murs
        //Contours
        for($i=0; $i<self::TAILLE; $i++){
            $this->carte[0][$i]->InverserNavigabilite();                    //Une case non-navigable est un mur
            $this->carte[self::TAILLE-1][$i]->InverserNavigabilite();
            if($i != 0 && $i != self::TAILLE-1 && $i != self::TAILLE/2 && $i != (self::TAILLE/2)-1 && $i != (self::TAILLE/2)+1){    //On laisse deux ouvertures dans les contours
                $this->carte[$i][0]->InverserNavigabilite();
                $this->carte[$i][self::TAILLE-1]->InverserNavigabilite();
            }
        }
        //Murs intérieurs
        $this->MursInterieur();
    }
    
    /**
     * Crée sur le terrain l'ensemble des murs à l'intérieur de la carte
     */
    private function MursInterieur(){
        //Coin haut gauche
        $this->AjoutZoneMur(3, 3, 7, 7); $this->AjoutZoneMur(9, 3, 11, 7); $this->AjoutZoneMur(3, 9, 7, 15); $this->AjoutZoneMur(1, 17, 7, 19);
        
        //Coin haut droit
        $this->AjoutZoneMur(3, 21, 7, 27); $this->AjoutZoneMur(3, 29, 7, 33); $this->AjoutZoneMur(9, 29, 11, 33);
        
        //Coin bas gauche
        $this->AjoutZoneMur(26, 3, 28, 7); $this->AjoutZoneMur(28, 5, 31, 7); $this->AjoutZoneMur(33, 5, 35, 14); $this->AjoutZoneMur(29, 9, 33, 11); $this->AjoutZoneMur(30, 1, 33, 3);
        
        //Coin bas centre
        $this->AjoutZoneMur(31, 16, 33, 20); $this->AjoutZoneMur(29, 13, 31, 23); $this->AjoutZoneMur(27, 13, 29, 15); $this->AjoutZoneMur(27, 21, 29, 23);
        
        //Coin bas droite
        $this->AjoutZoneMur(33, 22, 35, 31); $this->AjoutZoneMur(29, 25, 33, 27); $this->AjoutZoneMur(30, 33, 33, 35); $this->AjoutZoneMur(28, 29, 31, 31); $this->AjoutZoneMur(26, 29, 28, 33);
        
        //Contours sorties
        $this->AjoutZoneMur(13, 1, 17, 7); $this->AjoutZoneMur(20, 1, 24, 7); $this->AjoutZoneMur(13, 29, 17, 35); $this->AjoutZoneMur(20, 29, 24, 35);
        
        //Centre terrain
        $this->AjoutZoneMur(15, 13, 17, 17); $this->AjoutZoneMur(15, 19, 17, 23); $this->AjoutZoneMur(19, 13, 21, 17); $this->AjoutZoneMur(19, 19, 21, 23);
        $this->AjoutZoneMur(17, 13, 19, 15); $this->AjoutZoneMur(17, 21, 19, 23);
        $this->AjoutZoneMur(11, 17, 13, 19); $this->AjoutZoneMur(9, 13, 11, 23); $this->AjoutZoneMur(25, 17, 27, 19); $this->AjoutZoneMur(23, 13, 25, 23);
        $this->AjoutZoneMur(9, 9, 17, 11); $this->AjoutZoneMur(20, 9, 27, 11); $this->AjoutZoneMur(9, 25, 17, 27); $this->AjoutZoneMur(20, 25, 27, 27);
    }
    
    /**
     * Ajoute à la carte un mur sur une zone rectangulaire donnée (vue du dessus sur une map 2D)
     * @param type $xdebut Coordonnée en x du coin haut gauche du mur à ajouter
     * @param type $ydebut Coordonnée en y du coin haut gauche du mur
     * @param type $xfin Coordonnée en x du coin bas droit du mur à ajouter
     * @param type $yfin Coordonnée en y du coin bas droit du mur à ajouter
     */
    private function AjoutZoneMur($xdebut, $ydebut, $xfin, $yfin){
        for($i=$xdebut; $i<$xfin; $i++){
            for($j=$ydebut; $j<$yfin; $j++){
                $this->carte[$i][$j]->InverserNavigabilite();
            }
        }
    }
    
    /**
     * Retourne des coordonnées de libres sur la carte au format x:y.
     * @return type
     */
    public function coordonneesLibres(){
        $x = 0; $y = 0; 
            
        //Une case libre est une case qui est navigable, pas magique et pas déjà occupée
        while(!$this->carte[$x][$y]->estNavigable() && !$this->carte[$x][$y]->estMagique() && !$this->carte[$x][$y]->EstOccupee()){
            $x = rand(0,self::TAILLE-1);
            $y = rand(0,self::TAILLE-1);
        }
        
        return $x.':'.$y;
    }
    
    /**
     * Ajoute sur la carte un certain nombre d'obstacles
     * @param {type} nbObst Nombre d'obstacles à ajouter
     */
    private function ObstaclesAleatoires($nbObst){
        while($nbObst>0){
            $coords = explode(':',$this->coordonneesLibres());          //On cherche une case libre sur le terrain
            $x = $coords[0]; $y = $coords[1];                           //On récupère ses coordonnées
            $this->carte[$x][$y]->InverserNavigabilite();               //On ajoute un mur à cet endroit
            $nbObst--;
        }
    }
    
    /**
     * Ajoute sur la carte un certain nombre de cases magiques
     * @param {type} nbCasesMagiques
     */
    private function AjoutCasesMagiques($nbCasesMagiques){
        $this->magiques = array();                                      //On stocke la position des cases magiques pour pouvoir en trouver une par la suite
        
        while($nbCasesMagiques>0){
            $coords = explode(':',$this->coordonneesLibres());          //On cherche une case libre sur le terrain
            $x = $coords[0]; $y = $coords[1];                           //On récupère ses coordonnées
            $this->carte[$x][$y]->InverserMagie();                      //On la rend magique
            array_push($this->magiques, [$x,$y]);                       //On stocke sa position
            $nbCasesMagiques--;
        }
    }
    
    /*------------------------------------- Fonctions relatives à la gestion des joueurs -------------------------------------*/
    
    /**
     * On crée les joueurs
     * @param type $_joueurs Liste des pseudos des joueurs qui vont jouer sur le terrain
     */
    private function creerJoueurs($_joueurs){
        $this->joueurs = array();
                
        //1/3 des joueurs seront des blaireaux (en tout bien tout honneur)
        $blaireaux = array();
        $ind = rand(0,count($_joueurs)-1);
        for($i=0; $i<floor(count($_joueurs)/3);$i++){
            while(in_array($ind,$blaireaux)) { $ind = rand(0,count($blaireaux)); }
            array_push($blaireaux, $ind);
        }
        $i = 0;
        
        //Création des joueurs
        foreach($_joueurs as $j){
            $coords = explode(':',$this->coordonneesLibres());
            $x = $coords[0]; $y = $coords[1];
            if(!in_array($i,$blaireaux)) { array_push($this->joueurs, new Joueur($j, $x, $y, "red", "cube", $i, "kéké")); }
            else  { array_push($this->joueurs, new Joueur($j, $x, $y, "blue", "cylindre", $i, "blaireau")); }
            $i++;
        }
    }

    /**
     * Place les joueurs sur la représentation 2D de la carte
     */
    public function placerJoueurs(){
        foreach($this->joueurs as $j){
            $this->carte[$j->PositionX()][$j->PositionY()]->RendreOccupee($j->Role());
        }
    }
    
    /**
     * Cherche dans la liste des joueurs celui qui possède l'ID passé en paramètre
     * @param type $id ID du joueur à retrouver
     * @return type Joueur en question
     */
    public function TrouverJoueurID($id){
        $trouve = false; $i = 0; $j = null;
        while(!$trouve && $i < count($this->joueurs)){
            if($this->joueurs[$i]->GetID() == $id) { $j = $this->joueurs[$i]; $trouve = true; }
            $i++;
        }
        
        return $j;
    }
    
    /**
     * Cherche dans la liste des joueurs celui qui possède l'ID passé en paramètre
     * @param type $id ID du joueur à retrouver
     * @return type Joueur en question
     */
    public function TrouverIDPseudo($pseudo){
        $trouve = false; $i = 0; $id = "spec";
        while(!$trouve && $i < count($this->joueurs)){
            if($this->joueurs[$i]->Pseudo() == $pseudo) { $id = $this->joueurs[$i]->GetID(); $trouve = true; }
            $i++;
        }
        
        return $id;
    }
    
    /**
     * Cherche dans la liste des joueurs puis dans celle des bonus si besoin l'avatar aux coordonnées passées en paramètre
     * @param type $x Position en X de l'avatar
     * @param type $y Position en Y de l'avatar
     * @return type Le joueur ou le bonus trouvé
     */
    public function TrouverAvatarCoords($x,$y){
        $trouve = false; $i = 0; $a = null;
        
        //On regarde si c'est un joueur
        while(!$trouve && $i < count($this->joueurs)){
            if(intval($this->joueurs[$i]->PositionX()) == intval($x) && intval($this->joueurs[$i]->PositionY()) == intval($y)) { $a = $this->joueurs[$i]; $trouve = true; }
            $i++;
        }
        //Si non, on regarde si c'est un bonus
        $i = 0;
        while(!$trouve && $i < count($this->bonus)){
            if(intval($this->bonus[$i]->PositionX()) == intval($x) && intval($this->bonus[$i]->PositionY()) == intval($y)) { $a = $this->bonus[$i]; $trouve = true; }
            $i++;
        }
                
        return $a;
    }
    
    /**
     * Retire un joueur de la liste
     * @param type $autre Joueur à retirer
     */
    public function RetirerJoueur($autre){
        $joueurs = array();
        foreach($this->joueurs as $j){
            if($j->GetID() != $autre->GetID()) { array_push($joueurs, $j); }
        }
        $this->joueurs = $joueurs;
    }
    
    /**
     * Inverse les rôles de tous les joueurs présents sur le terrain
     */
    public function inverserRoles(){
        foreach($this->joueurs as $j){
            $j->InverserRole();
        }
    }
    
    /*------------------------------------- Fonctions relatives à la gestion des bonus -------------------------------------*/
    
    /**
     * Ajoute sur la carte un bonus
     * @param type $timer Valeur du timer à laquelle l'ajout a eu lieu
     */
    public function AjoutBonus($timer){
        $bonus = $this->BonusAleatoire($timer);                                             //On crée un bonus dont le type est aléatoire
        array_push($this->bonus, $bonus);                                                   //On l'ajoute à la liste
        $this->carte[$bonus->PositionX()][$bonus->PositionY()]->RendreOccupee("bonus");     //On indique au terrain que la case où il se trouve est maintenant occupée
    }
    
    /**
     * Supprime de la liste des bonus tous ceux qui ont expiré
     * @param type $timer Valeur du timer à laquelle la vérification a lieu
     */
    public function SupprimeBonus($timer){
        $pile = array();                                    //On va ajouter les bonus qui sont encore bons dans un pile
        
        //On regarde tous les bonus
        for($i=0; $i<count($this->bonus); $i++){
            if(!$this->bonus[$i]->EstTermine($timer)){      //S'ils ne sont pas terminés
                array_push($pile, $this->bonus[$i]);        //On les ajoute à la pile
            }
            else{                                           //Sinon on les retire de la carte
                $this->carte[$this->bonus[$i]->PositionX()][$this->bonus[$i]->PositionY()]->InverserOccupation();
            }
        }
        
        $this->bonus = $pile;                               //On ne garde que les bonus qui sont encore valables
    }
    
    /**
     * Fabrique de bonus qui en retourne un dont le type est choisi au hasard
     * @param type $timer Valeur du timer quand la création a lieu
     * @return Le bonus créé
     */
    public function BonusAleatoire($timer){
        //Coordonnées du bonus
        $coords = explode(':',$this->coordonneesLibres());
        $x = $coords[0]; $y = $coords[1];
        
        //Type de bonus, choisi au hasard
        $ran = rand(0,4);
        switch($ran){
            case 0 :    //Bottes de 7 lieux (gomme noire)
                $res = new Bottes("bonus".count($this->bonus), $x, $y, "black", "sphere", $timer-self::DUREEBONUS);
                break;
            case 1:     //Bouclier d'or (gomme blanche)
                $res = new Bouclier("bonus".count($this->bonus), $x, $y, "white", "sphere", $timer-self::DUREEBONUS);
                break;
            case 2:     //Cape d'invisibilité (gomme jaune)
                $res = new CapeInvisibilite("bonus".count($this->bonus), $x, $y, "yellow", "sphere", $timer-self::DUREEBONUS);
                break;
            case 3:     //Potion incognito (gomme orange)
                $res = new Incognito("bonus".count($this->bonus), $x, $y, "orange", "sphere", $timer-self::DUREEBONUS);
                break;
            default:    //Potion super vue (gomme magenta)
                $res = new SuperVue("bonus".count($this->bonus), $x, $y, "magenta", "sphere", $timer-self::DUREEBONUS);
                break;
        }
        
        return $res;
    }
    
    /**
     * Retire un bonus de la liste (et donc du terrain)
     * @param type $bonus Bonus à retirer
     */
    public function RetirerBonus($bonus){
        $newbonus = array();
        foreach($this->bonus as $b){
            if($b->Pseudo() != $bonus->Pseudo()) { array_push($newbonus, $b); }
        }
        $this->bonus = $newbonus;
    }
    
    /**
     * Répercute l'évolution du timer sur les bonus présents sur le terrain
     */
    public function TimerEffets(){
        foreach($this->joueurs as $j){
            $j->DiminuerTempsEffet();
        }
    }
    
    /*------------------------------------- Fonctions relatives aux déplacements des joueurs -------------------------------------*/
    
    /**
     * Deplace un joueur sur le terrain
     * @param type $joueur
     * @param type $dx Nombre de cases à se déplacer en X
     * @param type $dy Nombre de cases à se déplacer en Y
     */
    public function Deplacer($joueur, $dx, $dy){
        if(!($dx == 0 && $dy ==0)){                                 //Si aucun déplacement n'est demandé ça ne sert à rien de faire les actions suivantes
            $j = $this->TrouverJoueurID($joueur);                   //On récupère le joueur à déplacer
            if($j != null){                                         //Si on l'a trouvé
                for($i=0; $i<$j->Vitesse(); $i++){                  //Un des bonus permet d'augmenter la vitesse. Quand on l'obtient, on ré-effectue le déplacement le nombre de fois voulu (le déplacement est donc plus rapide)
                    $x = $j->PositionX(); $y = $j->PositionY();     //On regarde la position du joueur
                    $this->carte[$x][$y]->InverserOccupation();     //On indique qu'il s'en va (la case est de nouveau libre)
                    $this->Teleporter($j, $x + $dx, $y + $dy);      //On envoie le joueur sur la case en question
                }
                if($j->PositionX() == $x && $j->PositionY() == $y) { $this->carte[$x][$y]->RendreOccupee($j->Role()); }  //Cas où aucun déplacement n'a eu lieu, on indique que la case est au final toujours occupée
                else if($this->TrouverAvatarCoords($x, $y) != null) { $this->carte[$x][$y]->RendreOccupee("kéké"); }     //Cas où il y a eu un déplacement mais qu'il y a toujours quelqu'un ici (deux kékés se sont croisés)
            }
        }
    }
        
    /**
     * Modifie la position d'un joueur sur la carte
     * @param {type} joueur ID du joueur à téléporter
     * @param {type} posX Position en x où le déplacer
     * @param {type} posY Position en y où le déplacer
     */
    public function Teleporter($joueur, $posX, $posY){
        $x = $posX; $y = $posY; $action = 0;
        //On veut être ramené de l'autre côté de la map si on sort
        if($y >= self::TAILLE) { $y = 0; }            
        else if($y < 0) { $y = self::TAILLE-1; }
        
        if($this->carte[$x][$y]->estNavigable()){  //Si la case où on souhaite se rendre n'est pas navigable il n'y a rien à faire
            if($this->carte[$x][$y]->estMagique()){  //Si la case où on souhaite se rendre est magique alors il faut téléporter le joueur à une autre case magique
                $ind = $this->TrouverCaseMagique($x, $y);                               //On en cherche une
                $x = $this->magiques[$ind][0]; $y = $this->magiques[$ind][1];           //On récupère ses coordonnées
            }
                        
            if($this->carte[$x][$y]->estOccupee()){                 //Si la case où on souhaite se rendre est occupée par un autre joueur (ou par un bonus)
                $autre = $this->TrouverAvatarCoords($x, $y);        //On cherche de qui il s'agit
                $action = $joueur->EntrerEnContact($autre);         //On demande le comportement à avoir en fonction des rôles de chacun
                $this->Collisions($action, $joueur, $autre);        //On récupercute les résultats sur la carte
            }
            
            //On déplace le joueur aux coordonnées obtenues au final
            $joueur->Deplacer($x,$y);
            if($action >= 0){
                $this->carte[$x][$y]->RendreOccupee($joueur->Role());   //Si l'action est inférieure à 0 alors on est mort, il ne faut alors pas récpercuter le déplacement sur la carte
            }
        }
    }
    
    /**
     * Trouve une case magique différente de celle sur laquelle est le joueur
     * @param type $x Position en X du joueur
     * @param type $y Position en Y du joueur
     */
    public function TrouverCaseMagique($x,$y){
        $ind = rand(0,count($this->magiques));
        while($this->magiques[$ind][0] == $x && $this->magiques[$ind][1] == $y) { $ind = rand(0,count($this->magiques)); }
        return $ind;
    }
    
    /**
     * En fonction du résultat retourné par la fonction de collision du joueur on répercute les conséquences sur la map
     * @param type $action
     * @param type $joueur
     * @param type $autre
     */
    public function Collisions($action, $joueur, $autre){
        switch($action){
            case -1:        //On s'est fait manger mais l'autre est toujours vivant
                $this->RetirerJoueur($joueur);
                break;
            case 1:         //On a mangé l'autre
                $this->RetirerJoueur($autre);
                break;
            case -2:        //On a mangé l'autre mais il nous a mangé aussi
                $this->RetirerJoueur($joueur);
                $this->carte[$autre->PositionX()][$autre->PositionY()]->InverserOccupation();
                $this->RetirerJoueur($autre);
                break;
            case 2:         //L'autre était en fait un bonus
                $this->RetirerBonus($autre);
                break;
        }
    }
    
    /*------------------------------------- Fonctions relatives à l'exportation de l'état du jeu au format XML -------------------------------------*/
    
    /**
     * Exporte une version XML du terrain
     * @return \SimpleXMLElement
     */
    public function ExporterCarte(){
        $c = new SimpleXMLElement('<carte/>');
        for($i=0; $i<self::TAILLE; $i++){
            for($j=0; $j<self::TAILLE; $j++){
                $case = $c->addChild('case');
                $case->addChild('x',$i);
                $case->addChild('y',$j);
                $case->addChild('navigable',boolval($this->carte[$i][$j]->estNavigable()) ? 'O' : 'X');
                $case->addChild('magique',boolval($this->carte[$i][$j]->estMagique()) ? 'M' : 'O');
                $case->addChild('occupee',$this->carte[$i][$j]->OccupeePar());
            }
        }
        
        return $c;
    }
    
    /**
     * Exporte une version XML de la liste des joueurs
     * @return \SimpleXMLElement
     */
    public function ExporterJoueurs(){
        $j = new SimpleXMLElement('<joueurs/>');
        for($i=0; $i<count($this->joueurs); $i++){
            $joueur = $j->addChild('joueur');
            $joueur->addChild('ID',$this->joueurs[$i]->GetID());
            $joueur->addChild('pseudo',$this->joueurs[$i]->Pseudo());
            $joueur->addChild('posX',$this->joueurs[$i]->PositionX());
            $joueur->addChild('posY',$this->joueurs[$i]->PositionY());
            $joueur->addChild('orientation',$this->joueurs[$i]->Orientation());
            $joueur->addChild('primitive',$this->joueurs[$i]->Primitive());
            $joueur->addChild('couleur',$this->joueurs[$i]->Couleur());
            $joueur->addChild('role',$this->joueurs[$i]->Role());
            $joueur->addChild('rapide',boolval($this->joueurs[$i]->Vitesse() != 1) ? 'V' : 'X');
            $joueur->addChild('intouchable',boolval($this->joueurs[$i]->EstIntouchable()) ? 'V' : 'X');
            $joueur->addChild('incognito',boolval($this->joueurs[$i]->EstIncognito()) ? 'V' : 'X');
            $joueur->addChild('invisible',boolval($this->joueurs[$i]->EstInvisible()) ? 'V' : 'X');
            $joueur->addChild('superVue',boolval($this->joueurs[$i]->EstSuperVue()) ? 'V' : 'X');
            $joueur->addChild('couleurDeBase',$this->joueurs[$i]->CouleurDeBase());
        }
        
        return $j;
    }
    
    /*------------------------------------- Getters/Setters -------------------------------------*/
    
    /**
     * Permet d'obtenir la liste des joueurs
     * @return type
     */
    public function getListeJoueurs(){
        return $this->joueurs;
    }
    
    /**
     * Permet d'obtenir la liste des bonus
     * @return type
     */
    public function getListeBonus(){
        return $this->bonus;
    }
}
