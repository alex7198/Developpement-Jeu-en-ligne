window.onload = init;

/*------------------------------------- Initialisation -------------------------------------*/

/**
 * Initialisation du Javascript de la page
 * @returns {undefined}
 */
function init(){
    setEventListener();         //On initialise les évènements pour réagir aux actions de l'utilisateur
    initID();
}

/**
 * Initialise l'identifiant du joueur côté client
 * @returns {undefined}
 */
function initID(){
    var pseudo = IDCookie();
    if(pseudo !== "") getID(pseudo);
    else spectateur = true;
}

/**
 * Initialise le jeu
 * @returns {undefined}
 */
function initJeu(){
    initThreeJS();              //On initialise la représentation graphique du jeu
    //boucleJoueurs();            //On lance la récupération des états des joueurs
    //boucleTimer();              //On lance la récupération de l'état de la partie
}

/**
 * Récupère l'id du joueur enregistré dans le cookie
 * @returns {undefined}
 */
function IDCookie(){
    var cookie, id="";
    var cookies = document.cookie.split(';');
    for(var i=0; i<cookies.length; i++){
        cookie = cookies[i].split("=");
        if(cookie[0].includes("IDJoueur")) id = cookie[1];
    }
    return id;
}

/**
 * Récupère l'id de la partie enregistré sur la page
 * @returns {undefined}
 */
function IDPartie(){
    return document.getElementById("IDPartie").value;
}

/*------------------------------------- Evenements -------------------------------------*/

/**
 * Evènements de la page
 * @returns {undefined}
 */
function setEventListener(){
    document.onkeydown = clavier;                                                           //Quand l'utilisateur utilise son clavier
}

/**
 * Actions à effectuer quand l'utilisateur se sert de son clavier
 * Un spectateur ou une personne sous super vue ne peut pas déplacer d'avatar sur le terrain
 * @param {type} e Evenement déclenché
 * @returns {undefined}
 */
function clavier(e){
    switch(e.keyCode){
        case 68: //Appuie sur la touche D (rotation à droite)
            if(!sousSuperVue && !spectateur) updateOrientation(-90);
            break;
        case 81: //Appuie sur la touche Q (rotation à gauche)
            if(!sousSuperVue && !spectateur) updateOrientation(90);
            break;
        case 90: //Appuie sur la touche Z (déplacement en avant)
            if(!sousSuperVue && !spectateur) updatePosition(Math.sin(camera.rotation.y).toFixed(3) * -1,Math.cos(camera.rotation.y).toFixed(3) * -1);
            break;
        case 83: //Appuie sur la touche S (déplacement en arrière)
            //if(!sousSuperVue && !spectateur) updatePosition(Math.sin(camera.rotation.y).toFixed(3) * 1,Math.cos(camera.rotation.y).toFixed(3) * 1);
            break;
        case 82: //Appuie sur la touche R (à commenter quand tout sera fini, recommence une partie à la main)
            resetMap();
            break;
        case 70:    //Touche F, activation/désactivation de la Super Vue
            changerVue();
            break;
    }
}

/*------------------------------------- Affichage des informations de la partie -------------------------------------*/

/**
 * Affiche l'état de la partie
 * @param {type} nbRouge Nombre de joueurs dans l'équipe rouge
 * @param {type} nbBleu Nombre de joueurs dans l'équipe bleue
 * @returns {undefined}
 */
function messageGeneral(nbRouge,nbBleu){
    if(document.getElementById("scene") !== null){
        if(nbRouge === 0){
            //Les blaireaux ont gagné
            document.getElementById("msg_general").textContent = "Victoire de l'équipe bleue !";
            arreterPartie();
        }
        else if(nbBleu === 0){
            //Les kékés ont gagné
            document.getElementById("msg_general").textContent = "Victoire de l'équipe rouge !";
            arreterPartie();
        }
        else{
            //La partie est toujours en cours
            document.getElementById("msg_general").textContent = "Nombre de bleus : "+nbBleu+", Nombre de rouges : "+nbRouge+", Avantage pour les ";
            if(nbRouge > nbBleu) document.getElementById("msg_general").textContent += "rouges !";
            else document.getElementById("msg_general").textContent += "bleus !";
        }
    }
}

/**
 * Affiche le message de mort et arrête la partie
 * @returns {undefined}
 */
function messageMort(){
    if(document.getElementById("scene") !== null){
        document.getElementById("msg_general").textContent = "Dommage, vous êtes mort ...";
        arreterPartie();
    }
}

/**
 * Affiche le timer ou arrête la partie si le temps est écoulé
 * @param {type} tempsRestant Temps restant/Timer
 * @param {type} joueursRestants Liste des joueurs encore en vie au format XML
 * @returns {undefined}
 */
