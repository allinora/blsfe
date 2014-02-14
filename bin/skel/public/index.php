<?php	
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
ini_set("display_errors", "On");
ini_set("display_startup_errors", "On");
ini_set("html_errors", "On");

ini_set("zlib.output_compression", "Off");
date_default_timezone_set("Europe/Zurich");


define("CACHE_DISABLED", false);



function _app_contentHook($_content){
        blsfe_load_class("BLTranslate");
        $translator = new BLTranslate(LANG, "wbs_widgets");
        // Do something to the content before printing it out.
        $data = preg_replace_callback("@<po>([^<]*)?</po>@", array($translator, "translate"), $_content);

		if (!DEVELOPMENT_ENVIRONMENT){
			// Rewrite all /fwassets url to the static server	
		 	$static_server = (defined('CMS_STATIC_URL')) ? CMS_STATIC_URL : '//static.worldsoft-wbs.com';
			$data = preg_replace("@\"/fwassets/@", '"' . $static_server . '/fwassets/', $data);
		}
        print $data;
}


$_blsfe_bootstrap_file='/opt/allinora/blsfe//library/bootstrap.php';
require_once($_blsfe_bootstrap_file);

