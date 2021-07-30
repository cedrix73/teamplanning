<?php

include_once '../../../config.php';
require_once ABS_CLASSES_PATH . $dbFile;
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_GENERAL_PATH . 'formFunctions.php';
require_once ABS_PLANNING_PATH . CLASSES_PATH . 'Event.php';
//require_once ABS_GENERAL_PATH.'formFunctions.php';


/* 
 * Modification d'un type d'activité donné
 */

$retour = '';   
$isOk = false;

$activiteCouleur = '';
if(isset($_POST['activite_couleur']) && !is_null($_POST['activite_couleur']) &&  $_POST['activite_couleur'] == true){
    $activiteCouleur = $_POST['activite_couleur'];
    $isOk = true;
}

$activiteAbbrev = '';
if(isset($_POST['activite_abbrev']) && !is_null($_POST['activite_abbrev']) &&  $_POST['activite_abbrev'] == true){
    $activiteAbbrev = $_POST['activite_abbrev'];
    $isOk = true;
}

$activiteLibelle= '';
if(isset($_POST['activite_libelle']) && !is_null($_POST['activite_libelle']) &&  $_POST['activite_libelle'] == true){
    $activiteLibelle = $_POST['activite_libelle'];
    $isOk = true;
}

// On enlève le # qu'on ne souhaite pas sauver en base
$activiteCouleur = str_replace('#', '', $activiteCouleur);


if($isOk===FALSE){
    $retour = 'Paramètres incorrects';
}


// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Erreur: Problème de connexion à la base ' . $dbaccess->getError();
    $isOk = false;
}

$insertion = false;
if($isOk){
    $activite = new Event($dbaccess);
    $tabInsert = array();
    $tabInsert['event_libelle'] = $activiteLibelle;
    $tabInsert['event_couleur'] = $activiteCouleur;
    $tabInsert['event_affichage'] = $activiteAbbrev;
    
    
    $insertion = $activite->create($tabInsert);
    if($insertion === false){
        $retour = 'Un problème est survenu lors de la création du type d\'activité !' . $dbaccess->getError();
        //$retour.= $activite->getSql();
    }else{
        $retour = 'Votre nouveau type d\'activité a été créé.';
    }
}

$dbaccess->close($handler);
echo $retour;


?>
