<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 05/04/2019
 * Time: 19:13
 */

//Classe effectuant la validation des données saisies par l'utilisateur
//Elle fait appel à la base de données via la classe UserGateway

class UserModele
{
    /*------------------------------------- Requêtes liées à l'inscription et à l'authentification -------------------------------------*/
    
    public function connection($pseudo, $mdp)
    {
        //On valide les données
        $p=Validation::validePseudo($pseudo);
        $m=Validation::valideMdp($mdp);
        //Si les données ont été validées
        if($p != null && $m!=null)
        {
            //Instanciation de UserGateway pour vérifier si l'utilisateur existe
            $g=new UserGateway();
            //Si le mdp (crypté dans la base) correspond à celui écrit par l'utilisateur
            $mdp_trouve=$g->recherche_user($p);
            if($mdp_trouve==null || !password_verify($mdp,$mdp_trouve))
            {
                throw new Exception("Erreur : pseudo ou mot de passe inconnu");
            }
            if(!$g->verifCompteActif($pseudo))
            {
                throw new Exception("Erreur : votre compte n'est pas activé.");
            }
        }
        else{
            throw new Exception("Erreur : pseudo ou mot de passe incorrect.");
        }
    }

    //Méthode appelée lorsqu'un utilisateur s'inscrit
    public function inscription($pseudo, $mdp,$mdp_conf,$email, $email_conf)
    {
        global $vues;
        //On valide les données
        $p = $this->verifPseudo($pseudo);
        $mail = $this->verifMail($email);
        $mail_conf = $this->verifMail($email_conf);
        $mdp = $this->verifMdp($mdp);
        $mdp_conf = $this->verifMdp($mdp_conf);
        //Si l'email de confirmation est correcte
        if ($mail == $mail_conf) {
            //Si le mdp de confirmation est correct
            if ($mdp_conf == $mdp) {
                $g = new UserGateway();
                //On crypte le mdp
                $md_crypte = password_hash($mdp, PASSWORD_DEFAULT);
                //On vérifie si l'adresse mail n'a pas déjà été utilisée
                $this->verifMailBD($mail, $g, $p);
                //On génère la clé pour le mail
                $cle = md5(microtime(TRUE) * 100000);
                //On insère dans la bd
                $this->insertionUtilisateurBD($pseudo, $md_crypte, $mail, $cle, $g);
                //On envoie le mail de confirmation
                if(!new EnvoiMail($mail, $cle))
                {
                    throw new Exception("Erreur : impossible d'envoyer le mail de confirmation");
                }
                $_SESSION[$_COOKIE["IDJoueur"]]['notification'] = "Création du compte réussie. Un mail vient de vous être envoyé pour effectuer la validation.";
            } else {
                throw new Exception("Erreur: Les deux mots de passes ne correspondent pas.");
            }
        } else {
            throw new Exception("Erreur: Les deux adresses mail ne correspondent pas.");
        }
    }
    
    /**
     * Insère un utilisateur dans la base de données
     * g est une instance de UserGateway
     */
    private function insertionUtilisateurBD($pseudo,$mdp,$mail,$cle,$g)
    {
        if (!$g->insert_user($pseudo, $mdp, $mail, $cle)) {
            throw new Exception("Erreur : le pseudo que vous avez saisi est déjà utilisé, veuillez en saisir un nouveau");
        }
    }

    /**
     * Vérifie que l'adresse mail n'a pas déjà été utilisée
     * g est une instance de UserGateway
     */
    private function verifMailBD($mail,$g,$p)
    {
        if ($g->recherche_email($mail)) {
            throw new Exception("Erreur : l'adresse mail que vous avez saisie est déjà utilisée, veuillez en saisir une nouvelle");
        }
    }

    /**
     * Vérifie que le pseudo est correct
     */
    private function verifPseudo($pseudo)
    {
        $ps=Validation::validePseudo($pseudo);
        if($ps==null)
        {
            throw new Exception("Erreur : Pseudo incorrect : il doit comporter entre 8 et 20 caractères. Les seuls caractères spéciaux acceptés sont : \"!?_-\".");
        }
        return $pseudo;
    }

    /**
     * Vérifie que le mot de passe est correct
     */
    private function verifMdp($mdp)
    {
        $m=Validation::valideMdp($mdp);
        if($m==null)
        {
            throw new Exception("Erreur : Mot de passe incorrect : il doit comporter entre 8 et 20 caractères avec au moins une lettre majuscule, une lettre minuscule et un chiffre. Les seuls caractères spéciaux acceptés sont : \"!?_-\".");
        }
        return $m;
    }

