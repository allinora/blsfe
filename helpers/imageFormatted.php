<?php
function blsfe_helper_imageFormatted($image_url, $params){
	
	if ($params["width"]){
		$x=$params["width"];
	} else {
		$x=0;
	}

	if ($params["height"]){
		$y=$params["height"];
	} else {
		$y=0;
	}

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
		
		if ($params["maxwidth"] && $params["maxheight"]){
			$container="<div style='width: " . $params["maxwidth"] . "px; height: ".$params["maxheight"] . "px; overflow:hidden'>$image_string</div>";
			return $container;
		}
		return $image_string;
	}
	return "<img src='/fwassets/images/1x1.png' width='$x' height='$y'>";
	
	// print "<pre>" . print_r($image, true) . "</pre>";
};

