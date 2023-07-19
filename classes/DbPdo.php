<?php

require_once ABS_CLASSES_PATH.'DbInterface.php';

/**
 * @name DbPdp
 * @author cvonfelten
 * Classe gérant le driver Pdo et interface les méthodes de DBInterface
 */

class DbPdo implements DbInterface 
{

	protected $_log;
	protected $_stmt;
	protected $_eMessage;


    public function setLog($bln) {
        $this->_log = $bln;
    }

    public function getLog() {
        return $this->_log;
	}

	/**
	 * Retourne un message d'erreur (si le resultat retourné est false).
	 * @return String $this->_eMessage
	 */
	public function getErrorMessage() {
		return $this->_eMessage;
	}


	
	
    /**
     * Etablit une connexion à un serveur de base de données et retourne un identifiant de connexion
     * L'identifiant est positif en cas de succès, FALSE sinon.
     * On pourrait se connecter avec un utilisateur lambda
     */
	public function connect($conInfos, $log = false)
	{
		$host = $conInfos['host'];
		$dbname = $conInfos['dbase'];
		$dbh=$dsn='';
		$this->setlog($log);
		$this->_stmt = false; 
		$this->_eMessage = false;
		try {
			$dsn = "mysql:host=$host;dbname=$dbname";
			$dbh = new PDO($dsn, $conInfos['username'], $conInfos['password']);
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if($dbh===false) {
				throw new PDOException("La connexion à la base de données mySql a échoué");
			}
		} catch (PDOException $e) {
			$this->_eMessage = $e->getMessage();
		}
		return $dbh;
	}
	



	/**
	 * @name: execQuery
	 * @description: Execute la requete SQL $query et renvoie  le resultSet
	 * pour être interprétée ultérieurement par fetchRow ou fetchArray.
	 * 
	 * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL
	 * @return array $resultSet : resultat de l'execution
	 */
	public function execQuery($link, $query) {
		$resultSet = false;
		try {
			$resultSet = $link->query($query);
			if($link===false) {
				throw new PDOException("La requête à la base de données mySql a échoué");
			}
		
		} catch (PDOException $e) {
			$this->_eMessage = $e->getMessage();
			$resultSet = false;
		}
		return $resultSet;
		
	}

	/**
	 * @name: execPreparedQuery
	 * @description: il s'agit d'un prpared Statement: Prépare et execute 
	 * la requete SQL $query et renvoie  le resultSet pour être interprétée 
	 * ultérieurement par fetchRow ou fetchArray. Si on passe des arguments 
	 * dans la requête, ils doivent être passés dans le tableau clé-valeur 
	 * $args avec comme format ":nomDeLaVariable" => valeurDeLaVariable.
	 * Important ! La requête doit être de la forme :
	 * '.. WHERE author.last_name = :prenom AND author.name = :nom'
	 * 
	 * @param ressource $link: instance renvoiée lors de la connexion PDO.
	 * @param string $query: chaine SQL 
	 * @param string arg: champ à rechercher
	 * @param boolean $again: Si true, le même statement est réexecuté avec de
	 *                de nouveaux arguments; $query peut être vide.
	 * @return mixed $stmt : retourne le statement de la requête.
	 */
	public function execPreparedQuery($link, $query, $args=null, $again = false) {
		if(!$again) {
			$this->_stmt = false;
		}
		try {
			if($again || $this->_stmt = $link->prepare($query)){
				
				$resultSet = $this->_stmt->execute([$args]);
				if($resultSet===false) {
					throw new PDOException("La requête à la base de données mySql a échoué");
				}
			}
		} catch (PDOException $e) {
			$this->_eMessage = "Problème lors de l\'execution de la requête: " . $e->getMessage();
			$this->_stmt = false;
			
		}
		return $this->_stmt;
	}




	/**
	 * @name:          numRows
	 * @description:   Retourne le nombre de lignes qui sera retournées ultérieurement par
	 *                 fetchRow ou fetchArray.
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function numRows($resultSet) {
		return $resultSet->rowCount();
	}

	/**
	 * @name:          fetchRow
	 * @description:   Retourne un tableau énuméré clé-valeur  dont les indexes de clé sont numériques 
	 *                 et correspondent dans l'ordre des colonnes spécifiées en clause SELECT.
	 *                 Retourne FALSE s'il n'existe pas de résultat.
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchRow($resultSet) 
	{
		$results = false;
		try {
			$results = $resultSet->fetchAll(PDO::FETCH_NUM);
			if($results===false) {
				throw new PDOException("Problème lors du traitement du résultat de la requête " 
				. " en tableau numérique:");
			}
		} catch (PDOException $e) {
				$this->_eMessage = $e->getMessage();	
		}
		return $results;
	}
	
	/**
	 * @name:          fetchArray
	 * @description:   Retourne un tableau associatif dont la clé correspond aux nom colonnes 
	 *                 spécifiées en clause SELECT. Retourne FALSE s'il n'existe pas de résultat. 
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchArray($resultSet) 
	{
		$results = false;
		try {
			$results = $resultSet->fetchAll(PDO::FETCH_ASSOC);
			if($results === false) {
				throw new PDOException("Problème lors du traitement du résultat de la requête " 
				. " en tableau associatif:");
			}
		} catch (PDOException $e) {
				$this->_eMessage = $e->getMessage();
		}

		return $results;
	}

	/**
	 * @name:          fetchAssoc
	 * @description:   Retourne une ligne de résultat sous forme de tableau associatif 
	 *                 dont la clé correspond aux nom colonnes spécifiées en clause SELECT. 
	 * 			       Retourne FALSE s'il n'existe pas de résultat. 
	 * @param          array $resultSet: resultat de l'execution de la requête soit par execQuery(), 
	 *                 soit par execPreparedQuery.
	 */
	public function fetchAssoc($resultSet) 
	{
		$results = false;
		try {
			$results = $resultSet->fetch(PDO::FETCH_ASSOC);
			if($results === false) {
				throw new PDOException("Problème lors du traitement du résultat de la requête " 
				. " en tableau associatif:");
			}
		} catch (PDOException $e) {
			$this->_eMessage = $e->getMessage();
		}
			
		return $results;
	}
	
	public function escapeString($link, $arg)
	{
		return $link->quote($arg);
	}

	/**
     * @name GetTableDatas
     * @description Retourne un tableau des champs d'une table:
     * nom du champ, type du champ, is_nullable 
	 * @param String $link : pointeur de ressource de la base de données
     * @param String $query :Requête SQL émanant de DBAccess:GetTableDatas
     */
	public function getTableDatas($link, $query)
	{
		return $this->execQuery($link, $query);
	}

	/**
      * GetTableFields
      * @description Retourne un tableau des noms de champs d'une table 
      * @param String $link : pointeur de ressource de la base de données
      * @param String $query :Requête SQL émanant de DBAccess:GetTableFields
      */
	public function getTableFields($link, $query)
	{
		return $this->execQuery($link, $query);
	}
}

	

?>
