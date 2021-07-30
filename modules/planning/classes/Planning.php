<?php
require_once ABS_CLASSES_PATH . 'CvfDate.php';

/**
 * Planning
 * Classe gérant les entrées dans le planning
 * avec les données suivantes:
 * date, num ressource, type d'événement
 * CRUD
 * @author Cédric Von Felten
 */
class Planning {
    private $ressourceId;
    private $eventType;
    private $eventId;
    private $dateDebut;
    private $dateFin;
    private $dbaccess;
    private $dateDebutSql;
    private $dateFinSql;
    private $sql;
    private $dateDebutTxt;
    private $dateFinTxt;
    private $isSingle;
    private $periode;
    private $oldDateDebut;
    private $oldDateFin;
    private $oldDateDebutSql;
    private $oldDateFinSql;
        
    
    public function __construct($dbaccess, $ressourceId, $eventType, $dateDebut, $dateFin, $periode=1) {
        $this->dbaccess = $dbaccess;
        $this->dateSql =  false;
        $this->ressourceId = $ressourceId;
        $this->eventType = $eventType;
        $this->dateDebutTxt = $dateDebut;
        $this->dateFinTxt = $dateFin;
        $this->dateDebut = new CvfDate($dateDebut);
        $this->dateFin = new CvfDate($dateFin);
        $this->periode = $periode;
        $this->dateDebutSql = $this->dateDebut->tspToSql();
        $this->dateFinSql = $this->dateFin->tspToSql();
        $this->isSingle = true;
        $this->checkSingle();
        $this->sql = '';
        $this->oldDateDebut = null;
        $this->oldDateFin = null;
        $this->oldDateDebutSql = null;
        $this->oldDateFinSql = null;
    }
    
    public function checkSingle(){
        if($this->dateDebut < $this->dateFin){
            $this->isSingle = false;
        }
    }
    
    public function getSql(){
        return $this->sql;
    }
    
    public function getRessource_id() {
        return $this->ressource_id;
    }

    public function getEventType() {
        return $this->eventType;
    }

    public function getEventId() {
        return $this->eventId;
    }

    public function getDateDebut() {
        return $this->dateDebut;
    }

    public function getDateFin() {
        return $this->dateFin;
    }
    
    public function getDateDebutSql() {
        return $this->dateDebutSql;
    }
    
    public function setRessource_id($ressourceId) {
        $this->ressource_id = $ressourceId;
    }

    public function setEventType($eventType) {
        $this->eventType = $eventType;
    }

    public function setEventId($eventId) {
        $this->eventId = $eventId;
    }

    public function setDateDebut($dateDebut) {
        $this->dateDebut = $dateDebut;
    }

    public function setDateFin($dateFin) {
        $this->dateFin = $dateFin;
    }
    
    public function getIsSingle() {
        return $this->isSingle;
    }

    public function setOldDateDebut($oldDateDebut) {
        $this->oldDateDebut = new CvfDate($oldDateDebut);
        $this->oldDateDebutSql = $this->oldDateDebut->tspToSql();
    }

    public function setOldDateFin($oldDateFin) {
        $this->oldDateFin = new CvfDate($oldDateFin);
        $this->oldDateFinSql = $this->oldDateFin->tspToSql();
    }

        
    public function create(){
        $retour = FALSE;
        if($this->dateDebut > $this->dateFin){
            $retour = FALSE;
        }else{
            $sqlData = 'VALUES ';
            // fonction "raccourci" qui efectue une simple reconversion d'une chaine
            $sqlInsert = 'INSERT INTO planning (event, '
                       . 'ressource, '
                       . 'jour, '
                       . 'periode) ';

            if($this->isSingle){
            $sqlData .= '('.$this->eventType . ','
                   . $this->ressourceId . ',' 
                   . '\''. $this->dateDebutSql . '\','
                   . $this->periode
                   . ')';
            }else{
                $pointeurJour = new CvfDate($this->dateDebutTxt);
                $tabFeries = CvfDate::getFeries($pointeurJour->annee());
                $diffJours = $this->dateDebut->nbJourEcart($this->dateFin);
                $tabDatas = array();
                for($j=0; $j<$diffJours+1; $j++){
                    // les w-e et les jours feriés ne sont pas pris en compte
                    if(!($pointeurJour->isWeekEnd() || isset($tabFeries[$pointeurJour->getTsp()]))){
                        $tabDatas[] = '('.$this->eventType . ','
                       . $this->ressourceId . ',' 
                       . '\''. $pointeurJour->tspToSql() . '\','
                       . $this->periode
                       . ')';
                        
                    }
                   $pointeurJour->incJours();
                }
                $sqlData .= implode(",", $tabDatas);
            }
            $sql = $sqlInsert . $sqlData;
            $this->sql .= $sql.'<br>';
            $retour = $this->dbaccess->execQuery($sql);
            
           
        }
        return $retour;
    }
    
