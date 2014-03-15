<?php

// The rest does not need to be chnaged
ini_set("date.timezone", 'Europe/Berlin');

$_env_config_locations = ['/opt/allinora/env.conf.php', '/opt/ws_wbs/env.conf.php', __DIR__  . '/env.conf.php'];
foreach($_env_config_locations as $env_config_file){
	if (file_exists($env_config_file)){
		$_env = require_once($env_config_file);
		foreach ($_env as $key => $val){
			$_ENV[$key] = $val;
		}
		break;
	}
}

define ('APP_NAME', basename(dirname(__DIR__)));
/** Configuration Variables **/
define ('APP_NAME', "WBS-FileManager");
if (isset($_ENV['DEVELOPMENT_ENVIRONMENT'])) {
	define ('DEVELOPMENT_ENVIRONMENT', $_ENV['DEVELOPMENT_ENVIRONMENT']);
	if ($_ENV['DEVELOPMENT_ENVIRONMENT']) {
		ini_set("display_errors", 'On'); // Off for production and On for development
	}
} else {
	define ('DEVELOPMENT_ENVIRONMENT', FALSE);
}

define('BASE_URL','/');


# use this for production. The backend server shoud be on a different host / load balancer
# define('BLSEBE_TRANSPORT','http'); 
# define('BLSERVER_SHARED_KEY','s0m3p433');

//language related
define('LANGUAGES', 'en|de|fr');
define('TEMPLATE_BACKEND', "Smarty");

define('ROBOTS', false);

// Local TMP directory
define('TMP_DIRECTORY', '/var/tmp');
