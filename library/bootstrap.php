<?php
if (!defined("DS")) {
	define("DS", DIRECTORY_SEPARATOR);
}
if (!defined("ROOT")) {
	die("Error: ROOT constant is not defined");
}
if (!defined("BLSFE_ROOT")) {
	define("BLSFE_ROOT", dirname(__FILE__) . "/../");
}

ini_set("allow_call_time_pass_reference", true);
ini_set("error_log", "syslog");

if (!defined("APPCONFIGFILE")) {
	$appConfigFile= ROOT . DS . 'config' . DS . 'config.php';
} else {
	$appConfigFile= APPCONFIGFILE;
}

if (!file_exists($appConfigFile)){
	die("Application config file does not exists. Please create it");
} else {
	require_once($appConfigFile);
}

$appRoutingFile = ROOT . DS . 'config' . DS . 'routing.php';
if (!file_exists($appRoutingFile)){
	// Use the defaults
	$default['controller'] = 'index';
	$default['action'] = 'index';
} else {
	require_once($appRoutingFile);
}


if (!defined("MODULESCONFIGFILE")) {
	$modulesConfigFile = ROOT . DS . 'config' . DS . 'modules.php';
} else {
	$modulesConfigFile = MODULESCONFIGFILE;
}

if (file_exists($modulesConfigFile)){
	require_once($modulesConfigFile);
	if (is_array($modulesConfig)){
		$default["modules"] = $modulesConfig;
	}
}

session_start();


function blsfe_local_exception_handler($exception) {
	if (isset($_ENV['DEVELOPMENT_ENVIRONMENT']) && $_ENV['DEVELOPMENT_ENVIRONMENT'] === true) {
  		echo "<pre>" . $exception . "</pre>";
	} else {
	  echo "<h1>Error: 500</h1><pre>";
	  echo "<font color=red>" .  $exception->getMessage() . "</font>\n";
	}
}
set_exception_handler('blsfe_local_exception_handler');

function blsfe_load_class($className) {
	$classPath = dirname(__FILE__)  .'/'.strtolower($className)  . '.class.php';
	if (file_exists($classPath))	{
		include_once($classPath);
		return true;
	}
	return false;
}


if (!function_exists("base36_encode")) {
	function base36_encode($base10){
    	return base_convert($base10,10,36);
	}
}

if (!function_exists("base36_decode")) {
	function base36_decode($base36){
	    return base_convert($base36,36,10);
	}
}

if (!function_exists("base16_encode")) {
	function base16_encode($base10){
    	return base_convert($base10,10,16);
	}
}

if (!function_exists("base16_decode")) {
	function base16_decode($base16){
	    return base_convert($base36,16,10);
	}
}

if (!function_exists("getConstantValue")) {
	function getConstantValue($name, $required=true){
		if (!defined($name)){
			throw new Exception("Cannot find a defined constant named: $name");
		}
		return constant($name);
	}
}

if (!function_exists("getSessionValue")) {
	function getSessionValue($name, $defaultValue=true){
		if (isset($_SESSION[$name])){
			return $_SESSION[$name];
		} else {
			return $defaultValue;
		}
	}
}

// Require the rest of the bootstrap logic
require_once (dirname(__FILE__)  . DS .  'shared.php');