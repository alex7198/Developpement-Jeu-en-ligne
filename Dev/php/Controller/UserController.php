<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 04/04/2019
 * Time: 17:23
 */

//Contrôleur général qui réagit aux actions de l'utilisateur sur le site

class UserController
{
    function __construct()
    {
        global $vues;
        try {
            //On teste la valeur de la variable action et on fait le traitement adaquat.
            $action = $_REQUEST['action'];
            switch ($action) {
                case 'arrivee':
                    //Cas par défaut, on revient à l'accueil du site
                    require($vues['accueil']);
                    break;
                case 'click_inscription':
                    //Quand l'utilisateur souhaite afficher le formulaire de connexion
                    $this->click_inscription();
                    break;
                case 'click_connexion':
                    //Quand l'utilisateur souhaite afficher le formulaire d'inscription
                    $this->click_connexion();
                    break;
                case 'valider_connexion':
                    //Quand l'utilisateur valide sa connexion
                    $this->valider_connexion();
                    break;
                case 'inscription':
                    //Quand l'utilisateur valide son inscription
                    $this->inscription();
                    break;
                case 'deconnexion':
                    //Quand l'utilisateur clique sur le bouton de la page principale pour se déconnecter
                    $this->deconnexion();
                    break;
                case 'creation_partie':
                    //Quand l'utilisateur clique sur le bouton de la page principale pour créer une partie
                    $this->creation_partie();
                    break;
                case 'rejoindre_partie':
                    //Quand l'utilisateur clique sur le bouton de la page principale pour rejoindre une partie
                    $this->rejoindre_partie();
                    break;
                case 'retour_principal':
                    //Quand l'utilisateur clique sur un bouton quelque part sur le site pour revenir à la page principale
                    $this->retour_page_principal();
                    break;
                case 'attente_partie':
                    //Quand l'utilisateur valide la création de sa partie et part vers la salle d'attente
                    $this->attente_partie();
                    break;
                case 'rejoindre_attente':
                    //Quand l'utilisateur souhaite rejoindre une salle d'attente depuis la page pour rejoindre une partie
                    $this->rejoindre_attente();
                    break;
                case 'rejoindre_jeu':
                    //Quand l'utilisateur souhaite rejoindre une partie en tant que spectateur
                    $this->rejoindre_jeu();
                    break;
                case 'liste_joueurs':
                    //Récupère pour la salle d'attente la liste des joueurs qui attendent
                    $this->listeJoueursPartie();
                    break;
                case 'est_createur':
                    //Regarde pour la salle d'attente si le client est le créateur de la partie
                    $this->EstCreateur();
                    break;
                case 'recuperer_statut':
                    //Regarde pour la salle d'attente le statut de la partie
                    $this->recuperer_statut();
                    break;
                case 'quitter_partie':
                    //Quand l'utilisateur quitte une salle d'attente
                    $this->quitter_partie();
                    break;
                case 'lancer_partie':
                    //Quand le créateur lance la partie depuis la salle d'attente
                    $this->lancer_partie();
                    break;
                case 'quitter_jeu':
                    //Quand l'utilisateur quitte le jeu
                    $this->quitter_jeu();
                    break;
                case 'validation_mail':
                    $this->valider_mail();
                    break;
            }
        } catch (Exception $e) { }
    }

    /**
     * Quand l'utilisateur veut afficher le formulaire d'inscription
     */
    private function  click_inscription()
    {
        global $vues;
        $_SESSION[$_COOKIE["IDJoueur"]]['mode']=1;
        require($vues['accueil']);
        unset($_SESSION[$_COOKIE["IDJoueur"]]['mode']);
    }

    /**
     * Quand l'utilisateur veut afficher le formulaire de connexion
     */
    private function  click_connexion()
    {
        global $vues;
        $_SESSION[$_COOKIE["IDJoueur"]]['mode']=0;
        require($vues['accueil']);
    }
    
    /**
     * Quand l'utilisateur clique sur le bouton pour se connecter
     */
    private function valider_connexion()
    {
        global $vues;
        try {
            if (isset($_REQUEST['pseudo']) && isset($_REQUEST['mdp'])) {
                $ps = $_REQUEST['pseudo']; $mdp = $_REQUEST['mdp'];
                $m = new UserModele();
                $m->connection($ps, $mdp);
                setcookie("IDJoueur",$ps);      //On stocke le pseudo du joueur (unique) dans ses cookies
                require($vues['principal']);
            }
        }
        catch(Exception $e)
        {
            //En cas d'erreur lors de la connexion on récupère le message
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur']=$e->getMessage();
            require($vues['accueil']);
        }
    }

