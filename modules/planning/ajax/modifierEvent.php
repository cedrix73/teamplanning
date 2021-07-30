<?php

include_once '../../../config.php';
require_once ABS_CLASSES_PATH . $dbFile;
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_CLASSES_PATH . 'CvfDate.php';
require_once ABS_PLANNING_PATH . CLASSES_PATH . 'Planning.php';


/* 
 * Création ou modification d'un événement selon la variable javascript 
 * infoRessource.action ={insertion, modification}
 */

function verifDate($valDate) {
    $retour = '';
    $isOk = true;
    if (!preg_match("/^(\d{1,2})(\/)(\d{1,2})(\/)(\d{4})$/", $valDate)) {
        $retour .= "<br>Erreur: Seul le format date jj-mm-aaaa est authorisé pour le champ " . $valDate;
        $isOk = false;
    }
    return $isOk;
}


$retour = '';   
$isOk = false;

$ressourceId = '';
if(isset($_POST['ressource_id']) && !is_null($_POST['ressource_id']) &&  $_POST['ressource_id'] == true){
    $ressourceId = $_POST['ressource_id'];
    $isOk = true;
}

$activiteSel = '';
if(isset($_POST['activite_sel']) && !is_null($_POST['activite_sel']) &&  $_POST['activite_sel'] == true){
    $activiteSel = $_POST['activite_sel'];
    $isOk = true;
}

$dateDebut = '';
if(isset($_POST['date_debut']) && !is_null($_POST['date_debut']) &&  $_POST['date_debut'] == true &&  verifDate($_POST['date_debut'])){
    $dateDebut = $_POST['date_debut'];
    $isOk = true;
}

$dateFin = '';
if(isset($_POST['date_fin']) && !is_null($_POST['date_fin']) &&  $_POST['date_fin'] == true && verifDate($_POST['date_fin'])){
    $dateFin = $_POST['date_fin'];
    $isOk = true;
}

$actionUser = '';
if(isset($_POST['action_user']) && !is_null($_POST['action_user']) &&  $_POST['action_user'] == true){
    $actionUser = $_POST['action_user'];
    $isOk = true;
}

$periode = 1;
if(isset($_POST['periode_sel']) && !is_null($_POST['periode_sel']) &&  $_POST['periode_sel'] == true){
    $periode = $_POST['periode_sel'];
    $isOk = true;
}

if(isset($_POST['old_date_debut']) && !is_null($_POST['old_date_debut']) &&  $_POST['old_date_debut'] == true && verifDate($_POST['old_date_debut'])){
    $oldDateDebut = $_POST['old_date_debut'];
    $isOk = true;
}

if(isset($_POST['old_date_fin']) && !is_null($_POST['old_date_fin']) &&  $_POST['old_date_fin'] == true && verifDate($_POST['old_date_fin'])){
    $oldDateFin = $_POST['old_date_fin'];
    $isOk = true;
}
if($isOk===FALSE){
    $retour = 'Paramètres incorrects';
}

// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
if($handler===FALSE){
    $retour = 'Problème de connexion à la base ';
    $isOk = false;
}

if($isOk){
    $retour = '';
    $modification = false;
    $planning = new Planning($dbaccess, $ressourceId, $activiteSel, $dateDebut, $dateFin, $periode, true);
    $planning->setOldDateDebut($oldDateDebut);
    $planning->setOldDateFin($oldDateFin);
    // si modification autre que les dates
    if($oldDateDebut <> $dateDebut && $dateDebut < $dateFin) {
        // Est ce qu'on a un evenement pour la même ressource et pour le(s) même(s) jour(s) ?
        $tabActivites = $planning->read();
        if(count($tabActivites) > 0){
            try {
                $modification = $planning->delete();
            } catch(Exception $e) {
                $modification = false; 
                $retour .= "Il y a eu un problème SQL au moment de la suppression d'événements déjà existants !";
            }
            
        }
    }

    $rs = $planning->update();
    if($rs === false) {
        $retour .= "Il y a eu un problème SQL au moment de la modification !" . $dbaccess->getError();
    } else {
        $retour .= "Modification effectuée avec succès.";
    }
}

$dbaccess->close($handler);
echo $retour;


?>
