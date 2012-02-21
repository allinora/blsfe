<?php
$packagesConfig=array();

$packagesConfig["jquery-ui"]=array(
	'css' => array("/fwassets/css/redmond/jquery-ui-1.8.16.custom.css"),
	'js' => array("/fwassets/js/jquery/ui/1.8.16/jquery-ui.min.js"),
	'head' => array()
);

$packagesConfig["table-sorter"]=array(
	'css' => array("/fwassets/js/jquery/tableSorter/style.css"),
	'js' => array("/fwassets/js/jquery/metadata/jquery.metadata.js", "/fwassets/js/jquery/tableSorter/jquery.tablesorter.min.js"),
	'head' => array()
);

$packagesConfig["jquery-ui-timepicker"]=array(
	'css' => array("/fwassets/js/jquery/ui/addons/jquery-ui-timepicker-addon.css"),
	'js' => array("/fwassets/js/jquery/ui/addons/jquery-ui-timepicker-addon.js"),
	'head' => array()
);

$packagesConfig["lesscss"]=array(
	'js' => array("/fwassets/js/lesscss/less-1.1.5.min.js")
);

$packagesConfig["core"]=array(
	'css' => array('/fwassets/960.gs/code/css/reset.css', '/fwassets/960.gs/code/css/text.css'),
	'js' => array("/fwassets/js/core/console.js"),
	'head' => array()
);

$packagesConfig["jquery-1.7.1"]=array(
	'css' => array(),
	'js' => array("/fwassets/js/jquery/1.7.1/jquery.min.js"),
	'head' => array()
);

// alias to the latest jquery
$packagesConfig["jquery"]=$packagesConfig["jquery-1.7.1"];

$packagesConfig["jquery-cookie"]=array(
	'js' => array("/fwassets/js/jquery/cookie.js")
);





$packagesConfig["960-gs"]=array(
	'css' => array('/fwassets/960.gs/code/css/reset.css', '/fwassets/960.gs/code/css/text.css', '/fwassets/960.gs/code/css/960.css')
);
