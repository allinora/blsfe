<?php
include_once(dirname(__FILE__) . "/userAvatarUrl.php");
function blsfe_helper_userAvatar($id, $x=0, $y=0, $params){
	$image_url=blsfe_helper_userAvatarUrl($id, $x, $y, $params);
	// print "avatar: url: $image_url<br>";
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

