<form id="form_insc"  method="post" action="index.php?action=inscription">
    <p class="p_connection">
        <label class="label_connection" id="label_pseudo" for="Pseudo">Pseudo : </label>
        <input id="input_pseudo" class="input_connection" type="text" name="pseudo">
    </p>
    
    <p class="p_connection">
        <label class="label_connection" for="email">Adresse email : </label>
        <input class="input_connection" type="text" name="email" name="pseudo">
    </p>
    
    <p class="p_connection">
        <label class="label_connection" for="email2">Confirmation de l\'adresse email : </label>
        <input class="input_connection" type="text" name="email_conf" name="pseudo">
    </p>
    
    <p class="p_connection" >
        <label class="label_connection" for="mdp" > Mot de passe : </label>
        <input class="input_connection" id="input_password" type = "password" name = "mdp">
    </p>
    
    <p class="p_connection" >
        <label class="label_connection" for="mdp" > Confirmation du mot de passe : </label>
        <input class="input_connection" id="input_password_conf" type = "password" name = "mdp_conf">
        <progress max="100" value="0" id="strength" style="width : 230px" ></progress>
    </p>
    
   <input id="input_envoyer" class="btn_co" type = "submit" value = "Envoyer" >
</form>
