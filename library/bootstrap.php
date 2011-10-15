<?php
if (!defined("DS")) {
	define("DS", DIRECTORY_SEPARATOR);
}
if (!defined("ROOT")) {
	die("Error: ROOT constant is not defined");
}
if (!defined("BLSFE_ROOT")) {
	define("BLSFE_ROOT", dirname(__FILE__));
}

ini_set("allow_call_time_pass_reference", true);
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

$appRoutingFile=ROOT . DS . 'config' . DS . 'routing.php';
if (!file_exists($appRoutingFile)){
	// Use the defaults
	$default['controller'] = 'index';
	$default['action'] = 'index';
} else {
	require_once($appRoutingFile);
}


function blsfe_local_exception_handler($exception) {
  echo "Uncaught exception: <font color=red>" , $exception->getMessage(), "</font>\n";
	if (defined("DEVELOPMENT_ENVIRONMENT") && DEVELOPMENT_ENVIRONMENT === true) {
  		echo "<pre>" . $exception. "</pre>";
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



// Require the rest of the bootstrap logic
require_once (dirname(__FILE__)  . DS .  'shared.php');


