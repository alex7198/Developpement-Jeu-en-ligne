window.onreadystate=init();

function init(){
    document.getElementById("quitter").addEventListener("click",click_quitter);
    document.getElementById("radioButtonPrivateGame").addEventListener("change",click_privee);
    document.getElementById("radioButtonPublicGame").addEventListener("change",click_publique);
}

/**
 * Clic sur le bouton pour revenir au menu principal
 */
function click_quitter(){
    document.location.href="index.php?action=retour_principal";
}

/**
 * La partie sera privée
 */
function click_privee(){
    document.getElementById("radioButtonPublicGame").checked = false;
    document.getElementById("mdp").style.display = "initial";
}

/**
 * La partie sera publique
 */
function click_publique(){
    document.getElementById("radioButtonPrivateGame").checked = false;
    document.getElementById("mdp").style.display = "none";
}