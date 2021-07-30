<?php

include_once '../../../config.php';
require_once ABS_CLASSES_PATH . $dbFile;
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_GENERAL_PATH . 'formFunctions.php';


/* 
 * Création ou modification d'un événement selon la variable javascript 
 * infoRessource.action ={insertion, modification}
 */

$retour = '';   
$isOk = true;
$periode = 1;
$idActivite = false;
if(isset($_POST['id_activite']) && 
    !is_null($_POST['id_activite']) 
    &&  $_POST['id_activite'] == true  
    && is_numeric($_POST['id_activite']))
{
    $idActivite = $_POST['id_activite'];
    $isOk = true;
}

// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
}

if($isOk){
    $retour = selectLoad('libelle', 'evenement', $dbaccess, $idActivite);
}
$dbaccess->close($handler);
echo $retour;
?>
