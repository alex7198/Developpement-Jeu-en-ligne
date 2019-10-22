<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 05/04/2019
 * Time: 20:16
 */

class UserGateway
{
    private $connexion;

    public function __construct()
    {
        $this->dbh=null;
        $this->connexion = new Connection();
    }
    
    /*------------------------------------- Requêtes liées à l'inscription et à l'authentification -------------------------------------*/
    
    //Recherche un utilisateur dans la base à partir de son pseudo
    public function recherche_user($pseudo)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM UTILISATEUR where pseudo=:pseudo");
        $stmt->bindParam(':pseudo', $pseudo);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return $stmt->fetchAll()[0][2]; }
        }
        return null;
    }
    
    //Recherche si un utilisateur possède déjà l'adresse mail passée en paramètre
    public function recherche_email($mail)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM UTILISATEUR where mail=:email");
        $stmt->bindParam(':email', $mail);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return true; }
        }
        return false;
    }
    
    //Insert un utilisateur dans la base
    public function insert_user($pseudo,$mdp,$email,$cle)
    {
        $stmt = $this->connexion->dbh->prepare("INSERT INTO UTILISATEUR VALUES(:pseudo,:email,:mdp,0,:cle,0)");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':cle', $cle);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Vérifie si l'utilisateur a activé son compte ou non
    public function verifCompteActif($pseudo)
    {
        $actif=1;
        $stmt = $this->connexion->dbh->prepare("SELECT isActive FROM UTILISATEUR WHERE pseudo=:pseudo AND isActive=:isActive");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':isActive', $actif);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return true; }
        }
        return false;
    }

    //Vérifie si l'adresse mail passée en paramètre a validé son compte ou non
    public function recherche_compte_actif($mail)
    {
        $actif=0;
        $stmt = $this->connexion->dbh->prepare("SELECT isActive FROM UTILISATEUR WHERE mail=:mail");
        if($stmt->execute(array(':mail' => $mail)) && $row = $stmt->fetch())
        {
            $actif = $row['isActive'];
        }
        return $actif;
    }

    //Récupère la clé associée au compte enregistré avec le mail passé en paramètre
    public function recherche_cle($mail)
    {
        $cle=null;
        $stmt = $this->connexion->dbh->prepare("SELECT cle FROM UTILISATEUR WHERE mail=:mail");
        if($stmt->execute(array(':mail' => $mail)) && $row = $stmt->fetch())
        {
            $cle = $row['cle'];
        }
        return $cle;
    }

    //Active le compte d'un joueur à partir de son adresse mail
    public function validation_compte($mail)
    {
        $stmt = $this->connexion->dbh->prepare("UPDATE UTILISATEUR SET isActive=1 WHERE mail=:mail");
        if($stmt->execute(array(':mail' => $mail))) { return true; }
        return false;
    }
    
    /*------------------------------------- Requêtes liées à l'administration -------------------------------------*/
    
    //Regarde si un utilisateur est admin ou pas
    public function est_admin($pseudo)
    {
        $admin=1;
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM UTILISATEUR WHERE pseudo=:pseudo AND isAdmin=:isAdmin");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':isAdmin', $admin);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return true; }
        }
        return false;
    }
    
    //Récupère la liste des adhérents du site
    public function get_users()
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM UTILISATEUR");
        if($stmt->execute()) {
            return $stmt->fetchAll();
        }
        return null;
    }
    
    //Retire un utilisateur de la liste des inscrits
    public function bannir_mail($mail)
    {
        $stmt = $this->connexion->dbh->prepare("DELETE FROM UTILISATEUR WHERE mail=:email");
        $stmt->bindParam(':email', $mail);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Retire un utilisateur de la liste des inscrits
    public function ajoute_bannis($mail,$raison,$date)
    {
        $stmt = $this->connexion->dbh->prepare("INSERT INTO BANNIS VALUES (:email,:raison,:date)");
        $stmt->bindParam(':email', $mail);
        $stmt->bindParam(':raison', $raison);
        $stmt->bindParam(':date', $date);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    /*------------------------------------- Requêtes liées à l'invitation des joueurs -------------------------------------*/
    
    //Ajoute une invitation dans la table en question
    public function inviter_joueur($IDPartie,$exp,$dest,$date)
    {
        $stmt = $this->connexion->dbh->prepare("INSERT INTO INVITATION VALUES (:partie,:exp,:dest,:date)");
        $stmt->bindParam(':partie', $IDPartie);
        $stmt->bindParam(':exp', $exp);
        $stmt->bindParam(':dest', $dest);
        $stmt->bindParam(':date', $date);
        if($stmt->execute()) { return "ok"; }
        return $stmt->errorInfo()[2];
    }
    
    /*------------------------------------- Requêtes liées à la récupération des parties -------------------------------------*/
    
    //Récupère toutes les parties dont le statut est "EnAttente"
    public function getPartieEnAttente()
    {
        $statut="EnAttente";
        $stmt = $this->connexion->dbh->prepare("SELECT IDPartie, Nom, MaxJoueur,Type FROM PARTIE WHERE Statut=:statut");
        $stmt->bindParam(':statut', $statut);
        if($stmt->execute()) { return $stmt->fetchAll(); }
        else { return $stmt->errorInfo(); }
    }
    
    //Récupère toutes les parties publiques en cours (on ne veut pas qu'une partie privée puisse être suivie en tant que spectateur)
    public function getPartieEnCours()
    {
        $statut="EnCours";
        $stmt = $this->connexion->dbh->prepare("SELECT IDPartie, Nom, MaxJoueur,Type FROM PARTIE WHERE Statut=:statut AND Type='Publique'");
        $stmt->bindParam(':statut', $statut);
        if($stmt->execute()) { return $stmt->fetchAll(); }
        return null;
    }
    
    //Récupère toutes les invitations reçues par un joueur pour des parties qui sont encore en attente
    public function getInvitations($pseudo)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT IDPartie, Nom, MaxJoueur, Type, Expediteur FROM PARTIE NATURAL JOIN INVITATION WHERE Destinataire=:pseudo AND Statut='EnAttente'");
        $stmt->bindParam(':pseudo', $pseudo);
        if($stmt->execute()) { return $stmt->fetchAll(); }
        return null;
    }
    
    //Retourne l'id de la dernière partie créée par un joueur
    public function derniere_game($createur)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM PARTIE WHERE Createur=:createur ORDER BY IDPartie desc");
        $stmt->bindParam(':createur', $createur);
        if($stmt->execute()) { return $stmt->fetch(); }
        return null;
    }

    //Vérifie que le mot de passe passé en paramètre correspond à celui enregistré pour la partie privée
    public function verifMdpPartie($IDPartie,$mdp)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM PARTIE WHERE IDPartie=:IDPartie AND Mdp=:mdp");
        $stmt->bindParam(':IDPartie', $IDPartie);
        $stmt->bindParam(':mdp', $mdp);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return true; }
        }
        return false;
    }
    
    /*------------------------------------- Requêtes liées à la création de parties -------------------------------------*/
    
    //Vérifie que le joueur passé en paramètre est le créateur de la partie
    public function verifPeutLancer($IDPartie,$IDJoueur)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT * FROM PARTIE WHERE IDPartie=:IDPartie AND Createur=:joueur");
        $stmt->bindParam(':IDPartie', $IDPartie);
        $stmt->bindParam(':joueur', $IDJoueur);
        if($stmt->execute()) {
            if ($stmt->rowCount() == 1) { return true; }
        }
        return false;
    }
    
    //Insère une partie dans la base
    public function insert_partie($id,$nom,$createur,$maxJoueurs,$type,$mdp,$duree,$statut,$dateDebut,$dateFin,$gagnants)
    {
        $stmt = $this->connexion->dbh->prepare("INSERT INTO PARTIE VALUES(:id,:nom,:createur,:maxjoueurs,:type,:mdp,:duree,:statut,:dateDebut,:dateFin,:gagnants)");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':createur', $createur);
        $stmt->bindParam(':maxjoueurs', $maxJoueurs);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':duree', $duree);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->bindParam(':gagnants', $gagnants);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Insère une participation
    public function insert_participation($partie,$joueur,$couleur)
    {
        $stmt = $this->connexion->dbh->prepare("INSERT INTO PARTICIPE VALUES(:partie,:joueur,:couleur)");
        $stmt->bindParam(':partie', $partie);
        $stmt->bindParam(':joueur', $joueur);
        $stmt->bindParam(':couleur', $couleur);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Retire la participation du joueur pour la partie
    public function remove_participation($partie,$joueur)
    {
        $stmt = $this->connexion->dbh->prepare("DELETE FROM PARTICIPE WHERE IDPartie=:partie AND IDJoueur=:joueur");
        $stmt->bindParam(':partie', $partie);
        $stmt->bindParam(':joueur', $joueur);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Retire de la BD toutes les parties qui ne possèdent aucun joueur en attente
    public function remove_vides()
    {
        $stmt = $this->connexion->dbh->prepare("DELETE FROM INVITATION WHERE IDPartie NOT IN (SELECT IDPartie FROM PARTICIPE)");
        if($stmt->execute()) { 
            $stmt = $this->connexion->dbh->prepare("DELETE FROM PARTIE WHERE IDPartie NOT IN (SELECT IDPartie FROM PARTICIPE)");
            if($stmt->execute()) { 
                return true; 
            } 
        }
        return false;
    }
    
    //Spécifie la couleur d'un joueur pour la partie
    public function changer_couleur($partie,$joueur,$couleur)
    {
        $stmt = $this->connexion->dbh->prepare("UPDATE PARTICIPE SET Couleur=:couleur WHERE IDPartie=:partie AND IDJoueur=:joueur");
        $stmt->bindParam(':partie', $partie);
        $stmt->bindParam(':joueur', $joueur);
        $stmt->bindParam(':couleur', $couleur);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Démarre une partie
    public function demarrer_partie($partie)
    {
        $stmt = $this->connexion->dbh->prepare("UPDATE PARTIE SET Statut='EnCours' WHERE IDPartie=:partie");
        $stmt->bindParam(':partie', $partie);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    //Arrête une partie
    public function arreter_partie($partie, $couleurGagnants, $dateFin)
    {
        $stmt = $this->connexion->dbh->prepare("UPDATE PARTIE SET Statut='Terminee', Gagnants=:gagnants, DateFin=:datefin WHERE IDPartie=:partie");
        $stmt->bindParam(':partie', $partie);
        $stmt->bindParam(':gagnants', $couleurGagnants);
        $stmt->bindParam(':datefin', $dateFin);
        if($stmt->execute()) { return true; }
        return false;
    }
    
    /*------------------------------------- Requêtes liées à la récupération de données pour l'affichage -------------------------------------*/
    
    //Récupère le nombre de participants de la partie
    public function getNbParticipantsPartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT COUNT(*) AS nbJoueurs FROM PARTICIPE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetch()["nbJoueurs"]; }
        return null;
    }
    
    //Récupère la liste des participants de la partie
    public function getParticipantsPartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT IDJoueur FROM PARTICIPE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetchAll(); }
        return null;
    }
    
    //Récupère le nombre de participants maximum pour une partie
    public function getParticipantsMaxPartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT MaxJoueur FROM PARTIE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetch()["MaxJoueur"]; }
        return null;
    }
    
    //Récupère la durée d'une partie
    public function getDureePartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT Duree FROM PARTIE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetch()["Duree"]; }
        return null;
    }
    
    //Récupère le pseudo du créateur de la partie
    public function getNomCreateurPartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT Nom, Createur FROM PARTIE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetch(); }
        return null;
    }
    
    //Récupère le statut d'une partie
    public function verifStatutPartie($IDPartie)
    {
        $stmt = $this->connexion->dbh->prepare("SELECT Statut FROM PARTIE WHERE IDPartie=:IDPartie");
        $stmt->bindParam(':IDPartie', $IDPartie);
        if($stmt->execute()) { return $stmt->fetch()["Statut"]; }
        return null;
    }
}