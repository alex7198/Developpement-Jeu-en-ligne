<?php
    $i=0;
    if(isset($tabPartieEnAttente)) {
        foreach ($tabPartieEnAttente as $row) {
            echo '<tr> 
                <td class='.$tabPartieEnAttente[$i]["IDPartie"].'>' . $tabPartieEnAttente[$i]["Nom"] . '</td>
                <td class='.$tabPartieEnAttente[$i]["IDPartie"].'>' . $m->recupererNbJoueursPartie($tabPartieEnAttente[$i]["IDPartie"]) . "/". $tabPartieEnAttente[$i]["MaxJoueur"] . '</td>';
            if($tabPartieEnAttente[$i]["Type"]=="Privee")
            {
                 echo '<td id="Type" class='.$tabPartieEnAttente[$i]["IDPartie"].'>'
                         .$tabPartieEnAttente[$i]["Type"].
                         '<input type="password" class='.$tabPartieEnAttente[$i]["IDPartie"].' placeholder="Entrez la clé pour rejoindre ...">
                      </td>';
            }
            else
            {
                echo '<td id="Type" class='.$tabPartieEnAttente[$i]["IDPartie"].'>'
                    .$tabPartieEnAttente[$i]["Type"].
                      '</td>';
            }
            echo '<td>' .'<input type="button" value="Rejoindre" onclick="rejoindre_partie(this,0)" class="btn_rejoindre" id="'. $tabPartieEnAttente[$i]["IDPartie"] . '"></td>';
            echo '</tr>';
            $i++;
        }
    }
?>
