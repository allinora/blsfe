<?php
function smarty_function_packageCSS($params, &$smarty){
	$packages=$params["from"];
	$html="";
	if (!count($packages)){
		return;
	}
	foreach($packages as $p){
		if (count($p["css"])>0){
			foreach($p["css"] as $css){
				$html.='<link rel="stylesheet" type="text/css" href="'.$css.'" />' . "\n";
			}
		}
	}
	return $html;
};

