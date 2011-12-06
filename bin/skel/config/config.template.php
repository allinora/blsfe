<?php
ini_set("date.timezone", 'Europe/Berlin');
ini_set("display_errors", 'Off'); // Off for production and On for development

/** Configuration Variables **/

define ('APP_NAME', "YourAppName");
define ('DEVELOPMENT_ENVIRONMENT',true);

define('BASE_URL','/%%appname%%');

# use this for development. Makes a direct connections to the BLS backend
define('BLSEBE_TRANSPORT','local'); 
define('BLSBE_PROJECT_HOME','/some/path/to/your/appbls'); 
define('BLSBE_BOOTSTRAP_FILE','/usr/local/allinora/blsbe/library/bootstrap.php'); 

# use this for production. The backend server shoud be on a different host / load balancer
# define('BLSEBE_TRANSPORT','http'); 
# define('BLSERVER_URL','http://someserver:9090');
# define('BLSERVER_SHARED_KEY','s0m3p433');


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


//smarty options, defaults are ok

// Path to Smarty.class.php
// define('SMARTY_LIBRARY', ROOT.DS."library".DS."3rdparty".DS."smarty".DS."Smarty-3.0.8".DS."libs".DS."Smarty.class.php");

// Default template directory 
// define('SMARTY_TEMPLATE_DIR', ROOT.DS."application".DS."views");


// define('SMARTY_COMPILE_DIR', ROOT.DS."tmp".DS."smarty_compile");
// define('SMARTY_CACHE_DIR', ROOT.DS."tmp".DS."smarty_cache");

// YOUR smarty plugins directory. This will be added to the list of smarty plugins
// define('SMARTY_LOCAL_PLUGINS_DIR', ROOT.DS."library".DS."smarty-plugins");

// left and right delimeters for smarty tags. we recommend <{ $something }>
// define('SMARTY_LEFT_DELIMETER', "<{");
// define('SMARTY_RIGHT_DELIMETER', "}>");


//stuff related to tipiness v4->v5
define('MEDIA_JS_PREFIX', "/staticmedia");
define('MEDIA_CSS_PREFIX', "/staticmedia");
define('ROBOTS', false);