    /**
     * Vérifie que le mail passé ressemble à un mail
     */
    private function verifMail($mail)
    {
        $m=Validation::valideEmail($mail);
        if($m==null)
        {
            throw new Exception("Erreur : Le mail que vous avez renseigné n'est pas correct");
        }
        return $m;
    }

    /**
     * Valide un compte
     * @param type $m Mail du compte à valider
     * @param type $c Clé du compte
     */
    public function validationCompte($m,$c)
    {
            $mail=urldecode($m);
            $cle=urldecode($c);
            $g=new UserGateway();
            if(!$g->recherche_compte_actif($mail))
            {
                if($g->recherche_cle($mail)==$cle)
                {
                    if(!$g->validation_compte($mail)) { throw  new Exception("Erreur : Votre compte n'a pas pu être validé."); }
                    else { $_SESSION[$_COOKIE["IDJoueur"]]['notification']="Votre compte a été validé avec succès."; }
                }
                else { throw  new Exception("Erreur : la validation du compte a échouée."); }
            }
            else { throw new Exception("Erreur : Le compte est déjà actif."); }
    }
    
    /*------------------------------------- Requêtes liées à l'administration -------------------------------------*/
    
    /**
     * Vérifie si un joueur est administrateur ou non
     */
    public function est_admin($IDJoueur){
        $g=new UserGateway();
        $result=$g->est_admin($IDJoueur);
        return $result;
    }
    
