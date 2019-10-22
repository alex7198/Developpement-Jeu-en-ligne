<?php
    $i=0;
    if(isset($tabPartieEnCours)) {
        foreach ($tabPartieEnCours as $row) {
            echo '<tr> 
                <td class='.$tabPartieEnCours[$i]["IDPartie"].'>' . $tabPartieEnCours[$i]["Nom"] . '</td>
                <td class='.$tabPartieEnCours[$i]["IDPartie"].'>'. $m->recupererNbJoueursPartie($tabPartieEnCours[$i]["IDPartie"]) . "/". $tabPartieEnCours[$i]["MaxJoueur"] . '</td>';
            if($tabPartieEnCours[$i]["Type"]=="Privee")
            {
                 echo '<td id="Type" class='.$tabPartieEnCours[$i]["IDPartie"].'>'
                         .$tabPartieEnCours[$i]["Type"].
                         '<input type="password" class='.$tabPartieEnCours[$i]["IDPartie"].' placeholder="Entrez la clé pour rejoindre ...">
                      </td>';
            }
            else
            {
                echo '<td id="Type" class='.$tabPartieEnCours[$i]["IDPartie"].'>'
                    .$tabPartieEnCours[$i]["Type"].
                      '</td>';
            }
            echo '<td>' .'<input type="button" value="Rejoindre" onclick="rejoindre_jeu(this)" class="btn_rejoindre" id="'. $tabPartieEnCours[$i]["IDPartie"] . '"></td>';
            echo '</tr>';
            $i++;
        }
    }
?>