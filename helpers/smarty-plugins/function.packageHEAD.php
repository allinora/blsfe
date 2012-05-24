<?php
function smarty_function_packageHEAD($params, &$smarty){
	$packages=$params["from"];
	$html="";
	if (!count($packages)){
		return;
	}
	$seen=array(); // Array to not load the same file more than once
	foreach($packages as $p){
		if (count($p["head"])>0){
			foreach($p["head"] as $head){
				if ($seen[$head]){
					continue;
				}
				$seen[$head]++;
				
					$html.=$head . "\n";
				
				
			}
		}
	}
	return $html;
};

