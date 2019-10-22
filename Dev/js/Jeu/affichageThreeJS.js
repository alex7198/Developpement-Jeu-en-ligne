/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*------------------------------------- Création en ThreeJS des différents éléments de la partie -------------------------------------*/

/**
 * Crée la scène ThreeJS puis le terrain
 * @returns {undefined}
 */
function initThreeJS(){
    creerScene();
    getTerrain();
}

/**
 * Création de la scène ThreeJS
 * @returns {undefined}
 */
function creerScene(){
    //Création de la scène
    scene = new THREE.Scene();
    
    //Ajout du brouillard
    scene.fog = new THREE.Fog("black",1,3);
    
    //Création du moteur de rendu
    renderer = new THREE.WebGLRenderer();
    renderer.setSize(window.innerWidth/1.5,window.innerHeight/1.5);
    
    //Création des deux caméras (joueur et super vue)
    camera = new THREE.PerspectiveCamera(90,window.innerWidth/window.innerHeight,0.1,1000);
    cameraSuperVue = new THREE.PerspectiveCamera(90,window.innerWidth/window.innerHeight,0.1,1000);
    placerCamera(cameraSuperVue, 18, 22, 18, -90, 0, 90);
    
    //On réinitialise la scène sur la page et on ajoute celle qu'on vient de créer
    document.getElementById("scene").innerHTML = "";
    document.getElementById("scene").appendChild(renderer.domElement);
        
    //Création des lumières
    var light = new THREE.SpotLight(0x404040);
    light.position.set(0,30,60);
    light.castShadow = true;
    var light2 = new THREE.HemisphereLight( 0xffffbb, 0x080820, 0.5 );
    scene.add(light);
    scene.add(light2);
        
    //Rendu de la scène
    rafraichirScene();
}

/**
 * Création du terrain à partir d'un tableau d'éléments XML retournés par le serveur pour représenter les cases
 * @param {type} cases Tableau d'éléments XML de la forme <case><x></x><y></y><navigable></navigable><occupee></occupee></case>
 * @returns {undefined}
 */
function creerTerrain(cases){
    //Attributs qu'on va récupérer sur le document XML
    var x; var y; var navigable; var magique;
    
    for(var i=0; i<cases.length; i++){
        //On récupère les données du XML
        x = cases[i].getElementsByTagName("x")[0].textContent;
        y = cases[i].getElementsByTagName("y")[0].textContent;
        navigable = cases[i].getElementsByTagName("navigable")[0].textContent;
        magique = cases[i].getElementsByTagName("magique")[0].textContent;
        
        //Construction de la primitive qui va représenter le terrain
        var solObjet = new THREE.BoxGeometry(1,1,1);
        
        //Récupération des textures
        var loader = new THREE.TextureLoader();
        loader.crossOrigin = '';
        var solTexture;
        if(magique === 'M' && navigable === 'O') solTexture = loader.load("img/solmagique.jpg");     //Case magique
        if(magique === 'O' && navigable === 'O') solTexture = loader.load("img/sol.jpg");            //Case standard
        if(navigable === 'X') { creerMur(x,y); solTexture = loader.load("img/solmur.jpg");}          //Case sous un mur
        solTexture.wrapS = solTexture.wrapT = THREE.RepeatWrapping;                                     //Si cube plus grand que texture on la répète
        
        //Création du matériau qui va envelopper la primitive
        var solMateriau = new THREE.MeshBasicMaterial({map: solTexture});
        
        //Création de l'objet sol (combinaison de la primitive et du matériau)
        var sol = new THREE.Mesh(solObjet, solMateriau);
        
        //On place la case au bon endroit
        sol.position.set(parseInt(x,10),-1,parseInt(y,10));
        
        //On ajoute la case à la scène
        scene.add(sol);
    }
    
    //On réeffectue le rendu de la scène après ajout du terrain
    rafraichirScene();
}

