<?php 
require_once ABS_GENERAL_PATH.'formFunctions.php';

/**
 * @name            ProcessFormulaires
 * @author          cvonfelten
 * @ description    Classe créant et validant les formulaires
 */

Class ProcessFormulaires {

    protected $_dbaccess;

    protected $_tabInsert;

    protected $_msgErr;

    protected $tableName;

    protected $idToModif;

    protected $_tabDefaultValues;



    public function __construct($dbaccess, $tableName = null, $idToModif = null) 
    {
        $this->_dbaccess = $dbaccess;
        $this->_tabInsert = array();
        $this->msgErr = array();
        $this->tableName = $tableName;
        $this->_tabDefaultValues = array();
        
    } 


    /**
     * @name addDefaultValue 
     * @description Ajoute des valeurs par défaut aux champs du formulaire spécifiés
     * sous la forme d'un tableau [$key] = $value.
     * @param String $key  Nom du champ 
     * @param String $value Valeur du champ
     * @return Array $tabDefaultValues Tableau de champs retourné au formulaire
     */
    public function addDefaultValue($key, $value) {
        $this->_tabDefaultValues[$key] = $value;
    }

    /**
     * @name          getFormFromTable 
     * @author cvonfelten
     * @description   Obtient un formulaire à partir de la table $tableName 
     * 
     * Pour la validation du formulaire, la méthode construit un bouton validation_[nomtable] 
     * et une méthode js à définir validerSaisie[Nomtable]
     * Pour spécifier des comportements à des champs spécifiques (comboboxs, champs énumérés), 
     * il faut que le script appelant implémente une classe fille héritant de ProcessFormulaires 
     * et surcharge la méthode getSpecificFields avec des conditions pour chaque champ à définir.
     * 
     * Création (insertion): $this->idToModif doit être = à null.
     * Modification: Si $this->idToModif est renseigné et différent de null, il s'agit d'une 
     * modification de l'élément portant cet identifiant et provenant de la table $this->tablename.
     * 
     * @param         string   $titre: Titre à définir (par défaut: Enregistrement $tableName) 
     * @param         int      $nbChampsParLigne: Nombre de champs par ligne (par défaut 3)
     * @return        string   $retour:   Formulaire au format html
     * 
     */
    public function getFormFromTable($titre = '', $nbChampsParLigne = 3) {
        $retour = '';   
        // Connexion
        $handler = $this->_dbaccess->connect();
        if ($handler === false) {
            $retour = 'Problème de connexion à la base ';
        } else {
            $tabChamps = array();
            $tabChamps = $this->_dbaccess->getTableDatas($this->tableName);
            $tabValeurs = $this->getElementbyIdForUpdate();
          
            

            $retour = '';
            if (is_array($tabChamps) && count($tabChamps) > 0) {
                $i = 0;
                $numGroupe = 0;
                $nbChampsParLigne = 3;

                $champPrefixe = substr($this->tableName, 0, 3);
                $titre == '' ? 'Enregistrement ' . $this->tableName : $titre;
                $retour .= '<div class="legende_titre" onclick="$(\'#div_saisie_activite\').toggle();"><h1>' . $titre .'</h1></div>';
                $retour .= '<div id="panel_' . $this->tableName . '" name = "panel_' . $this->tableName 
                .'"><table id="tab_' . $this->tableName . '" >';
                
                // Liste de tous les types d'événement
                foreach ($tabChamps as $key => $value) {
                    $typeChamp = $value['typechamp'];
                    $nomChamp = $value['nomchamp'];
                    $isNullable = $value['is_nullable'];
                    $valeur = (isset($tabValeurs[$key]) && $tabValeurs[$key] !== null) ? $tabValeurs[$key] : '';
                    $modulo = intval($i % $nbChampsParLigne ) +1;
                    if ($modulo == 1) {
                        $retour .=   '<tr id='.$numGroupe.'>';
                        //  class="'.$classeParite.'"
                    }
                    // Champs requis
                    $classeIcone = ($isNullable == 'YES' ? '' : 'class="form_icon ui-icon ui-icon-alert" title ="champ obligatoire"');
                    $retour .= '<td>';
                    $libelleChamp = underscoreToLibelle($nomChamp);
                    $nomChampFinal = $champPrefixe . '_' . $nomChamp;
                    // label
                    $retour .= '<label for="' .$nomChampFinal . '">' . $libelleChamp . '</label>:&nbsp;';
                    $required = ($isNullable == 'NO' ? 'required="required"' : '');
                    
                    // Champs spécifiques de la classe fille
                    $specificField = $this->getSpecificFields($nomChamp, $required, $valeur);
                    if($specificField !==null) {
                        $retour .= $specificField;
                    }

                    // parsing champs
                    if (strpos($nomChamp, 'mail') == true) {
                        $retour .= '<input type="email" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                        ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" onchange="verifEmail($(this).attr(\'name\'));"'
                        . ' value="' . $valeur . '">'; 
                    } elseif (strpos ($nomChamp, 'phone') == true || strpos ($nomChamp, 'mobile') == true ) {
                      $retour .= '<input type="tel" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" maxlength="16" onchange="verifPhone($(this).attr(\'name\'));"'
                                . ' value="' . $valeur . '">'; 
                    }else {
                        switch($typeChamp) {
                            case 'varchar':
                                $retour .= '<input type="text" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                        ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" maxlength="30"'
                                        . ' value="' . $valeur . '">'; 
                            break;
                            case 'integer':
                              $retour .= '<input type="number" id="' . $nomChampFinal .' " name="' . $nomChampFinal .'"
                                      ' . $required . ' placeholder="' . $nomChamp . '" alt = "' . $libelleChamp . '" maxlength="30"'
                                      . ' value="' . $valeur . '">'; 
                            break;
                            case 'date':
                                $retour .= '<input type="date" id="' . $nomChampFinal .'" name="' . $nomChampFinal .'" 
                                ' . $required . ' alt = "' . $libelleChamp . '" size="10" maxlength="10" class="champ_date" value="' . $valeur . '">'; 
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
                        $jsCallbackFunction = null;
                        if(isset($this->idToModif) && $this->idToModif !==null) {
                            $jsCallbackFunction = 'validerModification' . ucfirst($this->tableName) .'('. $this->idToModif .')';
                        }else {
                            $jsCallbackFunction = 'validerSaisie' . ucfirst($this->tableName) .'()';
                        }
                        $retour .= '<tr><td><button id="validation_' . $this->tableName . '" onclick="'. $jsCallbackFunction .';">Enregistrer</button></td></tr>'; 
                        $retour .= '</table"></div>';
                    }
                    $i++;
                }
                
                
            } else {
              $retour = '<div><table"><tr><td>La table est vide !</td></tr></table"></div>';
            }
        }
        $this->_dbaccess->close($handler);
        return $retour;
    }

    public function getSpecificFields($nomChamp, $required, $valeur) {
        return null;
    }

    public function getElementbyIdForUpdate() {

    }

    public function checkForm($tabChamps) 
    {
        $msgErr = "";
        $isOk = true;
        try {
            foreach($tabChamps as $stdObj) {
                $nomChamp = $stdObj['nom'];
                $nomChampFinal = substr($nomChamp, 4);
                $valeurChamp = $stdObj['valeur'];
                $typeChamp = $stdObj['type'];
                $labelChamp = $stdObj['label'];
                $requiredChamp = isset($stdObj['required']) ? $stdObj['required'] : false;
    
                // On ne prend pas en compte les champs vides
                if(empty($valeurChamp)) {
                  // ... sauf s'ils sont obligatoires
                    if($requiredChamp) {
                      $isOk = false;
                      $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . " est obligatoire.";
                    }
                } else {
                
                    switch($typeChamp) {
                        case 'email':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_EMAIL);
                          if(!filter_var($valeurChamp, FILTER_VALIDATE_EMAIL)) {
                            $isOk = false;
                            $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . " n'a pas une adresse email valide.";
                          }
                        break;
    
                        case 'text':
                            $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_STRING);
                            if($nomChampFinal == "nom" || $nomChampFinal == "prenom") {
                                if (!preg_match("/^[a-zA-Z-\séèàüöñøå' ]*$/", $valeurChamp)) {
                                  $this->msgErr .= "<br>Erreur: Seul les lettres et les espaces sont authorisés pour le champ " . $labelChamp;
                                  $isOk = false;
                                }
                            }
                        break;
    
                        case 'select-one':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          $nomChampFinal .= '_id';
                        break;
    
                        case 'date':
                            if (!preg_match("/^(\d{4})(-)(\d{1,2})(-)(\d{1,2})$/", $valeurChamp)) {
                              $this->msgErr .= "<br>Erreur: Seul le format date aaaa-mm-jj est authorisé pour le champ " . $labelChamp;
                              $isOk = false;
                            }
                        break;
    
                        case 'tel':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          if (!preg_match("/^[0-9]{9,}$/", $valeurChamp)) {
                            $this->msgErr .= "<br>Erreur: Seul les chifres sont authorisés pour le champ " . $labelChamp;
                            $isOk = false;
                          }
                        break;
    
                        case 'num':
                          $valeurChamp = filter_var($valeurChamp, FILTER_SANITIZE_NUMBER_INT);
                          if(!filter_var($valeurChamp, FILTER_VALIDATE_INT)) {
                            $isOk = false;
                            $this->msgErr .= "<br>Erreur: Le champ " . $labelChamp . "ne contient pas de valeurs numériques.";
                          }
                        break;
    
                        default:
                          // radios
    
                        break;
                    }
    
    
                }
                if($isOk) {
                    $this->_tabInsert[$nomChampFinal] = $valeurChamp;
                }
    
            }
        } catch (Exception $e) {
          $this->msgErr .=  "Erreur: Une erreur s'est produite lors de l'enregistrement du champ " . $labelChamp;
          $isOk = false;
        }

        return $isOk;
    }

    public function getDbAccess() {
        return $this->_dbaccess;
    }

    public function getTabInsert() {
        return $this->_tabInsert;
    }

    public function getMsgErreurs() {
        return $this->msgErr;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function setIdToModif($idToModif) {
        $this->idToModif = $idToModif;
    }




}