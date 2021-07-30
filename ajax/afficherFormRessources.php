<?php

include_once '../config.php';
require_once ABS_CLASSES_PATH.$dbFile;
require_once ABS_CLASSES_PATH.'DbAccess.php';
require_once ABS_GENERAL_PATH.'formFunctions.php';
require_once ABS_CLASSES_PATH. 'ProcessFormulaires.php';

/**
 * @name        ProcessFormulaires
 * @author      cvonfelten
 * @description Classe créant et validant les formulaires
 * 
 */

Class RessourceProcessFormulaires extends ProcessFormulaires {

    

    public function __construct($dbaccess, $tableName = null, $id = null) 
    {
        
        $this->idToModif = $id;
        parent::__construct($dbaccess, $tableName, $id=null);
        
    } 

    /**
     * @name           getSpecificFields
     * @description    surcharge la méthode mère avec les listes déroulantes 
     *                 spécifiques à la localisation des ressources. 
     * @param          String $nomChamp :nom du champ à identifier 
     * @param          String $required :Si champ requis: 'required="required' sinon vide. 
     * @param          String $valeur : valeur par défaut pour le type de champ activé.
     * @return         String   $retour :Section du formulaire au format html
     */
    public function getSpecificFields($nomChamp, $required, $valeur="") {
        $retour = null;
        if ($nomChamp == 'site_id') {
            $optionsSite = selectLoad('libelle', 'site', $this->getDbAccess(), $valeur);
            $retour .=  '<select id="res_site" name ="res_site" '.$required
                    .' alt = "selectionnez un site" onchange="form_departements_load(this.options[this.selectedIndex].value)">' . $optionsSite . "</select>";

        } elseif ($nomChamp == 'departement_id') {
            $optionsDepartement = isset($valeur) && $valeur !== "" ? selectLoad('libelle', 'departement', $this->getDbAccess(), $valeur) : '';
            $retour .= '<select id="res_departement" name ="res_departement" '.$required
                    .' alt = "selectionnez un département" onchange="form_services_load(res_site.options[res_site.selectedIndex].value, options[this.selectedIndex].value);">' . $optionsDepartement . "</select>";

        } elseif ($nomChamp == 'service_id') {
            $optionsService = isset($valeur) && $valeur !== "" ? selectLoad('libelle', 'service', $this->getDbAccess(), $valeur) : '';
            $retour .= '<select id="res_service" name="res_service" '.$required.' alt = "selectionnez un service">' . $optionsService . "</select>";
        }

        return $retour;
    }

    
 

    /**
     * @name getElementbyIdForUpdate
     * @description Selection une ressource par son $id et retourne 
     * un tableau [nomDuChamp] = $valeur
     * @param integer $this->idToModif
     * @return array $listeDonneesRes
     */
    public function getElementbyIdForUpdate() {
        $listeDonneesRes = array();
        $listeDonneesResEncoded = array();
        if (isset ($this->idToModif) && $this->idToModif!== null) {
            $select = "SELECT * from ressource WHERE id = " . $this->idToModif;
            $rs = $this->getDbAccess()->execPreparedQuery($select);
            $listeDonneesRes = $this->getDbAccess()->fetchArray($rs);
            unset($listeDonneesRes[0]['id']);
        } else {
            $listeDonneesRes[0] = $this->_tabDefaultValues;
        }
        $listeDonneesResEncoded =  array_map('rightEncode', $listeDonneesRes[0]);

        return $listeDonneesResEncoded;
    }

    

}



/**
 * Ce script surcharge la fonction ProcessFormulaires:getFormFromTable
 */

$retour = '';   
// Connexion
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();
$retour = null;
$isOk = true;
$msgErr = '';
$id = null;
if ($handler === false) {
    $isOk = false;
    $msgErr = 'Problème de connexion à la base ';
} else {
    if(isset($_POST['res_id']) && $_POST['res_id']!=='') {
        if(!filter_var($_POST['res_id'], FILTER_VALIDATE_INT)) {
            $isOk = false;
            $msgErr .= "<br>Erreur:  Un paramètre est erroné.";
        } else {
            $id = $_POST['res_id'];
        }
    }

    



    
}

if(!$isOk) {
    echo $msgErr;
} else {
    $ressourceProcessFormulaire = new RessourceProcessFormulaires($dbaccess, 'ressource', $id);
    // Update 26/05/2021: Valeurs sélectionnées dans la liste de recherche
    if(isset($_POST['site']) && $_POST['site']!=='' && $_POST['site']!=='Tous *') {
        if(!filter_var($_POST['site'], FILTER_VALIDATE_INT)) {
            $isOk = false;
            $msgErr .= "<br>Erreur:  Un paramètre est erroné.";
        } else {
            $ressourceProcessFormulaire->addDefaultValue('site_id', $_POST['site']);
        }
    }

    if(isset($_POST['departement']) && $_POST['departement']!=='' && $_POST['departement']!=='Tous *') {
        if(!filter_var($_POST['site'], FILTER_VALIDATE_INT)) {
            $isOk = false;
            $msgErr .= "<br>Erreur:  Un paramètre est erroné.";
        } else {
            $ressourceProcessFormulaire->addDefaultValue('departement_id', $_POST['departement']);
        }
    }

    if(isset($_POST['service']) && $_POST['service']!=='' && $_POST['service']!=='Tous *') {
        if(!filter_var($_POST['site'], FILTER_VALIDATE_INT)) {
            $isOk = false;
            $msgErr .= "<br>Erreur:  Un paramètre est erroné.";
        } else {
            $ressourceProcessFormulaire->addDefaultValue('service_id', $_POST['service']);
        }
    }


    $retour = $ressourceProcessFormulaire->getFormFromTable('Enregistrement d\'une ressource');
    echo $retour;
}
$dbaccess->close($handler);





?>
