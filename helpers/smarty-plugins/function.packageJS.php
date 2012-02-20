<?php
function smarty_function_packageJS($params, &$smarty){
	$packages=$params["from"];
	$html="";
	if (!count($packages)){
		return;
	}
	foreach($packages as $p){
		if (count($p["js"])>0){
			foreach($p["js"] as $js){
				$html.='<script type="text/javascript" src="'.$js.'"></script>' . "\n";
			}
		}
	}
	return $html;
};