    public function read(){
        $tabPlanning = array();
        $retour = false;
        $results = false;
        $sql = 'SELECT event, jour ' .
                ' FROM planning ' .
                ' WHERE ressource = ' .$this->ressourceId;
        if(!$this->isSingle){
            $sql.= ' AND jour BETWEEN \'' . $this->dateDebutSql . '\'' .
                   ' AND \'' . $this->dateFinSql . '\'';
        }else{
            $sql.= ' AND jour = \'' . $this->dateDebutSql . '\'';
        }
        try{
            $rs = $this->dbaccess->execQuery($sql);
            $results=$this->dbaccess->fetchArray($rs);
            $retour = true;
        }catch(Exception $e){
            $retour = false;
        }
        if($retour !== false) {
            foreach ($results as $ligne) {
                $tabPlanning[]=$ligne;
            }
        } else {
            $tabPlanning = false;
        }
        return $tabPlanning;
    }
    
    public function delete(){
        $retour = false;
       $sql = 'DELETE FROM planning ' .
                ' WHERE ressource = ' .$this->ressourceId;
        if(!$this->isSingle){
            $sql.= ' AND jour BETWEEN \'' . $this->dateDebutSql . '\'' .
                   ' AND \'' . $this->dateFinSql . '\'';
        }else{
            $sql.= ' AND jour = \'' . $this->dateDebutSql . '\'';
        }
        
        $retour = $this->dbaccess->execQuery($sql);

        $this->sql .= $sql.'<br>';
        return $retour;
    }
    
    public function update(){
       $retour = FALSE;
       $sql = 'UPDATE planning ' .
              ' SET event = ' .$this->eventType . ','
              . ' jour = \'' . $this->dateDebutSql . '\','
              . ' periode = ' .$this->periode 
              . ' WHERE ressource = ' .$this->ressourceId;
        if(!$this->isSingle){
            $sql .= ' AND jour BETWEEN \'' . $this->oldDateDebutSql . '\''
                  . ' AND \'' . $this->oldDateFinSql . '\'';
        }else{
            $sql .= ' AND jour = \'' . $this->oldDateDebutSql . '\'';
        }

        $retour = $this->dbaccess->execQuery($sql);
        $this->sql .= $sql.'<br>';
        return $retour;
    }
    
    
    public function getActivites(){
        $tabActivites = array();
        $sql = 'SELECT DISTINCT(ressource), '
                . 'jour, '
                . 'event, '
                . 'periode'
                .' FROM planning'
                .' WHERE jour BETWEEN \''.$this->dateDebutSql .'\''
                .' AND \''.$this->dateFinSql.'\'';
        $this->sql = $sql.'<br>';
        
        $reponse = $this->dbaccess->execQuery($sql);
        $results=$this->dbaccess->fetchArray($reponse);
        
        foreach ($results as $ligne) {
            $ressource = ($ligne['ressource']);
            $ligne['date'] = $ligne['jour'];
            $ligne['journee'] = CvfDate::sqlToTspStatic($ligne['jour']);
            $tabActivites[$ressource][$ligne['journee']]['type'] = $ligne['event'];
            $tabActivites[$ressource][$ligne['journee']]['periode'] = $ligne['periode'];
        }
        return $tabActivites;
    }
    
    /*
     * SELECT DISTINCT (ressource), jour, event_affichage, event_couleur
        FROM planning, event 
        WHERE event = event_id
     */

}
