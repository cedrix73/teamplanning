<?php


/**
 * Event
 * Classe gérant les types d'événements
 * CRUD
 * @author CV170C7N
 */
class Event {
    
    private $dbaccess;
    private $sql;
    
    public function __construct($dbaccess){
        $this->dbaccess = $dbaccess;
        $this->sql = '';
    }
    
    public function getSql(){
        return $this->sql;
    }
    
    public function getAll(){
        $sql = 'SELECT DISTINCT(id), affichage, libelle, couleur'.
            ' FROM evenement';
        $tabTypeEvent = array();
        $reponse = $this->dbaccess->execQuery($sql);
        $results=$this->dbaccess->fetchArray($reponse);
        
        foreach ($results as $ligne) {
            $id = intval($ligne['id']);
            $tabTypeEvent[$id]=array_map('utf8_encode', $ligne);
        }
        return $tabTypeEvent;
    }
    
    public function update($eventId, $eventCouleur, $eventAbbrev){
        $sql = 'UPDATE evenement set couleur  = \''.$eventCouleur.
                '\', affichage = \''.$eventAbbrev.'\''.
                ' WHERE id = '.$eventId;
        $this->sql = $sql.'<br>';
        try{
            $retour = $this->dbaccess->execQuery($sql);
        }catch(Exception $e){
            $retour = false;
        }
        return $retour;
    }
    
    public function create($tabInsert){
        $sqlData = 'VALUES (';
            // fonction "raccourci" qui effectue une simple reconversion d'une chaîne
        $sqlInsert = 'INSERT INTO evenement (';
        $i = 0;
        $max = count($tabInsert)-1;
        foreach($tabInsert as $key=>$value){
            $sqlInsert .= $key;
            $sqlData .= '\''.$value.'\'';
            if($i<$max){
                $sqlInsert .= ', ';
                $sqlData .= ', ';
            }else{
                $sqlInsert .= ') ';
                $sqlData .= ') ';
            }
            $i++;
        }
        
        $sql = $sqlInsert . $sqlData;
        $this->sql = ' ' . $sql;
            $retour = $this->dbaccess->execQuery($sql);
            if($retour === false) {
                
            }
      
        return $retour;
        
        
        $sqlData .= '('.$this->eventType . ','
               . $this->ressourceId . ',' 
               . '\''. $this->dateDebutSql . '\','
               . 0
               . ')';
            
    }
    
    
}
