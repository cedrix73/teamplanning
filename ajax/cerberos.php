<?php

require_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_CLASSES_PATH.'Ressource.php';
require_once ABS_CLASSES_PATH.'ProcessFormulaires.php';
require_once ABS_GENERAL_PATH.'formFunctions.php'; 



/* 
 * Sanitization et vérification back-office du formulaire posté 
 * felten.cedric@yahoo.ch
 * 
 */

$retour = "";   
$isOk = false;
$msgErr = "";
$tabRetour = array();
$tabRetour['message'] = false;

// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();

$isOk = true;
$accreditation = false;
if($handler===FALSE){
    $accreditation = false;
    $msgErr = "Problème de connexion à la base ";
    $isOk = false;
} else {

    $validationClass = new ProcessFormulaires($dbaccess);


    if (!isset($_POST['json_datas']) && is_null($_POST['json_datas']) &&  $_POST['json_datas'] == false) {
        $isOk = false;
    } else {
        $jsonString = $_POST['json_datas'];
        $tabJson = json_decode($jsonString, true);
        if(!$validationClass->checkForm($tabJson, true)) {
          $isOk = false;
          $msgErr = $validationClass->getMsgErreurs();
       }
    }
}

// Verification mail est rempli
if($isOk) {
  $tabValeurs = $validationClass->getTabInsert();
  $checkMail = "";
  if(!isset($tabValeurs['usermail']) || is_null($tabValeurs['usermail']) || $tabValeurs['usermail']  == false){
    $isOk = false;
    $msgErr .= "Erreur: adresse email vide ou non valide.";
  } else {
    $checkMail = $tabValeurs['usermail'] ;
  }
} 
// Verification mdp est rempli
if($isOk) {
    $checkMdp = '';
    if(!isset($tabValeurs['userpassword']) || is_null($tabValeurs['userpassword']) &&  $tabValeurs['userpassword'] == false || strlen($tabValeurs['userpassword'] ) == 0){
      $isOk = false; 
      $msgErr .= "Erreur: mot de passe vide ou non valide.";
    } else {
      $checkMdp = $tabValeurs['userpassword'];
    }
}

unset($tabValeurs);

if($isOk) {
  
  // inputs ok, on soulet à authentification
  $ressource = new Ressource($dbaccess);
  // non: retour boolean et chercher message dans l'instance, comme pour ProcessFormulaires
  $tabRetour = $ressource->authenticate($checkMail, $checkMdp);
} else {
  $tabRetour['message'] = $msgErr;
  $tabRetour['is_ok'] = false;
}

      
$dbaccess->close($handler);

$retour = json_encode($tabRetour);
echo $retour;

?>