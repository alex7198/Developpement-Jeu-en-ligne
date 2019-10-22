/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*------------------------------------- Création du moteur AJAX -------------------------------------*/

/**
 * Crée le moteur AJAX qui va récupérer les données sur le serveur en fonction du navigateur du client
 * @returns {ActiveXObject|XMLHttpRequest}
 */
function creationXHR(){
    var resultat = null;
    
    try{    //La plupart des navigateurs (Chrome, Opera, Mozilla, ...)
        resultat = new XMLHttpRequest();
    }
    catch(Error){
        try{    //Internet Explorer version > 5.0
            resultat = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(Error){
            try{    //Internet Explorer version 5.0
                resultat = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(Error){
                resultat = null;
            }
        }
    }
    return resultat;
}

/*------------------------------------- Appels de fonctions à intervalle de temps régulier -------------------------------------*/

/**
 * Récupère l'état des joueurs sur le serveur toutes les 100 ms
 */
function boucleJoueurs(){
    interval_1 = setInterval(getJoueurs,500);
}

/**
 * Décrémente et récupère la valeur du timer toutes les secondes
 */
function boucleTimer(){
    interval_2 = setInterval(getTimer,1000);
}

/*------------------------------------- Récupérations de données sur le serveur -------------------------------------*/

/**
 * Récupère la représentation XML du terrain stocké sur le serveur
 * Le traitement de cette représentation est fait dans la fonction "recupererTerrain" (voir ci-dessous)
 * @returns {undefined}
 */
function getTerrain(){
    objetXHR = creationXHR();
    objetXHR.onreadystatechange = recupererTerrain;
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=GetTerrain",true);
    objetXHR.send(null);
}

/**
 * Récupère la représentation XML de la liste des joueurs stockée sur le serveur
 * @returns {undefined}
 */
function getJoueurs(){
    objetXHR = creationXHR();
    objetXHR.onreadystatechange = placerJoueurs;
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=RecupererJoueurs",true);
    objetXHR.send(null);
}

/**
 * Récupère la représentation XML du timer et de l'état de la partie
 * @returns {undefined}
 */
function getTimer(){
    objetXHR = creationXHR();
    objetXHR.onreadystatechange = recupererTimer;
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=Timer&joueur="+IDCookie(),true);
    objetXHR.send(null);
}

/**
 * Récupère l'identifiant in game du joueur courant
 * @param {type} pseudo Pseudo du joueur dont on cherche l'id
 * @returns {undefined}
 */
function getID(pseudo){
    objetXHR = creationXHR();
    objetXHR.onreadystatechange = recupererID;
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=GetID&pseudo="+pseudo,true);
    objetXHR.send(null);
}

/*------------------------------------- Mises à jour de données sur le serveur -------------------------------------*/

/**
 * Met à jour la position du joueur courant sur le serveur
 * @param {type} dh Déplacement à effectuer horizontalement
 * @param {type} dv Déplacement à effectuer verticalement
 * @returns {undefined}
 */
function updatePosition(dh,dv){
    objetXHR = creationXHR();
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=SeDeplacer&id="+ID+"&dh="+dh+"&dv="+dv,true);
    objetXHR.send(null);
}
/**
 * Met à jour l'orientation du joueur courant sur le serveur
 * @param {type} angle Angle de la rotation à effectuer
 * @returns {undefined}
 */
function updateOrientation(angle){
    objetXHR = creationXHR();
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=TournerJoueur&id="+ID+"&angle="+angle,true);
    objetXHR.send(null);
}

/**
 * Supprime la partie en cours et en crée une nouvelle
 * @returns {undefined}
 */
function resetMap(){
    objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            //Quand la nouvelle partie est créée on recharge la page
            window.location.reload();
        }
    };
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=ResetMap",true);
    objetXHR.send(null);
}

/**
 * Quitte la partie en cours
 * @returns {undefined}
 */
