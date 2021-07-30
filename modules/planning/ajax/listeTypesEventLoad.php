<?php

include_once '../../../config.php';
require_once ABS_CLASSES_PATH . $dbFile;
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_PLANNING_PATH . CLASSES_PATH . 'Event.php';
require_once ABS_GENERAL_PATH . 'formFunctions.php';


/* 
 * Affichage de tous les types d'activité
 * sélectionnées.
 */

$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
}else{
    $activite = new Event($dbaccess);
    $tabActivites = array();
    $tabActivites = $activite->getAll();
    $retour = '';
    $retour .= '<div class="legende_titre" onclick="cacherComposantsInfo();"><h1>Types de congés</h1></div>';
    
    $retour .= '<table id="tab_activites">';
    $retour .= '<th>libellé</th><th>couleur</th><th>abbréviation</th>';
    $classeParite = 'pair';
    if( count($tabActivites) > 0 ) {
        $i = 1;
        // Liste de tous les types d'événement
        foreach($tabActivites as $key => $value){
          $color = $value['couleur'];
          $classeParite = ($i%2 == 0 ? 'pair':'impair');
          $retour .=   '<tr id='.$key.' class="'.$classeParite.'">';
          $retour .= '<td id="' . $key . '_libelle" class="legende_activite">'.  $value['libelle'].':</td>';
          $retour .= '<td id><input type="text" id="' . $key . '_couleur" class ="choix_couleur" value="#'.$color.'" readonly /></td>';
          $retour .= '<td><input input type="text" id="' . $key . '_affichage" value="'.$value['affichage'].'" maxlength="3" /></td>';
          $retour .= '<td><input type="button" id="' . $key . '_validation_ligne" value="valider" onclick="modifierTypeEvent('. $key .');"/></td>';
          $retour .="</tr>";
          $i++;
        } 

    }
    // Ajout d'un nouveau type d'événement
    $retour .=   '<tr id="newLine" class="'.$classeParite.'">';
    $retour .= '<td><input type="text" id="new_libelle" value="" /> </td>';
    $retour .= '<td><input type="text" id="new_color" class ="choix_couleur" readonly /></td>';
    $retour .= '<td><input type="text" id="new_abbrev" value="" maxlength="3" /></td>';
    $retour .= '<td><input id="new_validation" type="button" value="ajouter" onclick="insererTypeEvent();"/></td>';
    $retour .="</tr>";
    $retour .= '</table>';
    //$retour = utf8_encode($retour);
    
}

?><script>$(".choix_couleur").colorpicker({
    strings: "Couleurs variées,Couleurs de base,+ de couleurs,- de couleurs,Palette,Historique,Pas encore d'historique."
});</script><?php
$dbaccess->close($handler);
//echo utf8_encode($retour);
echo $retour;


?>
