/**
 * Envoie le joueur dans la salle d'attente
 * @param {type} e Element sur lequel l'utilisateur a cliqué
 * @param {type} flag Vaut 0 si la partie est juste en attente, 1 si c'est une invitation
 */
function rejoindre_partie(e,flag){
    //On renseigne l'id de la partie qu'on va rejoindre
    document.getElementById("rejoindrePartie_id").setAttribute("value",e.id);
    
    //On récupère les infos dans le tableau
    var infos = document.getElementsByClassName(e.id);
    var mdp = "", type;
    if(flag == 0){
        for(var i=0; i<infos.length; i++){
            if(infos[i].id === "Type") type = infos[i].innerText;
            if(infos[i].localName === "input" && type === "Privee") mdp = infos[i].value;
        }
    } else type = "Publique";
    
    //On renseigne les champs et on envoie le formulaire
    document.getElementById("rejoindrePartie_type").setAttribute("value",type);
    document.getElementById("rejoindrePartie_mdp").setAttribute("value",mdp);
    document.getElementById("form_rejoindre").submit();
}

/**
 * Envoie le joueur dans la partie
 * @param {type} e Element sur lequel l'utilisateur a cliqué
 */
function rejoindre_jeu(e){
    document.getElementById("rejoindreJeu_id").setAttribute("value",e.id);
    var infos = document.getElementsByClassName(e.id);
    var mdp = "", type;
    for(var i=0; i<infos.length; i++){
        if(infos[i].id === "Type") type = infos[i].innerText;
        if(infos[i].localName === "input" && type === "Privee") mdp = infos[i].value;
    }
    document.getElementById("rejoindreJeu_type").setAttribute("value",type);
    document.getElementById("rejoindreJeu_mdp").setAttribute("value",mdp);
    document.getElementById("form_jeu").submit();
}