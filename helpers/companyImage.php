<?php
include_once(dirname(__FILE__) . "/companyImageUrl.php");
function blsfe_helper_companyImage($id, $x=0, $y=0, $params){
	$cached=$params["cached"];
	$image_url=blsfe_helper_companyImageUrl($id, $x, $y, $cached);

	$border=$params["border"];
	$class=$params["class"];
	$style=$params["style"];
	
	if ($image_url){
		$image_string= "<img src='$image_url' ";
		if($border){
			$image_string.=	"border='$border'";
		}
		if($class){
			$image_string.=	"class='$class'";
		}
		if($style){
			$image_string.=	"style='$style'";
		}
		$image_string.=">";
		return $image_string;
	}
	return "<img src='/fwassets/images/1x1.png' width='$x' height='$y'>";
	
	// print "<pre>" . print_r($image, true) . "</pre>";
};