    /**
     * Quand l'utilisateur clique sur le bouton pour s'inscrire
     */
    private function inscription()
    {
        global $vues;
        try {
            if (isset($_REQUEST['pseudo']) && isset($_REQUEST['email']) && isset($_REQUEST['email_conf']) && isset($_REQUEST['mdp']) && isset($_REQUEST['mdp_conf'])) {
                $ps = $_REQUEST['pseudo']; $mdp = $_REQUEST['mdp']; $mdp_conf=$_REQUEST['mdp_conf']; $email=$_REQUEST['email']; $email_conf=$_REQUEST['email_conf'];
                $m = new UserModele();
                $m->inscription($ps,$mdp,$mdp_conf,$email,$email_conf);
                require($vues['notification']);
                unset($_SESSION[$_COOKIE["IDJoueur"]]['notification']);
            }
        }
        catch(Exception $e)
        {
            $_SESSION[$_COOKIE["IDJoueur"]]['mode']=1;
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = $e->getMessage();
            require($vues['accueil']);
            unset($_SESSION[$_COOKIE["IDJoueur"]]['erreur']);
        }
    }

    /**
     * Quand l'utilisateur clique sur le bouton de déconnexion de la page principale
     */
    private function deconnexion()
    {
        global $vues;
        session_destroy();
        require($vues['accueil']);
    }

    /**
     * Quand l'utilisateur veut accéder à la page de création d'une partie
     */
    private function creation_partie()
    {
        global $vues;
        require($vues['creation']);
    }

    /**
     * Quand l'utilisateur veut accéder à la page pour rejoindre une partie
     */
    private function rejoindre_partie()
    {
        global $vues;
        $m = new UserModele();
        try{
            //On récupère les différentes parties
            $tabInvitations=$m->getInvitations($_COOKIE["IDJoueur"]);
            $tabPartieEnAttente=$m->recupererPartieEnAttente();
            $tabPartieEnCours=$m->recupererPartieEnCours();
            
            //On affiche la page
            require($vues['rejoindre']);
        }
        catch (Exception $e)
        {
            echo $e;
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = $e->getMessage();
            require($vues['rejoindre']);
        }
    }

    /**
     * Quand l'utilisateur clique sur un bouton qui ramène à la page principale (sauf ceux dans les salles d'attente et dans les salles de jeu)
     */
    private function retour_page_principal()
    {
        global $vues;
        require($vues['principal']);
    }

    /**
     * Quand l'utilisateur finit de créer sa partie et rejoint la salle d'attente
     */
    private function attente_partie()
    {
        global $vues;
        try {
            if (isset($_REQUEST['game_name']) && (isset($_REQUEST['gamePrive']) || isset($_REQUEST['gamePublique'])) && isset($_REQUEST['playerNumber']) && isset($_REQUEST['duree']) && isset($_REQUEST['game_password'])) {
                //Récupération des données de l'URL
                $id = time().""; $nom = $_REQUEST['game_name']; $createur = $_COOKIE['IDJoueur'];
                if(isset($_REQUEST['gamePrive'])) { $type = "Privee"; }
                else { $type = "Publique"; }
                $nbJoueurs = intval($_REQUEST['playerNumber']); $mdp = $_REQUEST['game_password']; $duree = $_REQUEST['duree']; $dateDebut = date('d m Y H:i');
                
                $m = new UserModele();
                
                //Protection pour éviter qu'une nouvelle partie soit créée au rafrachissement de la page
                $AncienID = $this->memePartie($id, $nom, $createur, $type, $nbJoueurs, $mdp, $duree);
                if($AncienID === null){ 
                    $m->supprimerPartiesVides();
                    $m->insertionPartieBD($id,$nom,$createur,$nbJoueurs,$type,$mdp,$duree,"EnAttente",$dateDebut,"",""); 
                    $m->insertionParticipationBD($id,$createur,"");
                }
                else { $id = $AncienID; }
                $joueurs = $m->recupererJoueursPartie($id); 
                require($vues['attente']);
            }
            else { require($vues['creation']); }
        }
        catch(Exception $e)
        {
            echo $e;
            require($vues['creation']);
        }
    }
    
    /**
     * Compare deux parties pour savoir si elles sont identiques
     */
    private function memePartie($id,$nom,$createur,$type,$nbJoueurs,$mdp,$duree){
        $m = new UserModele();
        $ancienne = $m->recupererDernierePartie($createur);
        //Deux parties sont jugées identiques si elles possèdent les mêmes attributs et qu'elles ont été créées à moins de 24h d'intervalle
        if(($ancienne['Createur'] == $createur) && 
        ($ancienne['Nom'] == $nom) && 
        ($ancienne['MaxJoueur'] == $nbJoueurs) && 
        ($ancienne['Mdp'] == $mdp) && 
        ($ancienne['Type'] == $type) && 
        ($ancienne['Duree'] == $duree) &&
        (intval($ancienne['IDPartie']) >= intval($id) - 86400))
        {
            return $ancienne['IDPartie'];
        }
        else { return null; }
    }
    