function gestionTempsRestant(tempsRestant, joueursRestants){
    var nBRouge = 0, nbBleu = 0;
        
    if(tempsRestant == 0){ //La partie est finie
        if(document.getElementById("scene") !== null){
            document.getElementById("msg_general").textContent = "Temps écoulé ! Victoire des ";
            
            //On détermine quelle équipe a gagné en comptant le nombre de joueurs dans chaque équipe
            for(var i=0; i<joueursRestants.length; i++){
                if(joueursRestants[i].getElementsByTagName("couleur")[0].textContent === "red") nBRouge++;
                else nbBleu++;
            }
            
            //On affiche l'équipe gagnante
            if(nBRouge >= nbBleu) document.getElementById("msg_general").textContent += "rouges !";
            else document.getElementById("msg_general").textContent += "bleus !";
            
            //On réinitialise les autres informations et on arrête la partie
            arreterPartie();
        }
    }
    else{   //La partie est toujours en cours
        document.getElementById("timer").textContent = "Temps restant : "+tempsRestant;
    }
}

/**
 * Ré-initialise les informations sur le joueur et sur le timer et arrête la partie
 * @returns {undefined}
 */
function arreterPartie(){
    document.getElementById("timer").textContent = "";
    document.getElementById("infos").textContent = "";
    clearInterval(interval_1); clearInterval(interval_2);                               //On coupe les communications avec le serveur
    document.getElementById("section").removeChild(document.getElementById("scene"));   //On retire l'interface de jeu de la page
}

/**
 * Affiche l'état des effets appliqués au joueur
 * @param {type} joueur Représentation XML du joueur retournée par le serveur
 * @returns {undefined}
 */
function afficherBonus(joueur){
    //On récupère les attributs qui concernent les bonus
    var vitesse = joueur.getElementsByTagName("rapide")[0].textContent;
    var intouchable = joueur.getElementsByTagName("intouchable")[0].textContent;
    var incognito = joueur.getElementsByTagName("incognito")[0].textContent;
    var invisible = joueur.getElementsByTagName("invisible")[0].textContent;
    var superVue = joueur.getElementsByTagName("superVue")[0].textContent;
    
    //On affiche leur état
    document.getElementById("bonus").textContent = "BONUS | Vitesse : "+vitesse+" - Bouclier : "+intouchable+" - Cape : "+invisible+" - Incognito : "+incognito+" - Super Vue : "+superVue;
}

/*------------------------------------- Gestion du mode spectateur -------------------------------------*/

/**
 * Edite le bout d'interface rajoutée pour les spectateurs
 * @param {type} joueurs Liste des représentations XML des joueurs encore en vie
 * @returns {undefined}
 */
function interfaceSpectateur(joueurs){
    if(spectateur){
        var child;
        //On récupère la zone où afficher la liste des vues visualisables par le spectateur
        var spec = document.getElementById("spec");
        
        //On s'assure que cette zone est vide
        reinitialiserInterfaceSpec();
        
        //On ajoute un bouton par joueur encore en vie. Chaque bouton possède comme identifiant celui du joueur dans la partie
        spec.appendChild(document.createElement("span").appendChild(document.createTextNode("Visualiser : ")));
        for(var i=0; i<joueurs.length; i++){
            child = document.createElement("button");
            child.setAttribute("id",joueurs[i].getElementsByTagName("ID")[0].textContent);
            child.setAttribute("class","visualisable");
            child.appendChild(document.createTextNode(" "+joueurs[i].getElementsByTagName("pseudo")[0].textContent+" "));
            spec.appendChild(child);
        }
        
        //Ajout de la possibilité d'accéder au mode super vue (identifiant égal à -1)
        child = document.createElement("button");
        child.setAttribute("id","-1");
        child.setAttribute("class","visualisable");
        child.appendChild(document.createTextNode(" SuperVue "));
        spec.appendChild(child);
        
        //On ajoute les eventlisteners sur nos boutons
        ajoutEventListenerVisualisables();
    }
}

/**
 * Vide la zone créée dans la fonction précédente
 * @returns {undefined}
 */
function reinitialiserInterfaceSpec(){
    var spec = document.getElementById("spec");
    while (spec.firstChild) {
        spec.removeChild(spec.firstChild);
    }
}

/**
 * Ajout des listeners qui vont déclencher le changement du vue au clic sur les boutons
 * @returns {undefined}
 */
function ajoutEventListenerVisualisables(){
    var visu = document.getElementsByClassName("visualisable");
    for(var i=0; i<visu.length; i++){
        visu[i].addEventListener("click",changerVueSpectateur);
    }
}

/**
 * On récupère l'id du bouton sur lequel on a cliqué et on ordonne le changement de vue
 * @param {type} elt Bouton qui a déclenché l'évènement
 * @returns {undefined}
 */
function changerVueSpectateur(elt){
    var id = elt.srcElement.getAttribute("id");
    if(id != -1) { ID = id; }                       //Si c'est un joueur de la partie on usurpe son identité pour afficher ce qu'il voit (on ne peut pas déplacer d'avatar en tant que spectateur)
    else{ changerVue(); }                           //Sinon c'est le mode super vue, on change donc la caméra
}