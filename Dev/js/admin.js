window.onload = init;

function init(){
    document.getElementById("btn_valider_ban").addEventListener("click",bannir);
    getJoueursBD();
}

/**
 * Demande la liste des personnes inscrites sur le serveur
 */
function getJoueursBD()
{
    var objetXHR = creationXHR();
    objetXHR.onreadystatechange = function(){
        if(objetXHR.readyState===4 && objetXHR.status ===200){
            remplirTableauJoueurs(JSON.parse(objetXHR.responseText));
        }
    };
    objetXHR.open("get","../php/Controller/JSController?fct=GetAdherents",true);
    objetXHR.send(null);
}

/**
 * Remplit le tableau présent sur la page avec les informations récupérées
 * @param {type} table Tableau contenant les adhérents du site
 * @returns {undefined}
 */
function remplirTableauJoueurs(table){
    var tab = document.getElementById("tab_joueur");
    for(i=0;i<table.length;i++) { tab.appendChild(creerTR(table[i])); }
}

/**
 * Crée une ligne dans le tableau des adhérents
 * @param {type} tuple Ligne du tableau récupéré du serveur
 * @returns {creerTR.tr|Element} Ligne créée
 */
function creerTR(tuple){
    var tr = document.createElement("tr");
    var pseudo = creerTD(tuple['pseudo']);
    var mail = creerTD(tuple['mail']);
    var cb = creerCheckBox(tuple['pseudo']);
    tr.appendChild(pseudo);
    tr.appendChild(mail);
    tr.appendChild(cb);
    return tr;
}

/**
 * Crée une colonne dans le tableau des adhérents
 * @param {type} texte Texte à écrire dans la colonne
 * @returns {Element|creerTD.td} Colonne créée
 */
function creerTD(texte){
    var td = document.createElement("td");
    td.appendChild(document.createTextNode(texte));
    return td;
}

/**
 * Crée la checkbox pour pouvoir sélectionner une ligne
 * @param {type} joueur Pseudo du joueur à qui est associée la ligne
 * @returns {Element|creerCheckBox.cb} Chekbox créée
 */
function creerCheckBox(joueur)
{
    var cb = document.createElement("input");
    cb.setAttribute("id",joueur);                 //On donne pour ID à la checkbox le nom du joueur pour savoir facilement à qui est associée chaque ligne sélectionnée
    cb.setAttribute("type","checkbox");
    return cb;
}

/**
 * Fonction appelée quand l'utilisateur clique sur le bouton pour valider son ban
 * @returns {undefined}
 */
function bannir(){
    var listebans = [], message, adherents, form;
    
    //On récupère les valeurs dont on a besoin
    message = document.getElementById("textarea_ban").value;
    adherents = document.getElementById("tab_joueur").children;
    for(var i=0; i<adherents.length; i++){
        if(adherents[i].children[2].checked) listebans.push(adherents[i].children[1].innerText);
    }
    
    //On remplit le formulaire pour passer les paramètres dans la requête
    form = document.getElementById("form_bannir");
    var input_j = document.createElement("input");
    input_j.setAttribute("type","hidden");
    input_j.setAttribute("name","aBannir");
    input_j.setAttribute("value",listebans.join("|"));
    
    var input_m = document.createElement("input");
    input_m.setAttribute("type","hidden");
    input_m.setAttribute("name","msg");
    input_m.setAttribute("value",message);
    
    form.appendChild(input_j);
    form.appendChild(input_m);
    
    //On envoie la requête au serveur
    form.submit();
}

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