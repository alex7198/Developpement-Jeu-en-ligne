window.onload = init;

function init(){
    document.getElementById("btn_inviter").addEventListener("click",inviter);
    getJoueurs();
    boucleJoueurs();
    EstCreateur();
}

/*------------------------------------- Boucles périodiques -------------------------------------*/

/**
 * Récupère sur le serveur la liste des joueurs pour la partie en attente toutes les 2s
 */
function boucleJoueurs(){
    setInterval(fonctionsPeriodiques,2000);
}

function fonctionsPeriodiques(){
    getJoueurs();
    RecupererStatut();
}

/*------------------------------------- Communications avec le serveur -------------------------------------*/

/**
 * Récupère la liste des joueurs en attente pour la partie en cours
 */
function getJoueurs()
{
    var objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            var joueurs = objetXHR.responseText.split('|');
            
            //Affichage sur l'interface
            remplirTableauJoueurs(joueurs);
            remplirNbJoueurs(joueurs.length);
            JoueursManquants(joueurs.length);
            if(joueurs.length >=3) document.getElementById("btn_lancer").disabled = false;
            else document.getElementById("btn_lancer").disabled = true;
        }
    };
    objetXHR.open("get","index.php?action=liste_joueurs&id="+document.getElementById("IDPartie").value,true);
    objetXHR.send(null);
}

/**
 * Regarde sur le serveur si le client est celui qui a créé la partie
 */
function EstCreateur()
{
    var objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            //Si oui on affiche le bouton pour lancer la partie (si non il ne sera pas affiché)
            if(objetXHR.responseText === "1") { document.getElementById("form_lancer").style.display = "initial"; }
        }
    };
    objetXHR.open("get","index.php?action=est_createur&IDPartie="+document.getElementById("IDPartie").value+"&IDJoueur="+IDJoueur(),true);
    objetXHR.send(null);
}

/**
 * Regarde sur le serveur si la partie pour laquelle on attend est démaréée ou non
 */
function RecupererStatut()
{
    var objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            //Si oui on part vers la partie
            if(objetXHR.responseText !== "EnAttente") document.location.href="index.php?action=rejoindre_jeu&IDPartie="+document.getElementById("IDPartie").value;
        }
    };
    objetXHR.open("get","index.php?action=recuperer_statut&IDPartie="+document.getElementById("IDPartie").value,true);
    objetXHR.send(null);
}

/**
 * Envoie une invitation à un autre adhérent
 */
function inviter(){
    var objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            document.getElementById("msg_retour_invitation").innerText = objetXHR.responseText;
            setTimeout(function(){document.getElementById("msg_retour_invitation").innerText = "";},1000);  //Au bout d'une seconde on efface le message de retour
        }
    };
    objetXHR.open("get","../php/Controller/JSController?fct=inviter_joueur&IDPartie="+document.getElementById("IDPartie").value+"&Expediteur="+IDJoueur()+"&Destinataire="+document.getElementById("PseudoInvite").value,true);
    objetXHR.send(null);
}

/*------------------------------------- Remplissage de l'interface -------------------------------------*/

/**
 * Affiche une phrase indiquant le nombre de joueurs manquants pour pouvoir lancer la partie
 * @param {type} nbJoueurs Nombre de joueurs en attente pour la partie
 */
function JoueursManquants(nbJoueurs){
    if(nbJoueurs >= 3) document.getElementById("joueurs_manquant").innerText = "Il ne manque plus aucun joueur avant de pouvoir lancer la partie";
    else if(nbJoueurs === 2) document.getElementById("joueurs_manquant").innerText = "Il ne manque plus qu'un joueur avant de pouvoir lancer la partie";
    else document.getElementById("joueurs_manquant").innerText = "Il manque "+(3-nbJoueurs)+" joueurs avant de pouvoir lancer la partie";
}

/**
 * Affiche dans le compteur la nombre de joueurs en attente pour la partie
 * @param {type} nbJoueurs Nombre de joueurs
 */
function remplirNbJoueurs(nbJoueurs)
{
    var label=document.getElementById("label_nbJoueurs");
    var max = label.textContent.split("/")[1].split(" ")[0];
    label.textContent = nbJoueurs + "/" + max + " joueurs";
}

/**
 * Remplit le tableau présent sur la page avec la liste des joueurs en attente
 * @param {type} table Tableau contenant les joueurs en attente
 */
function remplirTableauJoueurs(table){
    viderTable();
    var tab = document.getElementById("tab_joueurs_attente");
    for(var i=0;i<table.length;i++)
    {
      tab.appendChild(creerTR(table[i]));
    }
}

/**
 * Crée une ligne dans la file d'attente
 * @param {type} tuple Ligne du tableau récupéré du serveur
 * @returns {creerTR.tr|Element} Ligne créée
 */
function creerTR(tuple){
    var tr = document.createElement("tr");
    var pseudo = creerTD(tuple);
    tr.appendChild(pseudo);
    return tr;
}

/**
 * Crée une colonne dans la file d'attente
 * @param {type} texte Texte à écrire dans la colonne
 * @returns {Element|creerTD.td} Colonne créée
 */
function creerTD(texte){
    var td = document.createElement("td");
    td.appendChild(document.createTextNode(texte));
    return td;
}

/**
 * Enlève toutes les lignes et colonnes du tableau
 * @returns {undefined}
 */
function viderTable(){
    var table = document.getElementById("tab_joueurs_attente");
    while (table.firstChild) {
        table.removeChild(table.firstChild);
    }
}

/*------------------------------------- Autres -------------------------------------*/

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

/**
 * Récupère l'id du joueur enregistré dans le cookie
 * @returns {undefined}
 */
function IDJoueur(){
    var cookie, id="";
    var cookies = document.cookie.split(';');
    for(var i=0; i<cookies.length; i++){
        cookie = cookies[i].split("=");
        if(cookie[0] === "IDJoueur") id = cookie[1];
    }
    return id;
}



