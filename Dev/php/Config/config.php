<?php

$rep=__DIR__.'/../';

//Tableau de vues pour simplifier l'accès à chaque page
$vues['accueil']=$rep.'Vues/accueil.php';
$vues['admin']=$rep.'Vues/admin.php';
$vues['attente']=$rep.'Vues/attente.php';
$vues['creation']=$rep.'Vues/Creation.php';
$vues['jeu']=$rep.'Vues/jeu.php';
$vues['principal']=$rep.'Vues/principal.php';
$vues['rejoindre']=$rep.'Vues/rejoindre.php';
$vues['header']=$rep.'Vues/header.html';
$vues['footer']=$rep.'Vues/footer.html';
$vues['validation']=$rep.'Vues/validation.php';
$vues['notification']=$rep.'Vues/notification.php';
$vues['erreur']=$rep.'Vues/erreur.php';

//Tableau associatif de classes : clé : nom de la classe, valeur : réperotire dans lesquelles elles sont stockées.
//Pareil que pour Metier
foreach (scandir($rep.'Modele/') as $value)
{
    if($value!='.'&& $value!='..')
    { $classes[$value]='Modele'; }
}
//On parcourt les fichiers et dossiers du répertoire Metier
foreach (scandir($rep.'Metier/') as $value)
{
    //On ne prend pas en compte les répertoires
    if($value!='.'&& $value!='..')
    //On ajoute au tableau le nom de la classe en clé et le répertoire dans laquelle elle est en valeur
    { $classes[$value]='Metier'; }
}
//Pareil que pour Metier
foreach (scandir($rep.'Controller/') as $value)
{
    if($value!="." && $value!="..")
    { $classes[$value]='Controller'; }
}
foreach (scandir($rep.'DAL/') as $value)
{
    if($value!="." && $value!="..")
    { $classes[$value]='DAL'; }
}
foreach (scandir($rep.'Autre/') as $value)
{
    if($value!="." && $value!="..")
    { $classes[$value]='Autre'; }
}

//Config Alexis
/*define('DSN','mysql:host=localhost;dbname=projetweb');
define("USER",'root');
define("PASSWORD",'');*/

//Config Alexandre
/*define('DSN','mysql:host=localhost;dbname=dbs33334');
define("USER",'root');
define("PASSWORD",'root');*/


//Config lorsque le site sera sur la serveur
define('DSN','mysql:host=db5000038371.hosting-data.io;dbname=dbs33334');
define("USER",'dbu78701');
define("PASSWORD",'ProjetWeb2019_');
