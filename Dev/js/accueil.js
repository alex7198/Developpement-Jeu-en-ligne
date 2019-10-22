window.onload=init;

function init()
{
    if(document.getElementById("input_password")!==null) document.getElementById("input_password").addEventListener("keyup",checkPassword);
    if(document.getElementById("input_password_conf")!==null) document.getElementById("input_password_conf").addEventListener("keyup",checkPassword);
}

/**
 * Calcule la force du mot de passe
 */
function checkPassword(){
    var password= document.getElementById("input_password").value;
    var password_conf= document.getElementById("input_password_conf").value;
    var strength = 0;
    if(password.match(/[a-z]{1,18}/)){strength += 1 ;}
    if(password.match(/[0-9]{1,18}/)){strength += 1 ;}
    if(password.match(/[A-Z]{1,18}/)){strength += 1 ;}
    if(password.match(/^(?=.{8,20}$)(?=(?:.*?[A-Z]){1,18})(?=.*?[a-z])(?=(?:.*?[0-9]){1,18}).*$/)){ strength += 1 ;}
    if(password==password_conf){ strength += 1 ;}
    if(password.length==0){ strength=0;}
    changeBar(strength);
}

/**
 * Change l'état de la barre pour indiquer la force du mot de passe entré
 * @param {type} strength
 */
function changeBar(strength)
{
    var strengthBar = document.getElementById("strength");
    switch(strength){
        case 0 :
            strengthBar.value=0;
            break;
        case 1:
            strengthBar.value=20;
            break;
        case 2:
            strengthBar.value=40;
            break;
        case 3:
            strengthBar.value=60;
            break;
        case 4:
            strengthBar.value=80;
            break;
        case 5:
            strengthBar.value=100;
            break;
    }
}

