<?php	
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));


// Possible location where we can find the BLSFE library
$blsfe_locations=array(
        '/opt/git/blsfe',
        "/Users/aghaffar/www/blsfe",
        "/home/tipi/blsfe",
		"/usr/local/blsfe",
		"/www/blsfe"
);

$_blsfe_bootstrap_file=_findBootstrap($blsfe_locations) . "/library/bootstrap.php";



// Do something before the main Hook is called
function _app_preHook(){
	if (in_array($_REQUEST["op"], array("fblogin", "fbsignup2"))){
    include(dirname(__FILE__) . "/login.php");
    exit;
    }
    if ($_SESSION["fb_user_data"]){
            $_SESSION["loginFB"]=($_SESSION["loginFB"])?++$_SESSION["loginFB"]:1;
    }
    
}


// Do something with the content just before its being printed.
function _app_contentHook($_content){
	blsfe_load_class("BLTranslate");
	$translator=new BLTranslate(LANG, "ProjectName");


	// Do something to the content before printinga it out.
	$data=preg_replace_callback("@<po>([^<]*)?</po>@", array($translator, "translate"), $_content);
	print $data;
}



function _findBootstrap($blsfe_locations){
	foreach($blsfe_locations as $l){
		if (file_exists($l)){
			return $l;
	    }
	}
	
}

if (isset($_SERVER["BLSFE_BOOTSTRAP_FILE"])){
        // This is for development. 
        if (file_exists($_SERVER["BLSFE_BOOTSTRAP_FILE"])){
                require_once($_SERVER["BLSFE_BOOTSTRAP_FILE"]);
        }
} else {
        if (file_exists($_blsfe_bootstrap_file)){
                require_once($_blsfe_bootstrap_file);
        }
}