    /**
     * Quand l'utilisateur rejoint une partie depuis la page en question
     */
    private function rejoindre_attente(){
        global $vues;
        try {
            if (isset($_REQUEST['IDPartie']) && isset($_REQUEST['MDPPartie']) && isset($_REQUEST['TypePartie'])) {
                $id = $_REQUEST['IDPartie']; $mdp = $_REQUEST['MDPPartie']; $type = $_REQUEST['TypePartie'];
                $m = new UserModele();
                //On ne vérifie le mot de passe que si la partie n'est pas publique. Le joueur ne peut se connecter que si la partie n'est pas déjà pleine
                if(($type === "Publique" || $m->verifMdpPartie($id,$mdp)) && $m->recupererNbJoueursPartie($id) < $m->recupererJoueursMaxPartie($id)) {          
                    try { $m->insertionParticipationBD($id,$_COOKIE["IDJoueur"],""); } catch(Exception $e) {}
                    $joueurs = $m->recupererJoueursPartie($id); 
                    require($vues['attente']); 
                }
                else { $this->rejoindre_partie(); }
            }
            else { $this->rejoindre_partie(); }
        }
        catch(Exception $e)
        {
            echo $e;
            $this->rejoindre_partie();
        }        
    }
    
    /**
     * Quand une personne rejoint une partie pour la regarde en tant que spectateur
     */
    private function rejoindre_jeu()
    {
        global $vues;
        try {
            if (isset($_REQUEST['IDPartie']) && isset($_REQUEST['MDPPartie']) && isset($_REQUEST['TypePartie'])) {
                $id = $_REQUEST['IDPartie']; $mdp = $_REQUEST['MDPPartie']; $type = $_REQUEST['TypePartie'];
                $m = new UserModele();
                if($type === "Publique" || $m->verifMdpPartie($id,$mdp)) { 
                    try { $m->insertionParticipationBD($id,$_COOKIE["IDJoueur"],""); } catch(Exception $e) {}
                    $joueurs = $m->recupererJoueursPartie($id); 
                    require($vues['jeu']); 
                }
                else { 
                    if(isset($id) && isset($mdp)) { require($vues['jeu']); }
                    else { $this->rejoindre_partie(); }
                }
            }
            else { 
                if(isset($_REQUEST['IDPartie'])) { require($vues['jeu']); }
                else { $this->rejoindre_partie();  }
            }
        }
        catch(Exception $e)
        {
            echo $e;
            $this->rejoindre_partie();
        }      
    }
    
    /**
     * Récupère la liste des joueurs pour une partie
     */
    private function listeJoueursPartie(){
        $m = new UserModele();
        $joueurs = array();
        foreach($m->recupererJoueursPartie($_REQUEST["id"]) as $joueur){
            array_push($joueurs,$joueur["IDJoueur"]);
        }
        echo implode("|",$joueurs);
    }
    
    /**
     * Regarde si un utilisateur peut lancer une partie ou non
     */
    private function EstCreateur(){
        $m = new UserModele();
        echo $m->peutLancer($_REQUEST["IDPartie"], $_REQUEST["IDJoueur"]);
    }
    
    /**
     * Regarde si une partie est enCours ou non
     */
    private function recuperer_statut(){
        $m = new UserModele();
        echo $m->recupererStatutPartie($_REQUEST["IDPartie"]);
    }

    /**
     * Quand l'utilisateur clique sur le bouton pour quitter la page de jeu
     */
    private function quitter_partie()
    {
        global $vues;
        $m = new UserModele();
        $m->suppressionParticipationBD($_REQUEST['IDPartie'],$_REQUEST['IDJoueur']);
        require($vues['principal']);
    }

    /**
     * Quand le créateur clique sur le bouton pour lancer la partie
     */
    private function lancer_partie()
    {
        global $vues;
        if(isset($_REQUEST['IDPartie'])) { 
            $id = $_REQUEST['IDPartie'];
            unset($_REQUEST['IDPartie']);
            $m = new UserModele();
            $joueurs = array();
            foreach($m->recupererJoueursPartie($id) as $joueur){
                array_push($joueurs, $joueur["IDJoueur"]);
            }
            $duree = intval($m->recupererDureePartie($id))*60;
            if(!isset($_SESSION["P".$id]["partie"])) { $_SESSION["P".$id]["partie"] = new Partie($id,$_COOKIE["IDJoueur"],$duree,$joueurs, new UserModele()); }
            require($vues['jeu']);
        }
        else { 
            require($vues['principal']);
        }
    }

    /**
     * Quand l'utilisateur clique sur le bouton du jeu pour quitter
     */
    private function quitter_jeu()
    {
        global $vues;
        $this->retour_page_principal();
    }

    private function valider_mail()
    {
        global $vues;
        try {
            $mail = $_REQUEST['mail'];
            $cle = $_REQUEST['cle'];
            $m = new UserModele();
            $m->validationCompte($mail, $cle);
            require($vues['validation']);
        }
        catch (Exception $e)
        {
            $_SESSION[$_COOKIE["IDJoueur"]]['notification']=$e->getMessage();
            require($vues['notification']);
        }
    }
}

