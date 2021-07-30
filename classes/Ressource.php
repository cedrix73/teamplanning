<?php

/**
 * Classe gérant les ressources
 *
 * @author CV170C7N
 */
class Ressource {
    
    protected $dbaccess;
    protected $site;
    protected $service;
    protected $domaine;
    protected $tabRessources;
    protected $requeteRessources;
    protected $requeteJointures;
    private $_tableName;
    
    /**
     * Constructeur de la classe ressource
     * @param $dbaccess :Pointeur de ressource de la base de données
     * @param $allFields :
     * true: on récupère tout les champs de la table ressources
     * false: on récupère les champs ressource.id as ressource_id, 
     * ressource.nom, ressource.prenom, ressource.statut
     * site.libelle, departement.libelle, service.libelle
     */
    public function __construct($dbaccess, $allFields = false) 
    {
        $this->dbaccess = $dbaccess;
        $this->_tableName = "ressource";
        $this->siteId = false;
        $this->departementLibelle = false;
        $this->serviceLibelle = false;
        $this->tabRessources =  array();
        if($allFields === false) {
            $this->requeteSelect = "SELECT ressource.id as ressource_id, ressource.nom, ressource.prenom, "
        . " site.libelle, departement.libelle, service.libelle, ressource.statut "
        . " FROM " . $this->_tableName;
        } else {
            $this->requeteSelect = $this->selectAllFields();
        }
        
        $this->requeteJointures = " INNER JOIN service on ressource.service_id = service.id " 
                               . " INNER JOIN departement on service.departement_id = departement.id " 
                               . " INNER JOIN site on departement.site_id = site.id ";
        
        
    }
    
    /**
     * @name GetRessourcesBySelection
     * @description  Sort les ressources en Ajax en fonction 
     * des valeur (chaine) sélectionnées à partir du
     * combobox site, département et service du formulaire
     * 
     * @param int    $siteId             
     * @param string $departementLibelle 
     * @param string $serviceLibelle     
     * 
     * @return array
     */
    public function getRessourcesBySelection($site = null, $departementLibelle = '', $serviceLibelle = ''){
        $requete = $this->requeteSelect . $this->requeteJointures;
        $requete . " WHERE dateSortie IS NULL";
        // Traitement sites
        if($site != null && $site!='Tous*'){
            $this->siteId = $site;
            $requete.= " AND site.id = '" . $this->siteId ."'";
        }

        // Traitement departements
        if($departementLibelle != null && $departementLibelle != 'Tous*'){
            $this->departementLibelle = $departementLibelle;
            $requete.= " AND departement.libelle = '" . $this->departementLibelle ."'";
        }
        
        // Traitement services
        if($serviceLibelle != null && $serviceLibelle != 'Tous*'){
            $this->serviceLibelle = $serviceLibelle;
            $requete.= " AND service.libelle = '" . $this->serviceLibelle ."'";
        } 
        
        $requete.= " ORDER BY ressource.nom, site.id ";
	    $rs = $this->dbaccess->execQuery($requete);
        $results=$this->dbaccess->fetchArray($rs);
        
        foreach ($results as $ligne) {
            $id = $ligne['ressource_id'];
            unset($ligne['ressource_id']);
            $this->tabRessources[$id]=array_map('utf8_encode', $ligne);
        }
        return $this->tabRessources;
    }


    
    
    
    
    
    
    /**
     * @name GetRessourceById
     * @description Retourne l'id, le nom, prénom d'une ressource
     * ainsi que son affectation (site, département et service)
     * 
     * @param int $idRessource 
     * @return array
     */
    public function getRessourceById($idRessource)
    {
        $ressource = array();
        $requete = $this->requeteSelect . $this->requeteJointures;
        $requete .= " WHERE dateSortie IS NULL"
                 . " AND id = " . $idRessource;
        $rs = $this->dbaccess->execQuery($requete);
        $ressource=$this->dbaccess->fetchArray($rs);
        return $ressource;
    }

    /**
     * SelectAllFields 
     * Obtient tous les champs de la table ressource.
     */
    public function selectAllFields()

    {
        $listeChampsRes = $this->dbaccess->getTableFields('ressource');
        $select = 'SELECT ';
        $i=0;
        $last = count($listeChampsRes) -1;
        foreach($listeChampsRes as $value) {
            $select .=  $value['nomchamp'];
            $select .= ($i==$last) ? '' : ', ';
            $i++;
        }

        $this->requeteSelect = $select;
    }
    /**
     * @name Create
     * @description Enregistre une ressource en base de donnée
     * @param array $tabInsert tableau des enregistrements: 
     * key: nom du champ
     * value: valeur du champ 
     * 
     * @return String $retour :Message de feedback (erreur ou OK)
     */
    public function create($tabInsert)
    {

        $retour = $this->dbaccess->create($this->_tableName, $tabInsert);
        if($retour !== false){
            $retour = "Les données du collaborateur ont été correctement enregistrées !";
        }else{
            $retour = "Erreur: Un problème est survenu lors de la création d\'un collaborateur.";
        }
        return $retour;   
    }


    /**
     * @name Update
     * @description Modifie une ressource en base de donnée
     * @param array $tabUpdate tableau des enregistrements: 
     * key: nom du champ
     * value: valeur du champ 
     * 
     * @return String $retour :Message de feedback (erreur ou OK)
     */
    public function update($tabUpdate, $idRessource)
    {

        $retour = $this->dbaccess->update($this->_tableName, $tabUpdate, $idRessource);
        if($retour !== false){
            $retour = "La modification du collaborateur ont été correctement enregistrées !";
        }else{
            $retour = "Erreur: Un problème est survenu lors de la modification d\'un collaborateur.";
        }
        return $retour;   
    }
}
