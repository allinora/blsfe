<?php

function smarty_function_media($params, &$smarty){
    $file=$params["file"];
    
    if ($params["type"]=="css"){
		$file = MEDIA_CSS_PREFIX . DS . $file;
	}
	else if ($params["type"]=="js"){
		$file = MEDIA_JS_PREFIX . DS . $file;
	}

	if (DEVELOPMENT_ENVIRONMENT){
		$file.="?" . date("Ymdhis");
	} else {
		$file.="?" . $smarty->getTemplateVars("deployment_timestamp"); //XXX FIXME: VAR NOT SET
	}
	
	if ($params["type"]=="css"){
		return "<link rel='stylesheet' type='text/css' href='$file' />";
	}
	
	if ($params["type"]=="js"){
		return "<script type='text/javascript' src='$file'></script>";
	}
	
}

/* vim: set expandtab: */

?>
