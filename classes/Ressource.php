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
    private $departementLibelle;
    private $serviceLibelle;
    private $siteId;
    private $requeteSelect;
    private $num;
    private $username;
    private $password;
    private $host;
    private $role; 
    private $message;
    
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
        $this->message = false;
        $this->tabRessources =  array();
        if($allFields === false) {
            $this->requeteSelect = "SELECT ressource.id as ressource_id, ressource.nom, ressource.prenom, ressource.adresse_mail, "
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

    /**
     * @name  getRessourceByMail
     * @description Recherche un utilisateur avec le paramètre email 
     * @param string login  :  adresse mail de l'utilisateur 
     * @return array $ressource  ressource_id, ressource_prenom, ressource_nom ressource_mail
     */
    function getRessourceByMail($login){
        $ressource = false;
        $req = $req =  " SELECT ressource.id AS ressource_id,  "
        . " ressource.prenom AS ressource_prenom, ressource.nom AS ressource_nom, ressource.adresse_mail AS ressource_mail " 
        . " FROM " . $this->_tableName  
        . " WHERE adresse_mail=? LIMIT 1 ";
        $stmt = $this->dbaccess->execPreparedQuery($req, $login);
        if($stmt === false) {
            $this->message = $this->dbaccess->getErrorMessage();
        } else {
            $ressource = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $ressource;
    }

    /**
     * @name  getPasswordFromRessource
     * @description retourne le mdp utilisateur depuis un tableau de la ressource
     * @param array $tabInsert tableau de la ressource 
     * @return array $user  ressource_id,  ressource.mot_de_passe (hash), ressource_prenom, ressource_nom
     */
    function getPasswordFromRessource($ressource){
        $user = false;
        $req =  " SELECT ressource.id AS ressource_id, ressource.mot_de_passe AS mot_de_passe  "
        . " FROM " . $this->_tableName 
        . " WHERE adresse_mail=? LIMIT 1 ";
        $stmt = $this->dbaccess->execPreparedQuery($req, $ressource['ressource_mail']);
        if($stmt === false) {
            $this->message = $this->dbaccess->getErrorMessage();
        } else {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $user;
    }






    /**
     * @name authenticate
     * @description : authentifie l'utilisateur en 2 étapes :
     * 1) Verification du mail utilisateur en BD (requête)
     * 2) Extrait vérifie le mot de passe ()
     *  https://waytolearnx.com/2020/01/formulaire-dauthentification-login-mot-de-passe-avec-php-et-mysql.html 
     *  https://deliciousbrains.com/php-encryption-methods/
     * @param $login    string
     * @param $password string
     * @param $no_msg boolean optionnal
     */
    public function authenticate($login, $password, $no_msg = 0) 
    {
        $retour = array();
        $isOk = false;
        $utilisateurTrouve = false;
        $ressource = $this->getRessourceByMail($login);
        if(!isset($ressource) || $ressource['ressource_mail'] == FALSE) {
            if ($no_msg == 0) {
				$this->message .= "Une erreur en base de données est survenue. " . $this->message;
			}
        } else {
            if(count($ressource)==0) {
                $this->message = "Erreur: Utilisateur non enregistré.";
            } else {
                $utilisateurTrouve = true;
            }
        }
            
        if($utilisateurTrouve) {
            $fetchMdp = $this->getPasswordFromRessource($ressource);
            
            if (isset($fetchMdp['mot_de_passe']) && strlen($fetchMdp['mot_de_passe']) > 0) { 
                $mdpRessource = $fetchMdp['mot_de_passe'];
                unset($fetchMdp);
                $ressource['mot_de_passe'] = $mdpRessource ;
                if (!password_verify($password, $mdpRessource)) {
                    $this->message = "Erreur: Mot de passe invalide.";
                } else {
                    $isOk = true;
                    $this->message = "Bienvenue à votre espace personnel, " . $ressource['ressource_prenom'];
                }
            }
            
        }
        $retour = array();
        $retour['message'] = $this->message;
        $retour['is_ok'] = $isOk;

		return $retour;
	}





    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->message;
    }
}
