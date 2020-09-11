<?php

include_once realpath(dirname(__FILE__)).'/../config.php';

function checkcheck($post_group, $val_element, $type = 'checkbox'){
    if(!is_array($post_group)){
            if(isset($_POST[$post_group]) && $_POST[$post_group] == $val_element){
                    switch($type){
                            case 'radio':
                                    echo ' checked = "checked"';
                            break;
                            case 'selected':
                                    echo ' selected';
                            break;
                            case 'checkbox':
                                    echo ' checked = true';
                            break;
                    }
            }
    }else{
        foreach($_POST[$post_group] as $key => $value){
                if($value == $val_element){
                        echo ' checked = "checked"';
                }
        }
    }	
}
// Dates
function checkStringDateTime($date, $format = 'd/m/Y H:i:s'){
	//$format = 'd'.$separateur.'m'.$separateur.'Y H:i:s';
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function dateToDb_format($date){
	// str replace car strtotime ne fonctionne pas avec '/'
	$date = date(DB_DATE_FORMAT, strtotime(str_replace('/', '-', $date)));
	return $date;
}

function dbToDate_format($date){
	// str replace car strtotime ne fonctionne pas avec '/'
	$date = date(DATE_FORMAT, strtotime($date));
	return $date;
}

function dateTimeToDb_format($date){
	// str replace car strtotime ne fonctionne pas avec '/'
	$date = date(DB_DATETIME_FORMAT, strtotime(str_replace('/', '-', $date)));
	return $date;
}



/**
 * tabLoad
 * Crée un tableau à partir d'une table avec une structure telle que:
 * <nomTable>: nomTable_id, nom du champ libellé
 * @param type $nom_champ_sql
 * @param type $nom_table
 * @param type $db
 * @return type
 */
function tabLoad($nomChampSql, $nomTableBd, $db, $filtre = null){
    $nom_table = $nomTableBd;
    $tab = array();
    $sql = 'SELECT DISTINCT (id), '.$nomChampSql . 
            ' FROM '.$nomTableBd;
    if($filtre != null){
       $sql .= ' '.$filtre; 
    }
    $reponse = $db->execQuery($sql);
    $results=$db->fetchRow($reponse);
    foreach ($results as $value) {
        $id = $value[0];
        $tab[$id] = $value[1];
    }
    return $tab;
}

/**
 * @description Suite logique d'un tabLoad si on veut charger les résultats dans 
 * une combo avec une valeur sélectionnée par défaut 
 */

function getOptionsFromTab($tab, $selected = null) 
{
    $options = false;
    if (is_array($tab) && count($tab) > 0) {
        $trouve = false;
        foreach ($tab as $key => $value) {
            $options .= '<option id = ' . $key 
                        . ' value="'. $key.'"';
            if ($selected != null || !$trouve) {
                $pref = ($key == $selected) ? ' selected = selected ' : '';
                $options .= ' ' . $pref;
            }
            $options .= '>' . $value . '</option>';
        }
    }
    return $options;
}


/**
 * @name        selectLoad() 
 * @description Charge dans une combobox les valeurs des colonnes
 * $nomChampSql de la table $nomTableBd par un tableau clé (id) - 
 * valeur ($nomChampSql ) à partir d'une requete sql en un One Shot
 * 
 * @param string $nomChampSql nom du champ à afficher pour l'option
 * @param string $nomTableBd  nom de la table contenant nomChampSql
 * @param type $db          instance de la classe-repository DbAccess
 * @param string $filtre    valeur sélectionnée par défaut si corrélation
 * @return string
 */
function selectLoad($nomChampSql, $nomTableBd, $db, $filtre = null)
{
    $nom_table = $nomTableBd;
    $tab = array();
    $sql = 'SELECT DISTINCT (id), '.$nomChampSql . 
            ' FROM '.$nomTableBd;
    $reponse = $db->execQuery($sql);
    $results = $db->fetchArray($reponse);
    $options = '';
    if (is_array($results) && count($results) > 0) {
        $trouve = false;
        foreach ($results as $value) {
            $options .= '<option id = ' . $value['id'] 
                      . ' value="'. $value['id'].'">' . $value['libelle'];
            if ($filtre != null || !$trouve) {
                $pref = ($value['id'] == $filtre) ? ' selected = selected ' : '';
                $options .= ' ' . $pref;
            }
            $options .= '</option>';
        }
    }
    return $options;
}




/**
 * listeLoad
 * 
 * @param type $nomChampSql
 * @param type $nomTable
 * @param type $db
 * @return array
 */
function listeLoad($nomChampSql, $nomTable, $db, $filtre = null){
    $liste = array();
    $sql = 'SELECT DISTINCT(' . $nomChampSql . ') FROM '.$nomTable;
    if($filtre != null){
       $sql .= ' '.$filtre; 
    }
    $reponse = $db->execQuery($sql);
    array_push($liste, "Tous *");
    $results = $db->fetchRow($reponse);
    if (is_array($results) && count($results) > 0) {
        foreach ($results as $ligne) {
            array_push($liste, $ligne[0]);
        }
    }
    
    return $liste;
}

function getIdFomNom($searched, $tabValues){
    $found_id = '';
    foreach($tabValues as $id => $nom){
            if($nom == $searched){
                    $found_id = $id;
            }
    }
    return $found_id;
}

function fixEncodage($chaine){
    $retour = $chaine;
    if(mb_check_encoding($chaine)){
        if (preg_match('!!u', $chaine))
        {
           $retour = utf8_decode($chaine);
        }
        else 
        {
           $retour =  utf8_encode($chaine);
        }
    }
    return $retour;
}

/**
 * colorieDemiJournee
 * @param type $codeCouleur
 * @param integer $type (2=matin, 3=am) 
 * 
 * 
 * background: #901A1C;
    background-image: -moz-linear-gradient(right top,#901A1C 0%,#FF9980 100%);
    background-image: -webkit-gradient(linear,right top, left bottom,color-stop(0, #901A1C),color-stop(1, #FF9980));
    background: linear-gradient(right top, #901A1C 0%, #FF9980 100%); 
 */
function colorieDemiJournee($codeCouleur, $type) {
    $retour = '';
    $sens='left';
    $oppose = 'right';
    if($type=="3"){
        $sens='right';
        $oppose = 'left';
    }
    $retour.= 'background-image: -moz-linear-gradient('.$sens.', #'. $codeCouleur. ' 0%,#FFF 70%);';
    $retour.= 'background: linear-gradient('.$sens.', #'. $codeCouleur. ' 0%,#FFF 70%);';
    return $retour;
}

function explodeMaj($texte) { 
    $tabChaine  = explode(' ',trim(preg_replace('#([A-Z])#',' $1',$texte)));
    return $tabChaine; 
} 


/**
 * underscoreToLibelle
 * Transforme un libelle en uderscore (ex: BD) en suite de mots
 * séparés par un espace.
 * Français seulement: Si le mot commence par date, rajoute 
 * la préposition de iu d'u suivant si le mot suivant comme par une
 * voyelle ou une majuscule.
 */
function underscoreToLibelle($texte) {
    $texte = ucfirst(str_replace('_', ' ', $texte));
    if(substr($texte, 0 ,4) == "Date") {
        $tabVoyelles = ['a', 'e', 'o', 'u', 'i'];
        $tabChaine  = explode(' ', $texte);
        $tabChaine[0] .= in_array(substr($tabChaine[0],0,1), $tabVoyelles) ?  ' d\' ' : ' de ';
        $texte = implode(" ", $tabChaine);
    }
    

    return str_replace('Num', 'N°', $texte);
}

/**
 * @name          getFormFromTable
 * @description   Obtient un formulaire à partir de la table $tableName
 * 
 * @param         mixed     $dbaccess: ressource de la base de données
 * @param         string   $tableName: Nom de la table
 * @param         int      $nbChampsParLigne: Nombre de champs par ligne (par défaut 3)
 * @return        string   $retour:   Formulaire au format html
 */
function getFormFromTable($dbaccess, $tableName, $nbChampsParLigne = 3) {
    $retour = '';   
    // Connexion
    $handler = $dbaccess->connect();
    if ($handler === false) {
        $retour = 'Problème de connexion à la base ';
    } else {
        $tabChamps = array();
        $tabChamps = $dbaccess->getTableDatas($tableName);
        $retour = '';
        if (is_array($tabChamps) && count($tabChamps) > 0) {
            $i = 0;
            $numGroupe = 0;
            $nbChampsParLigne = 3;
            $champPrefixe = substr($tableName, 0, 3);
            $retour .= '<div class="legende_titre"><h1>Enregistrement ' . $tableName .'</h1></div>';
            $retour .= '<form action="">'; 
            $retour .= '<div id="panel_' . $tableName . '" name = "panel_' .$tableName .'"><table id="' . $tableName . '" class= "tab_params">';
            // Liste de tous les types d'événement
            foreach ($tabChamps as $value) {
                $typeChamp = $value['typechamp'];
                $nomChamp = $value['nomchamp'];
                $isNullable = $value['is_nullable'];
                $modulo = intval($i % $nbChampsParLigne );
                if ($modulo == 1) {
                    $retour .=   '<tr id='.$numGroupe.'>';
                    //  class="'.$classeParite.'"
                }
                $classeIcone = ($isNullable == 'YES' ? '' : 'class="form_icon ui-icon ui-icon-alert"');
                $retour .= '<td>';
                $libelleChamp = underscoreToLibelle($nomChamp);
                $nomChampFinal = $champPrefixe . '_' . $nomChamp;
                // label
                $retour .= '<label for="' .$nomChampFinal . '">' . $libelleChamp . '</label>:&nbsp;';
                $required = ($isNullable == 'NO' ? 'required="required"' : '');
                

                // parsing champs
               if (strstr($nomChamp, 'mail') == true) {
                    $retour .= '<input type="email" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                            ' . $required . ' placeholder="' . $nomChamp . '" maxlength="30" onchange="verifEmail($(this).attr(\'name\'));/>';
                }else {
                    switch($typeChamp) {
                        case 'varchar':
                            $retour .= '<input type="text" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                    ' . $required . ' placeholder="' . $nomChamp . '" maxlength="30" />';
                        break;
                        case 'date':
                            $retour .= '<input type="date" id="' . $nomChampFinal .'" name="' . $nomChampFinal .'" 
                            ' . $required . ' size="10" maxlength="10" class="champ_date" />';
                        break;
                    }
                }
                $retour .='<span id="res_' . $nomChamp . '_img" name ="res_' . $nomChamp . '_img" ' . $classeIcone . '>&nbsp</span>';
                $retour .= '</td>';
                
                if ($modulo == $nbChampsParLigne || $i >= count($tabChamps)) {
                    $retour .="</tr>";
                    $numGroupe++;
                }
                if ($i >= count($tabChamps)-1) {
                    $retour .= '<tr><td><input type="submit" id="validation_' . $tableName . '" value="Enregistrer" onclick="validerSaisie' . ucfirst($tableName) .'();"/></td></tr>'; 
                    $retour .= '</table"></div>';
                }
                $i++;
            }
            
            
        }
        $retour .= '</form>';
    }
    $dbaccess->close($handler);
    echo $retour;
}

?>