/**
 * Crée un mur aux coordonnées passées en paramètres
 * Même principe que pour le sol (voir précédemment)
 * @param {type} x Coordonnée en X du mur
 * @param {type} y Coordonnée en Y du mur
 * @returns {undefined}
 */
function creerMur(x, y){
    //Construction des murs
    var murObjet = new THREE.BoxGeometry(1,5,1);
    var loader = new THREE.TextureLoader();
    loader.crossOrigin = '';
    var murTexture;
    murTexture = loader.load("img/mur.jpg");
    murTexture.wrapS = murTexture.wrapT = THREE.RepeatWrapping;
    var murMateriau = new THREE.MeshBasicMaterial({map: murTexture});
    var mur = new THREE.Mesh(murObjet, murMateriau);
    mur.position.set(parseInt(x,10),0,parseInt(y,10));
    scene.add(mur);
}

/**
 * Fabrique d'objets ThreeJS
 * @param {type} primitive Primitive qui représente la géométrie de l'objet
 * @param {type} couleur Couleur de l'objet
 * @param {type} x Coordonnée en X de l'objet sur le terrain
 * @param {type} y Coordonnée en Y de l'objet sur le terrain
 * @returns {creerObjet.obj|THREE.Mesh} objet créé
 */
function creerObjet(primitive, couleur, x, y){
    var obj = null;
    
    switch(primitive){
        case "cube":    //L'objet est un cube (équipe rouge)
            var geometry = new THREE.BoxGeometry(0.5,0.5,0.5);
            var material = new THREE.MeshPhongMaterial({color:couleur,specular:couleur});
            obj = new THREE.Mesh(geometry,material);
            obj.position.set(x,-0.25,y);
            break;
        case "cylindre":    //L'objet est un cylindre (équipe bleue)
            var geometry = new THREE.CylinderGeometry(0.25,0.25,0.5,20);
            var material = new THREE.MeshPhongMaterial({color:couleur,specular:couleur});
            obj = new THREE.Mesh(geometry,material);
            obj.position.set(x,-0.25,y);
            break;
        case "cone":    //L'objet est un cône (joueur incognito)
            var geometry = new THREE.ConeGeometry(0.25,0.5,20);
            var material = new THREE.MeshPhongMaterial({color:couleur,specular:couleur});
            obj = new THREE.Mesh(geometry,material);
            obj.position.set(x,-0.25,y);
            break;
        case "sphere":  //L'objet est une sphère (bonus)
            var geometry = new THREE.SphereGeometry(0.1,20,20,20,20,20,20);
            var material = new THREE.MeshPhongMaterial({color:couleur,specular:couleur});
            obj = new THREE.Mesh(geometry,material);
            obj.position.set(x,-0.1,y);
            break;
    }
    
    return obj;
}

/**
 * Crée les joueurs à partir de la liste de joueurs au format XML retournée par la serveur
 * @param {type} joueurs Liste d'éléments XML qui représentent les joueurs au format
 * <joueur><id><pseudo><posX><posY><orientation><primitive><couleur><couleurDeBase><role><invisible><superVue></joueur>
 * @returns {undefined}
 */
