<?php

//include_once 'config.php';
require_once '../../config.php';
require_once ABS_CLASSES_PATH . 'DbAccess.php';
require_once ABS_GENERAL_PATH . 'formFunctions.php';
require_once ABS_CLASSES_PATH . 'Ressource.php';
require_once ABS_CLASSES_PATH . 'CvfDate.php';

  // Connection
$dbaccess = new DbAccess($dbObj);
$handler = $dbaccess->connect();

if ($handler === false) {
    echo 'Handler NOK...';
    die();
}
// Affichage message erreur BD techniques ?
$dbaccess->setError(M_LOG);

$blnSites = false;
$blnDepartements = false;
$blnServices = false;

$blnRessources = false;

// Liste des sites

$listeSites = tabLoad('libelle', 'site', $dbaccess);
$listeSites[0] = "Tous *";
ksort($listeSites);

if (count($listeSites) > 1) {
    $blnSites = true;

    // Liste des departements
    $filtreDepartements = "";
    $listeDepartements = listeLoad('libelle', 'departement', $dbaccess, $filtreDepartements);
    if (count($listeDepartements) > 1) {
        $blnDepartements = true;
        $listeServices = listeLoad('libelle', 'service', $dbaccess);
        if (count($listeServices) > 1) {
            $blnServices = true;
            $listeRessources = listeLoad('id', 'ressource', $dbaccess, 'LIMIT 1');
            if (count($listeRessources) > 1) {
                $blnRessources = true;
            }
        }

    }
}

$siteDefaut = 'Tous *';
$departementDefaut = 'Tous *';
$serviceDefaut = 'Tous *';


// Charger la service de l'utilisateur connecté
$ressource = new Ressource($dbaccess);

if(isset($idUser) && $idUser != false) {
    $tabUser = $ressource->getRessourceById($idUser);
    $serviceDefaut = $tabUser['service'];
    $siteDefaut = $tabUser['site'];
}

$refreshCalendarOption = '';

