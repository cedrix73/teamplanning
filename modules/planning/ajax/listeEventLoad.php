<?php

include_once '../../../config.php';
require_once ABS_CLASSES_PATH . $dbFile;
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_PLANNING_PATH . CLASSES_PATH . 'Event.php';
//require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Affichage de tous les types d'activité
 * sélectionnées.
 */

$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ' . $dbaccess->getError();
}else{
    $activite = new Event($dbaccess);
    $tabActivites = array();
    $tabActivites = $activite->getAll();
    $retour = '';
    if(count($tabActivites) > 0 ){
        $i = 1;
        $retour .= '<table id="tab_activites">';
        $retour .= '<th>libellé</th><th>couleur</th>';
        foreach($tabActivites as $key => $value){
          $color = $value['couleur'];
          $classeParite = ($i%2 == 0 ? 'pair':'impair');
          $retour .=   '<tr id='.$key.' class="'.$classeParite.'">';
          $retour .= '<td id="' . $key . '_libelle" >'.$value['libelle'].':</td>';
          $retour .= '<td><input id="' . $key . '_div_color" class ="choix_couleur" value="#'.$color.'" style="display: none;"/></td>';
          $retour .= '<td><input id="' . $key . '_validation" type="button" value="valider" onclick="modifierTypeEvent('. $key .');"/></td>';
          $retour .="</tr>";
          $i++;
        }
        $retour .= '</table>';
    }
}
?><script>$(".choix_couleur").colorpicker({
    strings: "Couleurs variées,Couleurs de base,+ de couleurs,- de couleurs,Palette,Historique,Pas encore d'historique."
});</script><?php
echo $retour;


?>
