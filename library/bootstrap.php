<?php
if (!defined("DS")) {
	define("DS", DIRECTORY_SEPARATOR);
}
if (!defined("ROOT")) {
	die("Error: ROOT constant is not defined");
}

$appConfigFile= ROOT . DS . 'config' . DS . 'config.php';
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


// Require the rest of the bootstrap logic
require_once (__DIR__ . DS .  'shared.php');