?>
<!DOCTYPE html>
<html>
    <head>
            <title>Planning</title>
            <meta http-equiv="Content-Type" content="text/HTML" charset="utf-8" />
            <meta http-equiv="Content-Language" content="fr" />
            <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
            <META NAME="Author" CONTENT="Cédric Von Felten">
            <link rel="stylesheet" type="text/css" media="screen"  href="../../styles/redmond/jquery-ui-1.11.4.custom.min.css" />
            <link type="text/css" rel="stylesheet" href="../../styles/principal.css">
            <script src="../../js/jquery-1.8.3.min.js"></script>
            <script src="../../js/jquery-ui-1.11.4.custom.min.js"></script>
            <script src="../../js/general.js"></script>
            <script src="js/planning.js"></script>
            <script src="../../js/localisation.js"></script>
            <script src="../../js/ressource.js"></script>
            <script src="js/event.js"></script>

            <script>	
                    $(document).ready(function(){
                        cacherComposantsInfo();
                        <?php if ($blnRessources) {
                            $refreshCalendarOption = 'refreshCalendar(null);';
                            echo 'initialiserFormulaire();';
                        } else {
                            ?>afficherTexteStarter();<?php
                            if (!$blnSites) {
                                $prefixe = ' tout d\'abord ';
                                $obj = 'site';

                            } elseif (!$blnDepartements) {
                                $prefixe = 'maintenant';
                                $obj = 'departement';

                            } elseif (!$blnServices) {
                                $prefixe = 'ensuite';
                                $obj = 'service';
                            }

                            if ($blnSites && $blnDepartements && $blnServices){
                                echo 'afficherMessage("Veuillez  enregistrer un premier collaborateur pour continuer");';
                                echo 'afficherFormRessources();';
                                //echo 'form_departements_load($("#res_site").options[$("#res_site").selectedIndex].value);';

                            }else{
                                echo '$("#div_saisie_activite").html("<div class=\"text_helper\">Veuillez ' . $prefixe . ' enregistrer un premier ' . $obj 
                                   . ' pour continuer");';
                                
                            }
                        }
                        ?> 
                        $("#div_saisie_activite").hide();
                        initialiserFormulaire.saisieActivite = $("#div_saisie_activite").html();
                    });
                    $(window).resize(function(){
                        refreshCalendar(null);
                    });

            </script>
    </head>
    <body>
        <div id="cadre">
            <div id="entete_planning">
              <?php echo 'Planning des disponibilités';?>
            </div>
           <div id="main">
                <div id="menu_gauche" class="column">
                    <div class="titre"><?php echo 'Sélection d\'une date';?></div>
                    <div id ="div_date" class="champ_date">&nbsp;</div>
                    <div id ="div_choix">
                        <div class="titre"><?php echo 'Sélection du périmètre';?></div>
                        <fieldset id="fielset_sites">
                            <legend>Sites</legend>
                            <?php if ($blnSites) { ?>
                                <select id="cbo_sites" name="cbo_sites" onchange="<?php echo $refreshCalendarOption;?>
                                liste_departements_load(this.options[this.selectedIndex].value);liste_services_load(cbo_sites.options[cbo_sites.selectedIndex].value, options[this.selectedIndex].value);">
                                <?php
                                foreach ($listeSites as $key => $value) {
                                    $pref = ($value == $siteDefaut) ? 'selected = selected' : "";
                                    ?><option value="<?php echo $key;?>" <?php echo $pref;?>><?php echo $value;?></option>
                                    <?php 
                                }
                                ?>
                            </select>
                            <?php } ?>
                            <input id="new_site" type="button" name ="site" value="+" onclick="afficherTypesLocalisation();"/>
                        </fieldset>
                        <fieldset id="fielset_departements">
                            <legend>Departements</legend>
                            <?php if ($blnDepartements) { ?>
                            <select id="cbo_departements" name="cbo_departements" onchange="<?php echo $refreshCalendarOption;?>
                            liste_services_load(cbo_sites.options[cbo_sites.selectedIndex].value, options[this.selectedIndex].value);">
                                
                            </select>
                            <?php } 
                            if ($blnSites) {
                            ?>
                            <input id="new_departement" type="button" value="+" onclick="afficherTypesLocalisation(cbo_sites.options[cbo_sites.selectedIndex].value, '');"/>
                            <?php } ?>
                        </fieldset>
                        <fieldset id="fielset_service">
                            <legend>Services</legend>
                            <?php if ($blnServices) { ?>
                            <select id="cbo_services" name="cbo_services" onchange="<?php echo $refreshCalendarOption;?>">
                            
                            </select>
                            <?php } 
                            if ($blnDepartements) {
                            ?>
                            <input id="new_service" type="button" value="+" onclick="afficherTypesLocalisation(cbo_sites.options[cbo_sites.selectedIndex].value, cbo_departements.options[cbo_departements.selectedIndex].value);"/>
                            <?php } ?>
                        </fieldset>
                        <?php if(isset($isAdmin) && $isAdmin){?>
                        <fieldset id="menu_prefs">
                            <legend>Administration</legend>
                            <span id="prefs_activite" class ="liens_admin" onclick="afficherTypesEvents();"><a><?php echo '- Modifier types d\'activités';?></a></span>
                            <span id="prefs_ressources" class ="liens_admin" onclick="afficherFormRessources();"><a><?php echo '- Ajouter des ressources';?></a></span>
                        </fieldset>
                        <?php } ?>
                    </div>
                </div>

                <div class="col_droite" class="column">
                    <div id="planning">
                    
              
                    </div><!-- fin div planning -->
               </div>
           </div> 
            
            <div id ="div_info">
                <div id="div_cadre_saisie_activite">
                    <div id ="div_saisie_activite" style="float:left;" class="tab_params">
                        <fieldset id="fld_saisie_activite" name ="fld_saisie_activite">
                            <legend id = "lgd_saisie_activite" class ="legende_activite" onclick="cacherComposantsInfo();">Saisie d'une absence</legend>
                            <span>Du&nbsp;</span>  
                            <input type="text"
                            name="txt_str_date_debut"
                            value=""
                            id="txt_str_date_debut" 
                            size="10" maxlength="10"
                             readonly>                     
                            <span>&nbsp;au&nbsp;</span>   
                            <input type="text"
                            name="txt_str_date_fin"
                            id="txt_str_date_fin"
                            value=""
                            size="10" maxlength="10"
                            class="champ_date_fin" readonly>
                            <span>&nbsp;Type d'absence:&nbsp;</span> 
                            <select id="lst_activites" name="lst_activites"></select>
                            <span>&nbsp;Période:&nbsp;</span> 
                            <select id="lst_periodes">
                                <option id="periode_journee" value="1" selected = "selected">journée</option>
                                <option id="periode_matin" value="2">matin</option>
                                <option id="periode_am" value="3">après-midi</option>
                            </select>
                        </fieldset>
                                

                        <fieldset id="fld_modification_activite" name="fld_modification_activite">
                        Modification:</br>
                        <span>Du&nbsp;</span>  
                            <input type="text"
                            name="txt_str_date_debut_modif"
                            value=""
                            id="txt_str_date_debut_modif" 
                            onchange="attribuerDateFinModif(this.value);" 
                            size="10" maxlength="10"
                            readonly>                     
                            <span>&nbsp;au&nbsp;</span>   
                            <input type="text"
                            name="txt_str_date_fin_modif"
                            id="txt_str_date_fin_modif"
                            value=""
                            size="10" maxlength="10"
                            class="champ_date_fin" readonly>
                            <span>&nbsp;Type d'absence:&nbsp;</span> 
                            <select id="lst_activites_modif" name="lst_activites_modif"></select>
                            <span>&nbsp;Période:&nbsp;</span> 
                            <select id="lst_periodes_modif">
                            </select>
                            <img id="supprimer" src="<?php echo MAIN_IMAGES_PATH . 'supprimer.jpg';?>" 
                                onclick='supprimerSaisie()' title = 'supprimer' />
                        </fieldset>
                        <input id = "btn_valider_saisie" name = "btn_valider_saisie" 
                                    input type="button" value = "Valider" onclick="verifierEvent();" />                        
                    </div>
                </div>
                <div id="message"><div id="message_box">&nbsp;</div>
                <img id="img_loading" src="<?php echo MAIN_IMAGES_PATH . 'loader.gif';?>" alt="Loading" />
            </div>
        </div>
        <input type = "hidden" id = "affichage_activite" name ="affichage_activite" />
    </body>
</html>
