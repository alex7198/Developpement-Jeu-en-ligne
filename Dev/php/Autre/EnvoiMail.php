<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 06/04/2019
 * Time: 12:08
 */

/**
 * Classe permettant d'envoyer un mail à l'inscription
 */
class EnvoiMail
{

    function __construct($destinataire,$cle)
    {

        $sujet = "Activer votre compte";
        $entete = 'From: noreply@projet-web-l3.fr' . "\r\n" .
            'Reply-To: noreply@projet-web-l3.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        //Sur le serveur
        $message = 'Bienvenue sur projet-web-l3.com,
            Pour activer votre compte, veuillez cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet.
                        http://www.projet-web-l3.fr/index.php?action=validation_mail&mail=' . urlencode($destinataire) . '&cle=' . urlencode($cle) . '
                            ---------------
                            Ceci est un mail automatique, Merci de ne pas y répondre.';
        //En local
        /*$message = 'Bienvenue sur projet-web-l3.com,
            Pour activer votre compte, veuillez cliquer sur le lien ci dessous ou copier/coller dans votre navigateur internet.
                        http://localhost/projet_web/Dev/index.php?action=validation_mail&mail=' . urlencode($destinataire) . '&cle=' . urlencode($cle) . '
                            ---------------
                            Ceci est un mail automatique, Merci de ne pas y répondre.';*/
        
        return mail($destinataire, $sujet, $message, $entete);
    }
}