function creerJoueurs(joueurs){
    //Attributs joueur
    var id, pseudo, posX, posY, orientation, primitive, couleur, couleurDeBase, role, invisible;
    //Compteurs
    var nbBleu = 0, nbRouge = 0, vivant=0;
    
    //On retire les avatars des joueurs du tour précédent
    scene.remove(scene.getObjectByName("joueurs"));
    
    //Tous les joueurs feront partie du groupe "joueurs" (facilite leur suppression comme expliqué ci-dessus)
    var groupeJ = new THREE.Group();
    
    for(var i=0; i<joueurs.length; i++){
        //Récupération des attributs
        id = joueurs[i].getElementsByTagName("ID")[0].textContent;
        pseudo = joueurs[i].getElementsByTagName("pseudo")[0].textContent;
        posX = joueurs[i].getElementsByTagName("posX")[0].textContent;
        posY = joueurs[i].getElementsByTagName("posY")[0].textContent;
        orientation = joueurs[i].getElementsByTagName("orientation")[0].textContent;
        primitive = joueurs[i].getElementsByTagName("primitive")[0].textContent;
        couleur = joueurs[i].getElementsByTagName("couleur")[0].textContent;
        couleurDeBase = joueurs[i].getElementsByTagName("couleurDeBase")[0].textContent;
        role = joueurs[i].getElementsByTagName("role")[0].textContent;
        invisible = joueurs[i].getElementsByTagName("invisible")[0].textContent;
        
        //On compte le nombre de kékés et de blaireaux dans la partie
        if(couleurDeBase === "red") nbRouge++;
        else nbBleu++;
        
        //Création des joueurs
        if(id !== ID){ 
            //Adversaires
            if(invisible === "X") groupeJ.add(creerObjet(primitive, couleur, posX, posY));
        }
        else{
            //Joueur courant
            vivant++;   //Flag permettant d'indiquer que le joueur est toujours vivant puisque son ID est présent dans la liste des joueurs
            
            //On regarde si le joueur possède la super vue ou non
            if(joueurs[i].getElementsByTagName("superVue")[0].textContent === "V") superVue = true;
            else { if(!spectateur) { superVue = false; sousSuperVue = false; } }
            
            //On place le joueur courant dans la scène (quand il n'est pas en super vue il joue la caméra)
            creerJoueurCourant(couleurDeBase, pseudo, posX, posY, orientation, role);
            
            //On affiche l'état de ses bonus
            afficherBonus(joueurs[i]);
            
            //S'il est en train de regarder la carte en super vue on crée un objet indentique à ceux de son équipe mais vert pour indiquer sa position
            if(sousSuperVue) groupeJ.add(creerObjet(primitive, "green", posX, posY));
        }
    }
    
    //On ajoute le groupe à la scène
    groupeJ.name = "joueurs";
    scene.add(groupeJ);
    
    //On modifie le message général pour renseigner sur l'état de la partie
    messageGeneral(nbRouge,nbBleu);
    
    //Si vivant est toujours à 0 alors son ID ne fait plus partie de la liste des joueurs, il est donc mort
    if(vivant === 0) messageMort();
    
    //On remet à jour la scène
    rafraichirScene();
}

/**
 * Place le joueur courant sur la scène et met à jour ses informations dans la barre du dessus
 * @param {type} pseudo Pseudo du joueur courant
 * @param {type} posX Sa position en X
 * @param {type} posY Sa position en Y sur le terrian 2D (Z dans l'espace 3D de ThreeJS)
 * @param {type} orientation L'orientation que doit prendre la caméra
 * @param {type} role Son rôle (kéké ou blaireau)
 * @returns {undefined}
 */
function creerJoueurCourant(couleur,pseudo, posX, posY, orientation, role){
    //Si le joueur a changé de rôle par rapport au tour différent on joue un son pour le lui faire remarquer
    if(roleJoueur !== role) { roleJoueur = role; jouerSon();}                                                                               
    document.getElementById("infos").textContent = "Couleur : "+couleur+" - Pseudo : "+pseudo+" - Role : "+role+" - X : "+posX+" - Y : "+posY;    //On met à jour ses informations
    if(!sousSuperVue) placerCamera(camera, parseInt(posX,10), 0, parseInt(posY,10), 0, parseInt(orientation,10), 0);                        //On place la caméra au bon endroit (à part si le joueur est sous super vue, auquel cas il utilise l'autre caméra)
}

/**
 * Crée les bonus et les place sur le terrain
 * @param {type} bonus Liste d'éléments XML qui représentent les joueurs au format
 * <bonus><ID><posX><posY><primitive><couleur></bonus>
 * @returns {undefined}
 */
