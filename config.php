<?php
# --- Team_Planning Config file ---

$docker = true;

$serverAdress = $_SERVER['SERVER_ADDR']=='::1' ?  '127.0.0.1' : $_SERVER['SERVER_ADDR'];
$host = ($docker == true) ? "database" : $serverAdress;
define('TEAM_DATABASE_SERVER', $host);
define('TEAM_DATABASE_USER' , 'root');
define('TEAM_DATABASE_PASSWORD', 'cedrix');
define('TEAM_DATABASE_NAME', 'team_planning');
$bdObj = null;
$usedDb = 'mysql';
$isAdmin = true;




// Database hostname (usually "localhost")
define('M_DBHOST', TEAM_DATABASE_SERVER);

// Database user
define('M_DBUSER', TEAM_DATABASE_USER);

// Database password
define('M_DBPASSWORD', TEAM_DATABASE_PASSWORD);

// Database name
define('M_DBNAME', TEAM_DATABASE_NAME);



// Detail des mesage d'erreurs: A mettre sur false en PROD
define('M_LOG', true);

// Path for error log
define('M_TMP_DIR','/tmp/errors.log');

// FirePHP (false or true)
define('M_FIREPHP',false);

define('ABS_ROOT_PATH', realpath(dirname(__FILE__)));
define('ABS_MODULES_PATH', ABS_ROOT_PATH . '/modules/');
define('ABS_CLASSES_PATH', ABS_ROOT_PATH . '/classes/');
define('ABS_SCRIPTS_PATH', ABS_ROOT_PATH . '/js/');
define('ABS_GENERAL_PATH', ABS_ROOT_PATH . '/general/');
define('ABS_DATA_PATH', ABS_ROOT_PATH . '/data/');
define('ABS_STYLES_PATH', ABS_ROOT_PATH . '/styles/');
define('ABS_IMAGES_PATH', ABS_ROOT_PATH . '/styles/img/');
define('ABS_PLANNING_PATH', ABS_MODULES_PATH . 'planning/');

define('APPLI_PATH', '/planning/');
define('ROOT_PATH', $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']. APPLI_PATH);
define('CLASSES_PATH',  'classes/');
define('SCRIPTS_PATH',  'js/');
define('GENERAL_PATH',  'general/');
define('DATA_PATH',  'data/');
define('STYLES_PATH',  'styles/');
define('IMAGES_PATH',  'styles/img/');
define('MAIN_IMAGES_PATH',  '../../styles/img/');

// Database preset
$dbObj = null;
switch($usedDb){
    case 'mysql':
        define('TEAM_DATABASE_DRIVER', 'mysql');
        define('TEAM_DATABASE_PORT', 3306);
        $dbFile = 'DbPdo.php';
        include_once ABS_CLASSES_PATH.$dbFile;
        $dbObj = new DbPdo();
     break;
 
    case 'postgresql':
        define('TEAM_DATABASE_DRIVER', 'pdo_pgsql');
        define('TEAM_DATABASE_PORT', 5432);
        $dbFile = 'DbPostGresql.php';
        include_once ABS_CLASSES_PATH.$dbFile;
        $dbObj = new DbPostGresql();
     break;
 
    default:
        // mySqli
        define('TEAM_DATABASE_DRIVER', 'mysqli');
        define('TEAM_DATABASE_PORT', 5432);
        $dbFile = 'DbMysqli.php';
        include_once ABS_CLASSES_PATH.$dbFile;
        $dbObj = new DbMySqli();
    break;
}
// Database driver (mysql, pgsql)
define('M_DBDRIVER', TEAM_DATABASE_DRIVER);
define('M_DBPORT', TEAM_DATABASE_PORT);


// Dates
define('DATE_FORMAT', 'd/m/Y'); // voir aussi la fonction getDate() de /public/js/main.js pour le côté client
define('DB_DATE_FORMAT', 'Y-m-d'); // MySQL
define('DB_DATETIME_FORMAT', 'Y-m-d H:i:s'); // MySQL
// Erreurs
define('DB_NOPRESENT_ERROR', 'La base de données team_planning\'existe pas ou n\'est pas visible par le serveur Web');
define('TABLE_NOPRESENT_ERROR', 'La base de données team_planning n\'existe pas ou n\'est pas visible par serveur Web');


define('PREF_FAMILLE', 'GOUV');

$tabPeriode[1] =  'journée';
$tabPeriode[2] = 'matin';
$tabPeriode[3] = 'a-m';


/*
echo $_SERVER['SERVER_NAME'].'</br>';
echo $_SERVER['SERVER_PORT'].'</br>';
echo $_SERVER['PHP_SELF'].'</br>';
*/
?>
