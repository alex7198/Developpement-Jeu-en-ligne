/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Variables communication
var objetXHR;                   //Moteur AJAX
var interval_1, interval_2;     //Boucles qui effectuent des actions à intervalles de temps réguliers (interval_1 = récupération des joueurs, interval_2 = récupération du timer et de l'état de la partie)
var nbJListePrec = -1;          //Nombre de joueurs dans la précédente liste retournée par le serveur

//Variables ThreeJS
var scene;                      //Scene ThreeJS qui contient le jeu
var camera, cameraSuperVue;     //Caméras du jeu
var renderer;                   //Moteur de rendu
var ID;                   //Identifiant du joueur dans le jeu
var roleJoueur;                 //Role du joueur
var superVue = false;           //Indique si le joueur peut utiliser la super vue
var sousSuperVue = false;       //Indique si le joueur est en train d'utiliser la super vue
var spectateur = false;         //Indique si le joueur est un spectateur