function quitterPartie(){
    objetXHR = creationXHR();
    objetXHR.open("get","php/Controller/JeuController.php?IDPartie="+IDPartie()+"&fct=QuitterPartie&id="+ID,true);
    objetXHR.send(null);
}

/*------------------------------------- Traitement des données reçues du serveur -------------------------------------*/

/**
 * Récupère le document XML créé par le serveur et lance la génération du terrain
 * @returns {undefined}
 */
function recupererTerrain(){  
    if(objetXHR.readyState===4 && objetXHR.status ===200){                      //Si la réponse a bien été renvoyée et que la requête est un succès
        var reponse = objetXHR.responseXML;                                     //On récupère le document XML
        creerTerrain(reponse.documentElement.getElementsByTagName("case"));     //On crée le terrain
        //getJoueurs();                                                           //On récupère les joueurs
        boucleJoueurs();
        boucleTimer();
    }
}

/**
 * Récupère le document XML créé par le serveur et place les joueurs sur le terrain
 * @returns {undefined}
 */
function placerJoueurs(){
    if(objetXHR.readyState===4 && objetXHR.status ===200){                                  //Si la réponse a bien été renvoyée et que la requête est un succès
        var reponse = objetXHR.responseXML;                                                 //On récupère le document XML
        if(reponse !== null && reponse.documentElement.nodeName === "joueurs") {            //S'il existe et qu'il s'agit bien du XML sur les joueurs (il y avait parfois des conflits avec celui du timer puisqu'il arrivait parfois que les reqûetes soient envoyées en même temps)
            creerJoueurs(reponse.documentElement.getElementsByTagName("joueur"));           //On crée les joueurs
            
            var nbJoueurs = reponse.documentElement.getElementsByTagName("joueur").length;      //On regarde combien de joueurs il y a en tout dans la partie
            if(spectateur && nbJListePrec !== nbJoueurs) {                                      //Si on est spectateur et que ce nombre est différent de celui de la dernière requête
                interfaceSpectateur(reponse.documentElement.getElementsByTagName("joueur"));    //On met à jour l'interface des spectateurs
                nbJListePrec = nbJoueurs;                                                       //On indique que pour cette requête il y avait nbJoueurs
            }
        }
    }
}

/**
 * Récupère le document XML créé par le serveur, crée les bonus si besoin et vérifie que la partie n'est pas terminée
 * @returns {undefined}
 */
function recupererTimer(){
    if(objetXHR.readyState===4 && objetXHR.status ===200){                                                  //Si la réponse a bien été renvoyée et que la requête est un succès
        var response = objetXHR.responseXML;                                                            //On récupère le document XML
        if(response !== null){                                                                          //S'il existe
            var tempsRestant = response.documentElement.getElementsByTagName("temps")[0].textContent;   //On récupère la valeur du timer
            var joueursRestants = response.documentElement.getElementsByTagName("joueur");              //On récupère la liste des joueurs restants dans la partie
            var bonus = response.documentElement.getElementsByTagName("bonus");                         //On récupère la liste des bonus présents sur le terrain
            if(bonus.length > 0) creerBonus(bonus);                                                     //S'il y en a on les ajoute dans la scène ThreeJS
            gestionTempsRestant(tempsRestant,joueursRestants);                                          //On vérifie que la partie n'est pas terminée
        }
    }
}

/**
 * Récupère la réponse du serveur concernant l'id du joueur
 * @returns {undefined}
 */
function recupererID(){
    if(objetXHR.readyState===4 && objetXHR.status ===200){     
        var response = objetXHR.responseText;       //On récupère la réponse du serveur                                                   
        if(response !== null){                      //Si elle existe  
            if(response !== "spec") ID = response;        //On affecte l'ID du joueur    
            else { ID = "0"; spectateur = true; }   //Si l'ID vaut "spec" la personne n'est pas joueur de la partie, il est donc spectateur (et observe en premier le point de vue du premier joueur inscrit)
            initJeu();                              //On lance l'initialisation du jeu
        }
    }
}
