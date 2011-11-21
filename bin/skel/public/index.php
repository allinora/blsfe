<?php	
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));


// Possible location where we can find the BLSFE library
$blsfe_locations=array(
        '/opt/git/blsfe',
        "/Users/aghaffar/www/blsfe",
        "/home/tipi/blsfe",
	"/usr/local/blsfe"
);

$_blsfe_bootstrap_file=_findBootstrap($blsfe_locations) . "/library/bootstrap.php";



// Do something before the main Hook is called
function _app_preHook(){
}


// Do something with the content just before its being printed.
function _app_contentHook($_content){
        print $_content;
}



function _findBootstrap($blsfe_locations){
	foreach($blsfe_locations as $l){
		if (file_exists($l)){
			return $l;
	    }
	}
	
}

if ($_SERVER["BLSFE_BOOTSTRAP_FILE"]){
        // This is for development. 
        if (file_exists($_SERVER["BLSFE_BOOTSTRAP_FILE"])){
                require_once($_SERVER["BLSFE_BOOTSTRAP_FILE"]);
        }
} else {
        if (file_exists($_blsfe_bootstrap_file)){
                require_once($_blsfe_bootstrap_file);
        }
}