    /**
     * Récupère la liste des personnes inscrites sur le site
     */
    public function get_users()
    {
        $g=new UserGateway();
        $result=$g->get_users();
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer les adhérents du site";
            throw new Exception("Problème lors de la récupération de la liste des adhérents du site");
        }
        return $result;
    }
    
    /**
     * Retire un adhérent de la liste des inscrits et le passe dans la table des bannis
     */
    public function bannir_joueur($mail,$raison,$date)
    {
        $g=new UserGateway();
        if ($g->ajoute_bannis($mail,$raison,$date)) {
            if (!$g->bannir_mail($mail)) {
                $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de supprimer l'adhérent de la liste des joueurs";
                throw new Exception("Problème lors de la suppression du joueur de la liste des adhérents");
            }
        }
        else {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible d'ajouter le joueur à la liste des bannis";
            throw new Exception("Problème lors de l'ajout du joueur à la liste des bannis");
        }
    }
    
    /*------------------------------------- Requêtes liées à l'invitation des joueurs -------------------------------------*/
    
    /**
     * Ajoute une invitation dans la table
     */
    public function inviter_joueur($IDPartie,$exp,$dest,$date){
        $g=new UserGateway();
        $resultat = $g->inviter_joueur($IDPartie,$exp,$dest,$date);
        if($resultat === "ok"){
            return "Invitation envoyée ...";
        }
        else{
            if(strpos($resultat,"a foreign key constraint fails") !== false) { return "Le joueur invité n'existe pas ..."; }
            else { return "Vous avez déjà invité ce joueur ..."; }
        }
    }
    
    /*------------------------------------- Requêtes liées à la récupération des parties -------------------------------------*/

    /**
     * Récupère la liste des parties en attente
     */
    public function recupererPartieEnAttente()
    {
        $g=new UserGateway();
        $result=$g->getPartieEnAttente();
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer les parties en attentes";
        }
        return $result;
    }
    
    /**
     * Récupère la liste des parties en cours
     */
    public function recupererPartieEnCours()
    {
        $g=new UserGateway();
        $result=$g->getPartieEnCours();
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer les parties en cours";
        }
        return $result;
    }
    
    /**
     * Récupère la liste des invitations pour un joueur
     */
    public function getInvitations($pseudo){
        $g=new UserGateway();
        $result=$g->getInvitations($pseudo);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer les parties en attentes";
        }
        return $result;
    }
    
    /**
     * Récupère la dernière partie créée par un joueur
     */
    public function recupererDernierePartie($createur)
    {
        $g=new UserGateway();
        $result=$g->derniere_game($createur);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer la dernière partie du joueur";
        }
        return $result;
    }
    
    /*
     * Vérifie que le mot de passe passé pour une partie est correct
     */
    public function verifMdpPartie($IDPartie,$mdp){
        $g=new UserGateway();
        $result=$g->verifMdpPartie($IDPartie,$mdp);
        return $result;
    }
    
    /*------------------------------------- Requêtes liées à la création de parties -------------------------------------*/
    
    /**
     * Vérifie si un joueur peut lancer la partie ou non
     */
    public function peutLancer($IDPartie,$IDJoueur){
        $g=new UserGateway();
        $result=$g->verifPeutLancer($IDPartie,$IDJoueur);
        return $result;
    }
    
    /*
     * Ajoute une partie dans la base de données
     */
    public function insertionPartieBD($id,$nom,$createur,$maxJoueurs,$type,$mdp,$duree,$statut,$dateDebut,$dateFin,$gagnants)
    {
        $g=new UserGateway();
        if (!$g->insert_partie($id,$nom,$createur,$maxJoueurs,$type,$mdp,$duree,$statut,$dateDebut,$dateFin,$gagnants)) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible d'ajouter la partie à la base de données";
            throw new Exception("Problème lors de l'ajout à la BD");
        }
    }
    
    /*
     * Ajoute une participation à une partie dans la BD
     */
    public function insertionParticipationBD($partie,$joueur,$couleur)
    {
        $g=new UserGateway();
        if (!$g->insert_participation($partie,$joueur,$couleur)) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible d'ajouter le joueur à la partie";
            throw new Exception("Problème lors de l'ajout du joueur à la partie");
        }
    }
    
    /*
     * Retire une participation à une partie dans la BD
     */
    public function suppressionParticipationBD($partie,$joueur)
    {
        $g=new UserGateway();
        if (!$g->remove_participation($partie,$joueur)) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de retirer le joueur à la partie";
            throw new Exception("Problème lors du retrait du joueur à la partie");
        }
    }
    
    /*
     * Spécifie la couleur d'un joueur dans une partie
     */
    public function changer_couleur($partie,$joueur,$couleur){
        $g=new UserGateway();
        $result=$g->changer_couleur($partie,$joueur,$couleur);
        if (!$result) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer modifier la couleur des joueurs";
            throw new Exception("Problème lors de la récupération du changements des couleurs des joueurs");
        }
    }
    
    /*
     * Démarre une partie
     */
    public function demarrer_partie($IDPartie){
        $g=new UserGateway();
        $result=$g->demarrer_partie($IDPartie);
        if (!$result) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de démarrer la partie";
            throw new Exception("Problème lors de la récupération du démarrage de la partie");
        }
    }
    
    /*
     * Arrête une partie
     */
    public function arreter_partie($IDPartie,$couleurGagnants, $dateFin){
        $g=new UserGateway();
        $result=$g->arreter_partie($IDPartie,$couleurGagnants,$dateFin);
        if (!$result) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible d'arreter la partie";
            throw new Exception("Problème lors de la récupération de l'arret de la partie");
        }
    }
    
    /*
     * Supprime toutes les parties qui ne possèdent aucun joueur en attente
     */
    public function supprimerPartiesVides(){
        $g=new UserGateway();
        $result=$g->remove_vides();
        if (!$result) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de supprimer les parties vides";
        }
    }
    
    /*------------------------------------- Requêtes liées à la récupération de données pour l'affichage -------------------------------------*/

    /**
     * Récupère le nombre de joueurs dans une partie
     */
    public function recupererNbJoueursPartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->getNbParticipantsPartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer le nombre de joueurs dans la partie";
            throw new Exception("Problème lors de la récupération du nombre de joueurs dans la partie");
        }
        return $result;
    }
    
    /*
     * Récupère la liste des joueurs dans une partie
     */
    public function recupererJoueursPartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->getParticipantsPartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer les participants de la partie";
            throw new Exception("Problème lors de la récupération des participants de la partie");
        }
        return $result;
    }
    
    /*
     * Récupère le nombre de joueurs max dans une partie
     */
    public function recupererJoueursMaxPartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->getParticipantsMaxPartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer le nombre de participants max de la partie";
            throw new Exception("Problème lors de la récupération du nombre de participants max de la partie");
        }
        return $result;
    }
    
    /*
     * Récupère le pseudo du créateur de la partie
     */
    public function recupererNomCreateurPartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->getNomCreateurPartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer le nom et le créateur de la partie";
            throw new Exception("Problème lors de la récupération du nom et du créateur de la partie");
        }
        return $result;
    }
    
    /*
     * Récupère le statut de la partie
     */
    public function recupererStatutPartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->verifStatutPartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer le statut de la partie";
            throw new Exception("Problème lors de la récupération du statut de la partie");
        }
        return $result;
    }
    
    /*
     * Récupère la durée de la partie
     */
    public function recupererDureePartie($IDPartie)
    {
        $g=new UserGateway();
        $result=$g->getDureePartie($IDPartie);
        if ($result==null) {
            $_SESSION[$_COOKIE["IDJoueur"]]['erreur'] = "Erreur : Impossible de récupérer la durée de la partie";
            throw new Exception("Problème lors de la récupération de la durée de la partie");
        }
        return $result;
    }
}