<?php
function smarty_function_packageCSS($params, &$smarty){
	$packages=$params["from"];
	$html="";
	if (!count($packages)){
		return;
	}
	$seen=array(); // Array to not load the same file more than once
	foreach($packages as $p){
		if (count($p["css"])>0){
			foreach($p["css"] as $css){
				if ($seen[$css]){
					continue;
				}
				$seen[$css]++;
				
				if(substr($css, -3)=="less"){
					$html.='<link rel="stylesheet/less" type="text/css" href="'.$css.'" />' . "\n";
					
				}else {
					$html.='<link rel="stylesheet" type="text/css" href="'.$css.'" />' . "\n";
				};
				
				
			}
		}
	}
	return $html;
};

