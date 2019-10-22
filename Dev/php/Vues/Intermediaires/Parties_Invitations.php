<?php
    $i=0;
    if(isset($tabInvitations)) {
        foreach ($tabInvitations as $row) {
            echo '<tr> 
                <td class='.$tabInvitations[$i]["IDPartie"].'>' . $tabInvitations[$i]["Nom"] . '</td>
                <td class='.$tabInvitations[$i]["IDPartie"].'>'. $m->recupererNbJoueursPartie($tabInvitations[$i]["IDPartie"]) . "/". $tabInvitations[$i]["MaxJoueur"] . '</td>';
            echo '<td id="Type" class='.$tabInvitations[$i]["IDPartie"].'>'.$tabInvitations[$i]["Type"].'</td>';
            echo '<td class='.$tabInvitations[$i]["IDPartie"].'>' . $tabInvitations[$i]["Expediteur"] . '</td>';
            echo '<td>' .'<input type="button" value="Rejoindre" onclick="rejoindre_partie(this,1)" class="btn_rejoindre" id="'. $tabInvitations[$i]["IDPartie"] . '"></td>';
            echo '</tr>';
            $i++;
        }
    }
?>