function creerBonus(bonus){
    //Attributs d'un bonus
    var pseudo, posX, posY, primitive, couleur;
    
    //On retire les avatars des bonus du tour précédent
    scene.remove(scene.getObjectByName("bonus"));
    
    //Comme les joueurs les bonus seront regroupés dans un groupe, toujours pour les mêmes raisons
    var groupeB = new THREE.Group();
    
    for(var i=0; i<bonus.length; i++){
        //Récupération des attributs
        pseudo = bonus[i].getElementsByTagName("ID")[0].textContent;
        posX = bonus[i].getElementsByTagName("posX")[0].textContent;
        posY = bonus[i].getElementsByTagName("posY")[0].textContent;
        primitive = bonus[i].getElementsByTagName("primitive")[0].textContent;
        couleur = bonus[i].getElementsByTagName("couleur")[0].textContent;
        
        //Création des bonus
        groupeB.add(creerObjet(primitive, couleur, posX, posY));
    }
    
    //Ajout des bonus à la scène
    groupeB.name = "bonus";
    scene.add(groupeB);

    rafraichirScene();
}

/*------------------------------------- Gestion de la caméra et de l'affichage -------------------------------------*/

/**
 * Place la caméra donnée à la position donnée avec l'orientation donnée
 * @param {type} cam Caméra à bouger
 * @param {type} posX Nouvelle position en X
 * @param {type} posY Nouvelle position en Y
 * @param {type} posZ Nouvelle position en Z
 * @param {type} oX Nouvelle orientation en X (angle en degrés)
 * @param {type} oY Nouvelle orientation en Y (angle en degrés)
 * @param {type} oZ Nouvelle orientation en Z (angle en degrés)
 * @returns {undefined}
 */
function placerCamera(cam, posX, posY, posZ, oX, oY, oZ){
    cam.position.x = posX;
    cam.position.y = posY;
    cam.position.z = posZ;
    cam.rotation.x = THREE.Math.degToRad(oX);
    cam.rotation.y = THREE.Math.degToRad(oY);
    cam.rotation.z = THREE.Math.degToRad(oZ);
    rafraichirScene();
}

/**
 * Permet de passer de la caméra classique à la super vue et vice versa
 * @returns {undefined}
 */
function changerVue(){
    if(superVue || spectateur){                             //On ne peut passer en super vue que si on possède le bonus ou si on est spectateur
        sousSuperVue = !sousSuperVue;                       //On indique qu'on entre ou qu'on sort du mode super vue
        if(sousSuperVue) scene.fog = new THREE.Fog(null);   //Si on passe en super vue on désactive le brouillard pour pouvoir bien voir
        else scene.fog = new THREE.Fog("black",1,3);        //Quand on sort du mode on réactive le brouillard
        rafraichirScene();                                  //On ré-effectue le rendu de la scène
    }
}

/**
 * Effectue le rendu de la scène avec la bonne caméra en fonction du mode du joueur
 * @returns {undefined}
 */
function rafraichirScene(){
    if(sousSuperVue) renderer.render(scene,cameraSuperVue);
    else renderer.render(scene,camera);
}

/*------------------------------------- Gestion des sons -------------------------------------*/

/**
 * Joue un son pour alerter le joueur
 * @returns {undefined}
 */
function jouerSon(){
    //On crée un contexte audio pour pouvoir jouer le son
    var context = new AudioContext();
    context.resume();
    
    //La caméra ThreeJS va jouer le son
    var listener = new THREE.AudioListener();
    camera.add(listener);
    
    //On crée puis charge puis joue le son
    var sound = new THREE.Audio(listener);
    var audioLoader = new THREE.AudioLoader();
    audioLoader.load("img/sound.mp3",function(buffer){
        sound.setBuffer(buffer);
        sound.setLoop(false);
        sound.setVolume(0.5);
        sound.play();
    });
}