<?php

function smarty_function_dealorplaceimage($params, &$smarty){
	//print "<pre>" . print_r($params, true) . "</pre>";exit;
	
    $deal_id=$params["deal_id"];
	$data=$params["data"];
    $x=$params["width"];
    $y=$params["height"];
	
	
	// return the regular image if there is something in data
	if (!function_exists("smarty_function_dealimage")){
		include_once(dirname(__FILE__) . "/function.dealimage.php");
	}
	if ($data["image_name"]){
		return smarty_function_dealimage($params, $smarty);
	}
	
	// print "<pre>" . print_r($data, true) . "</pre>";exit;
	
	
	$dealData=blcall("website/deals/getDeal", array("deal_id" => $deal_id));
	$placeData=blcall("website/places/getPlace", array("place_id" => $dealData["deal"]["place_id"]));
	if ($placeData["placeImages"][0]){
		$params["data"]["place_id"]=$dealData["deal"]["place_id"];
	    $params["data"]["image_name"]=$placeData["placeImages"][0]["image_name"];
		//print "<pre>" . print_r($smarty, true) . "</pre>";exit;
		if (!function_exists("smarty_function_placeimage")){
			include_once(dirname(__FILE__) . "/function.placeimage.php");
		}
		return smarty_function_placeimage($params, $smarty);
	}
	
    return "<img src='http://static.tipiness.com/staticmedia/images/spacer.gif'  width='$x' height='$y'>";
}

/* vim: set expandtab: */

