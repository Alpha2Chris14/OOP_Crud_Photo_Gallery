<?php 


//Define The Core Paths
//Define Them As Absolute Paths To Make Sure require_once work as expected

//DIRECTORY_SEPERATOR is a PHP re-defined constant
// (\ for windows, / for unix)

defined('DS') ? null :define('DS',DIRECTORY_SEPARATOR);
defined('SITE_ROOT') ? null : define('SITE_ROOT','C:'.DS.'wamp64'.DS.'www'.DS.'photo_gallery');
defined('LIB_PATH') ? null : define('LIB_PATH',''.SITE_ROOT.DS.'include');

//loads config file first
require_once(LIB_PATH.DS.'config.php');

//loads basic function next so that everything after can use them
require_once(LIB_PATH.DS.'function.php');

//load core objects
require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'database_object.php');
require_once(LIB_PATH.DS.'pagination.php');

//load database related classes
require_once(LIB_PATH.DS.'user.php');
require_once(LIB_PATH.DS.'photograph.php');


?>