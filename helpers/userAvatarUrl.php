<?php

function blsfe_helper_userAvatarUrl($id, $x=0, $y=0){
	
		
		if (!$x){
			$x=0;
		}
		if (!$y){
			$y=0;
		}

		return "/core/userprofiles/avatar/image/$id/$x/$y/" . $image["name"];
	// print "<pre>" . print_r($image, true) . "</pre>";
};

