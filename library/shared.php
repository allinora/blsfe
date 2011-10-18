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
	global $url;
	if (!defined("LANGUAGES")){
		return;
	}	
	$languages = explode("|", LANGUAGES);
	$lang = $languages[0]; //fallback to default if none specified
	
	//let's pick the lang token from the url first, if any
	$slash = strpos($url, "/");
	$slash = $slash === false ? -1 : $slash;
	$t1 = $slash < 0 ? $url : substr($url, 0, $slash);
	if (in_array($t1, $languages)) {
		$lang = $t1;
		$url = substr($url, strlen($lang)+1);
	}
	
	define("LANG", $lang);
	define("DATE_FORMAT", constant("DATE_FORMAT_".strtoupper(LANG)));
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

function routeURL($url) {
	global $routing;

	if (!isset($routing) || !is_array($routing)){
		return $url;
	}
	foreach ( $routing as $pattern => $result ) {
            if ( preg_match( $pattern, $url ) ) {
				return preg_replace( $pattern, $result, $url );
			}
	}
	return ($url);
}

function i18nURL(&$url){
	if (!defined('LANGUAGES')){
		return;
	}
	// Get the language from the url
	
	if (preg_match("@^(" . LANGUAGES . ")$@", $url, $x)){
		$lang=$x[0];
	} elseif (preg_match("@^(" . LANGUAGES . ")/@", $url, $x)){
		$lang=$x[0];
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


/** Main Call Function **/

function callHook() {
	global $url;
	global $default;
	
	list($url, $params)=split('\?', $_SERVER["REQUEST_URI"]);   // Just get everything before t
	

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
		//print "URL is $url<br>";
		i18nURL($url);
		$url = routeURL($url);
		$urlArray = array();
		
		$urlArray = explode("/",$url);
		
		//print "<pre>" . print_r($urlArray, true) . "</pre>";exit;
		$controller = $urlArray[0];
		array_shift($urlArray);
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
	//print "<pre>" . print_r($queryString, true) . "</pre>";exit;
	if (method_exists($controllerName, $actionName)) {
		call_user_func_array(array($dispatch, "beforeAction"), $queryString);
		
		call_user_func_array(array($dispatch, $actionName), $queryString);
		
		call_user_func_array(array($dispatch, "afterAction"), $queryString);
		
		if ($dispatch->render) {
			$dispatch->display();
		}
	}
}


/** Autoload any classes that are required **/

function __autoload($className) {
	// echo "autoload: $className<br/>";
	if (file_exists(BLSFEROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
		require_once(BLSFEROOT . DS . 'library' . DS . strtolower($className) . '.class.php');
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

// gzipOutput() || ob_start("ob_gzhandler");


include_once(dirname(__FILE__) . "/cache.class.php");
include_once(dirname(__FILE__) . "/session.class.php");
include_once(dirname(__FILE__) . "/blcontroller.class.php");

$cache   = Cache::factory();
$session = Session::factory();
session_start();

setReporting();
removeMagicQuotes();
unregisterGlobals();
setLanguage();

callHook();

?>
