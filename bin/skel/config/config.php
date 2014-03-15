<?php
define ('APP_NAME', basename(dirname(__DIR__)));

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
// Almost everything should be defined in env.conf.php
// No need to defined constants

//language related
define('LANGUAGES', 'en|de|fr');
define('TEMPLATE_BACKEND', "Smarty");

define('ROBOTS', false);

// Local TMP directory
define('TMP_DIRECTORY', '/var/tmp');
