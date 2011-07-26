<?php
ini_set("date.timezone", 'Europe/Berlin');

/** Configuration Variables **/

define ('DEVELOPMENT_ENVIRONMENT',true);

define('BASE_URL','/%%appname%%');

define('BLSERVER_URL','http://localhost/tipibls');
define('BLSERVER_SHARED_KEY','s0m3p433');

define('CACHE_BACKEND', 'FILE');
define('CACHE_PATH', ROOT . DS . "tmp" . DS . "cache");

define('SESSION_BACKEND', 'FILE');
define('SESSION_PATH', ROOT . DS . "tmp" . DS . "sessions");

//language related
define('LANGUAGES', 'en|de|fr');
define('DATE_FORMAT_EN', "n-j-Y, H:i:s");
define('DATE_FORMAT_FR', "j/n/Y, H:i:s");
define('DATE_FORMAT_DE', "j/n/Y, H:i:s");

define('TEMPLATE_BACKEND', "Smarty");


//smarty options

// Path to Smarty.class.php
define('SMARTY_LIBRARY', ROOT.DS."library".DS."3rdparty".DS."smarty".DS."Smarty-3.0.8".DS."libs".DS."Smarty.class.php");

// Default template directory 
define('SMARTY_TEMPLATE_DIR', ROOT.DS."application".DS."views");


define('SMARTY_COMPILE_DIR', ROOT.DS."tmp".DS."smarty_compile");
define('SMARTY_CACHE_DIR', ROOT.DS."tmp".DS."smarty_cache");

// YOUR smarty plugins directory. This will be added to the list of smarty plugins
define('SMARTY_PLUGINS_DIR', ROOT.DS."library".DS."smarty-plugins");

// left and right delimeters for smarty tags. we recommend <{ $something }>
define('SMARTY_LEFT_DELIMETER', "<{");
define('SMARTY_RIGHT_DELIMETER', "}>");


//stuff related to tipiness v4->v5
define('MEDIA_JS_PREFIX', "/staticmedia");
define('MEDIA_CSS_PREFIX', "/staticmedia");
define('ROBOTS', false);
