<?php

/** Check if environment is development and display errors **/

function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL &  ~E_DEPRECATED & ~E_NOTICE);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
}

/** Takes care of setting the LANG cst **/
function setLanguage () {
	global $url,$lang;
	if (!defined("LANGUAGES")){
		return;
	}	
	i18nURL($url);
	if (!defined("LANG")){
		
		if ($lang){
			define("LANG", $lang);
			$date_constant="DATE_FORMAT_".strtoupper(LANG);
			if (defined($date_constant)){
			define("DATE_FORMAT", constant($date_constant));
			}
		} else {
			$lang="en";
			define("LANG", $lang);
		}
	}
}

/** Check for Magic Quotes and kill them **/
function stripSlashesDeep($value) {
	$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	return $value;
}

function removeMagicQuotes() {
if ( get_magic_quotes_gpc() ) {
	$_GET    = stripSlashesDeep($_GET   );
	$_POST   = stripSlashesDeep($_POST  );
	$_COOKIE = stripSlashesDeep($_COOKIE);
}
}

/** Check register globals and remove them **/
function unregisterGlobals() {
    if (ini_get('register_globals')) {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

/** Secondary Call Function **/
function performAction($controller,$action,$queryString = null,$render = 0) {
	
	$controllerName = ucfirst($controller).'Controller';
	$actionName=$action . "Action"; // This is done to avoid clash with reserved function names like list();
	$dispatch = new $controllerName($controller,$action);
	$dispatch->render = $render;
	$dispatch->$actionName($queryString);
	
	if ($dispatch->render >0) {
			$dispatch->display();
	}
	
}

/** Routing **/

function routeURL(&$url) {
	global $routing;

	if (!isset($routing) || !is_array($routing)){
		return;
	}
	//print "Routing<br><pre>" . print_r($routing, true) . "</pre>";
	foreach ( $routing as $pattern => $result ) {
		if ($pattern){
			//print "PAttern is $pattern, Result is $result<br>";
            if ( preg_match($pattern, $url,$m) ) {
				//print "MAtch: <pre>" . print_r($m, true) . "</pre>";
				$url=preg_replace( $pattern, $result, $url );
			}
		}
	}
	return ($url);
}

function i18nURL(&$url){
	global $lang;
	if (!defined('LANGUAGES')){
		return;
	}
	// Get the language from the url
	cleanURL($url);
	if (preg_match("@^(" . LANGUAGES . ")$@", $url, $x)){
		$lang=$x[1];
	} elseif (preg_match("@^(" . LANGUAGES . ")/@", $url, $x)){
		$lang=$x[1];
	}
	if (isset($lang)){
		$url=preg_replace("@^(" . LANGUAGES . ")/*@", "", $url);
	}
}
function cleanURL(&$url){
	// Do whatever need to clean the url..
	$url=preg_replace("@^/*@", "", $url);
	$url=preg_replace("@/*$@", "", $url);
	
}


function moduleHook(&$urlArray){
	//print "<pre>" . print_r($urlArray, true) . "</pre>";;
	// Load the module
	$module = $urlArray[0];
	array_shift($urlArray);

	$_controller = $urlArray[0]; // controller within module
	array_shift($urlArray);
	

	//print "controller is $controller";
	$module_controller_file=ROOT . DS . 'application' . DS . 'controllers' . DS . 'modules' . DS . strtolower($module) . DS .  strtolower($_controller) .'controller.php';
	if (file_exists($module_controller_file)){
		include_once($module_controller_file);
	} else {
		die("Module controller $module_controller_file does not exists");
	}
	
	// Create the controller class such as Modules_Gallery_List
	$controller="Modules_" . ucfirst(strtolower($module)) . "_" . ucfirst(strtolower($_controller)); 
	return $controller;
}
function coreHook(&$urlArray){
	//print "<pre>" . print_r($urlArray, true) . "</pre>";;
	// Load the core module
	$module = $urlArray[0];
	array_shift($urlArray);

	$_controller = $urlArray[0]; // controller within module
	array_shift($urlArray);
	

	// Load the core controller class
	include_once(BLSFE_ROOT . "/core/controller.php");
	
	//print "controller is $controller";
	$module_controller_file=BLSFE_ROOT . DS . 'core' . DS .  strtolower($module) . DS . "controllers" . DS .  strtolower($_controller) .'.php';
	$module_controller_index=BLSFE_ROOT . DS . 'core' . DS .  strtolower($module) . DS . "controllers" . DS .'index.php';
	if (file_exists($module_controller_file)){
		include_once($module_controller_file);
	} else {
		// Try the catchall/index controller
		if (file_exists($module_controller_index)){
			include_once($module_controller_index);
		} else {
			die("Module controller $module_controller_file does not exists<br>The index controller $module_controller_index also does not exists");
			
		}
		
	}
	
	// Create the controller class such as Modules_Gallery_List
	$controller="Core_" . ucfirst(strtolower($module)) . "_" . ucfirst(strtolower($_controller)); 
	return $controller;
}
/** Main Call Function **/

function callHook() {
	global $url;
	global $default;
	
	if(empty($url)){
		$url=$_SERVER["SCRIPT_NAME"];
	}
	if ($url=="/index.php" || $url=="/"){
		unset($url); 
	}
	$queryString = array();
	
	if (!isset($url) || $url == "") {
		$controller = $default['controller'];
		$action = $default['action'];
	} else {
		//print "URL is $url<br>";
		cleanURL($url); // Get rid of the junk..
		i18nURL($url); // Do the language stuff
		routeURL($url); // Do the routing
		$urlArray = array();
		
		$urlArray = explode("/",$url);
		
		//print "<pre>" . print_r($urlArray, true) . "</pre>";exit;
		$controller = $urlArray[0];
		array_shift($urlArray);
		
		if ($controller=="modules"){ // application modules
			$controller=moduleHook($urlArray);
		}
		if ($controller=="core"){  // Core modules bundled with the framework
			$controller=coreHook($urlArray);
		}
	
		if (isset($urlArray[0]) && !empty($urlArray[0])) {
			$action = $urlArray[0];
			array_shift($urlArray);
		} else {
			$action = 'index'; // Default Action
		}
		$queryString = $urlArray;
	}
	
	$controllerName = ucfirst($controller).'Controller';
	//print "Controller: $controllerName $action";exit;
	
	/* recursively handle cases and fallback nicely */
	_runControllerAction($controller, $action, $queryString, $controllerName, $action);
}

function _runControllerAction($controller, $action, $queryString){
	$controllerName = ucfirst($controller).'Controller';
	$actionName=$action . "Action"; // This is done to avoid clash with reserved function names like list();

	$dispatch = new $controllerName($controller,$action);
	
	//print "<pre>" . print_r($dispatch, true) . "</pre>";exit;

	// Call the init stuff
	call_user_func_array(array($dispatch, "beforeAction"), $queryString);

	// Dont care if the action does not exist. Let the __call handle it
	call_user_func_array(array($dispatch, $actionName), $queryString);

	// Call the cleanup stuff
	call_user_func_array(array($dispatch, "afterAction"), $queryString);
		
	if ($dispatch->render) {
		$_content=$dispatch->getContents();
		if (function_exists("_app_contentHook")){
			print _app_contentHook($_content);
		} else {
			print $_content;
		}
	}
}


/** Autoload any classes that are required **/

function __autoload($className) {
	// echo "autoload: $className<br/>";
	if (file_exists(BLSFE_ROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
		require_once(BLSFE_ROOT . DS . 'library' . DS . strtolower($className) . '.class.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php');
	} else if (file_exists(ROOT . DS . 'library'  . DS . strtolower($className) . '.php')) {
		require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.php');
	} else {
		/* Error Generation Code Here */
		//echo("<p><font color=red>Could not autoload class file for \"$className\".</font></p>\n");
		throw new AutoloadClassException($className);
	}
}

/** CallHook exceptions **/
class AutoloadClassException extends Exception { }
class ActionFailedException extends Exception { }


/** GZip Output **/

function gzipOutput() {
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
        || false !== strpos($ua, 'Opera')) {
        return false;
    }

    $version = (float)substr($ua, 30); 
    return (
        $version < 6
        || ($version == 6  && false === strpos($ua, 'SV1'))
    );
}

/** Get Required Files **/

gzipOutput() || ob_start("ob_gzhandler");


include_once(dirname(__FILE__) . "/cache.class.php");
include_once(dirname(__FILE__) . "/session.class.php");
include_once(dirname(__FILE__) . "/blcontroller.class.php");

$cache   = Cache::factory();
$session = Session::factory();
session_start();

list($url, $params)=split('\?', $_SERVER["REQUEST_URI"]);   // Just get everything before t
setReporting();
removeMagicQuotes();
unregisterGlobals();
setLanguage();

if (function_exists("_app_preHook")){
	_app_preHook();
}

callHook();

?>
